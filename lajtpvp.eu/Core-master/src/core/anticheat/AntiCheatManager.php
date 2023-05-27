<?php

declare(strict_types=1);

namespace core\anticheat;

use core\anticheat\modules\AntiBlinkModule;
use core\anticheat\modules\AntiNoclipModule;
use core\anticheat\modules\AntiReachModule;
use core\anticheat\modules\AntiSpeedMineModule;
use core\anticheat\modules\AntiSpeedModule;
use core\anticheat\modules\AntiTeleportHackModule;
use core\anticheat\modules\data\AttackPlayerCalculator;
use core\anticheat\modules\data\DistPlayerCalculator;
use core\Main;
use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;
use pocketmine\Server;

class AntiCheatManager {

    /** @var BaseModule[] */
    private array $anticheats = [];

    public function __construct(private Main $plugin) {}

    public function banPlayer(Player $player) : void {
        $this->plugin->getBanManager()->setBan($player->getName(), $player->getNetworkSession()->getIp(), $player->getPlayerInfo()->getExtraData()["DeviceId"], "ANTI-CHEAT", "Cheat", (time() + (86400 * 7)));
    }

    public function init() : void {

        $modules = [
            new AntiSpeedMineModule(),
            new AntiSpeedModule(),
            new AntiTeleportHackModule(),
            new AntiNoclipModule(),
            new AntiReachModule(),
            new AntiBlinkModule(),
            //new AntiAirJumpModule(),
            new DistPlayerCalculator(),
            new AttackPlayerCalculator(),
        ];

        $this->anticheats = $modules;

        foreach($modules as $module)
            Server::getInstance()->getPluginManager()->registerEvents($module, Main::getInstance());
    }

    #[Pure] public function getAntiCheatByName(string $name) : ?BaseModule {
        foreach($this->anticheats as $anticheat) {
            if($anticheat->getModuleName() === $name)
                return $anticheat;
        }

        return null;
    }
}