<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\form\forms\terrain\MainTerrainForm;
use core\manager\managers\terrain\TerrainManager;
use core\user\UserManager;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

class TerrainCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("terrain", "Terrain Command", true, false, "Komenda sluzy do zarzadzanie terenem", ["protect"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        if(empty($args)) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["create", "remove", "flag", "list"]]));
            return;
        }

        $user = UserManager::getUser($player->getName());

        switch($args[0]) {
            case "create":
                if(!isset($args[1])) {
                    $player->sendMessage($this->correctUse($this->getCommandLabel(), [["create"], ["nazwa"]]));
                    return;
                }

                if($user->getPos1() === null || $user->getPos2() === null) {
                    $player->sendMessage(MessageUtil::format("Musisz zaznaczyc wszystkie pozycje"));
                    return;
                }

                if(TerrainManager::terrainExists($args[1])) {
                    $player->sendMessage(MessageUtil::format("Teren o podanej nazwie juz istnieje!"));
                    return;
                }

                TerrainManager::createTerrain($args[1], 1, $user->getPos1(), $user->getPos2());
                $player->sendMessage(MessageUtil::format("Teren zostal poprawnie utworzony!"));
                $user->setPos1(null);
                $user->setPos2(null);

                break;

            case "flag":

                if(!isset($args[1])) {
                    $player->sendMessage($this->correctUse($this->getCommandLabel(), [["flag"], ["nazwa"]]));
                    return;
                }

                if(!TerrainManager::terrainExists($args[1])) {
                    $player->sendMessage(MessageUtil::format("Teren o podanej nazwie nie istnieje!"));
                    return;
                }

                $player->sendForm(new MainTerrainForm(TerrainManager::getTerrainByName($args[1])));

                break;

            case "remove":

                if(!isset($args[1])) {
                    $player->sendMessage($this->correctUse($this->getCommandLabel(), [["remove"], ["nazwa"]]));
                    return;
                }

                if(!TerrainManager::terrainExists($args[1])) {
                    $player->sendMessage(MessageUtil::format("Teren o podanej nazwie nie istnieje!"));
                    return;
                }

                TerrainManager::removeTerrain($args[1]);
                $player->sendMessage(MessageUtil::format("Poprawnie usunieto teren!"));

                break;

            case "list":
                $terrains = [];

                foreach(TerrainManager::getTerrains() as $name => $terrain)
                    $terrains[] = $name;

                $player->sendMessage(MessageUtil::format("Twoje tereny: §l§8(§9".count($terrains)."§8)§r§7: §l§9".implode("§r§7, §l§9", $terrains)));
                break;

            case "test":
                $terrains = [];

                foreach(TerrainManager::getTerrainsFromPos($player->asPosition()) as $terrain)
                    $terrains[] = $terrain->getName();

                $player->sendMessage(MessageUtil::format("Tereny w tym miejscu: §l§8(§9".count($terrains)."§8)§r§7: §l§9".implode("§r§7, §l§9", $terrains)));
                break;

            default:
                $player->sendMessage($this->correctUse($this->getCommandLabel(), [["create", "remove", "flag", "list"]]));
                break;
        }
    }
}