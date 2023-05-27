<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\Main;
use core\managers\AdminManager;
use core\managers\nameTag\NameTagPlayerManager;
use core\utils\BroadcastUtil;
use core\utils\MessageUtil;
use core\utils\ParticleUtil;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\SoundUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\particle\HugeExplodeParticle;

class VanishCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("vanish", "", true, false, ["v"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $selected = $sender->getName();

        if(!empty($args))
            $selected = implode(" ", $args);

        $user = Main::getInstance()->getUserManager()->getUser($selected);

        if(!$user) {
            $sender->sendMessage(MessageUtil::format("Ten gracz nigdy nie gral na tym serwerze!"));
            return;
        }

        $vanish = $user->isVanished();

        $user->setVanish(!$vanish);

        if($selected === $sender->getName()) {

            BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($vanish, $sender) : void {
                if(PermissionUtil::has($onlinePlayer, Settings::$PERMISSION_TAG."vanish.see"))
                    return;

                if(!$vanish)
                    $onlinePlayer->hidePlayer($sender);
                else
                    $onlinePlayer->showPlayer($sender);
            });

            ParticleUtil::spawnParticle([$sender], new HugeExplodeParticle());
            SoundUtil::addSound([$sender], $sender->getPosition(), "random.explode");
            $sender->sendMessage(MessageUtil::format((!$vanish ? "Wlaczyles" : "Wylaczyles") . " sobie vanisha"));
            AdminManager::sendMessage($sender, $sender->getName() . " " . (!$vanish ? "wlaczyl" : "wylaczyl") . " sobie vanisha");

            NameTagPlayerManager::updatePlayersAround($sender);
        } else {

            $selectedPlayer = Server::getInstance()->getPlayerExact($user->getName());

            if($selectedPlayer) {

                if($vanish) {
                    BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($selectedPlayer) : void {
                        if(PermissionUtil::has($onlinePlayer, Settings::$PERMISSION_TAG."vanish.see"))
                            return;

                        $onlinePlayer->hidePlayer($selectedPlayer);
                    });
                } else {
                    BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($sender) : void {
                        $onlinePlayer->showPlayer($sender);
                    });
                }
            }

            ParticleUtil::spawnParticle([$selectedPlayer], new HugeExplodeParticle());
            SoundUtil::addSound([$selectedPlayer], $selectedPlayer->getPosition()(), "random.explode");
            $sender->sendMessage(MessageUtil::format((!$vanish ? "Wlaczyles" : "Wylaczyles") . " vanisa graczowi §e" . $selected));
            AdminManager::sendMessage($sender, $sender->getName() . " " . (!$vanish ? "wlaczyl" : "wylaczyl") . " vanisha graczowi " . $selected);

            NameTagPlayerManager::updatePlayersAround($selectedPlayer);
        }
    }
}