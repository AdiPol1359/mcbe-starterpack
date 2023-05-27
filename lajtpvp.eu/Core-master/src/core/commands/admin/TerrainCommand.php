<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\forms\MainTerrainForm;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class TerrainCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("terrain", "", true, false, ["protect"]);

        $parameters = [
            0 => [
                $this->commandParameter("terrainOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "terrainOptions", ["create", "remove", "flag", "list"])
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(empty($args)) {
            $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Tworzy nowy teren" => ["create", "§8(§enazwa§8)"], "Usuwa teren" => ["remove", "§8(§enazwa§8)"], "Otwiera menu flag terenu" => ["flag"], "Pokazuje liste terenow" => ["list"]]));
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($sender->getName());
        $terrainManager = $user->getTerrainManager();

        switch($args[0]) {
            case "create":
                if(!isset($args[1])) {
                    $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Tworzy nowy teren" => ["create", "§8(§enazwa§8)"], "Usuwa teren" => ["remove", "§8(§enazwa§8)"], "Otwiera menu flag terenu" => ["flag"], "Pokazuje liste terenow" => ["list"]]));
                    return;
                }

                if($terrainManager->getPos1() === null || $terrainManager->getPos2() === null) {
                    $sender->sendMessage(MessageUtil::format("Musisz zaznaczyc wszystkie pozycje"));
                    return;
                }

                if(Main::getInstance()->getTerrainManager()->terrainExists($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Teren o podanej nazwie juz istnieje!"));
                    return;
                }

                Main::getInstance()->getTerrainManager()->createTerrain($args[1], 1, $terrainManager->getPos1(), $terrainManager->getPos2());
                $sender->sendMessage(MessageUtil::format("Teren zostal poprawnie utworzony!"));
                $terrainManager->setPos1(null);
                $terrainManager->setPos2(null);

                break;

            case "flag":

                if(!isset($args[1])) {
                    $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Tworzy nowy teren" => ["create", "§8(§enazwa§8)"], "Usuwa teren" => ["remove", "§8(§enazwa§8)"], "Otwiera menu flag terenu" => ["flag"], "Pokazuje liste terenow" => ["list"]]));
                    return;
                }

                if(!Main::getInstance()->getTerrainManager()->terrainExists($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Teren o podanej nazwie nie istnieje!"));
                    return;
                }

                $sender->sendForm(new MainTerrainForm(Main::getInstance()->getTerrainManager()->getTerrainByName($args[1])));

                break;

            case "remove":

                if(!isset($args[1])) {
                    $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Tworzy nowy teren" => ["create", "§8(§enazwa§8)"], "Usuwa teren" => ["remove", "§8(§enazwa§8)"], "Otwiera menu flag terenu" => ["flag"], "Pokazuje liste terenow" => ["list"]]));
                    return;
                }

                if(!Main::getInstance()->getTerrainManager()->terrainExists($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Teren o podanej nazwie nie istnieje!"));
                    return;
                }

                Main::getInstance()->getTerrainManager()->removeTerrain($args[1]);
                $sender->sendMessage(MessageUtil::format("Poprawnie usunieto teren!"));

                break;

            case "list":
                $terrains = [];

                foreach(Main::getInstance()->getTerrainManager()->getTerrains() as $name => $terrain)
                    $terrains[] = $name;

                $sender->sendMessage(MessageUtil::format("Twoje tereny: §8(§e".count($terrains)."§8)§r§7: §e".implode("§r§7, §e", $terrains)));
                break;

            case "test":
                $terrains = [];

                foreach(Main::getInstance()->getTerrainManager()->getTerrainsFromPos($sender->getPosition()) as $terrain)
                    $terrains[] = $terrain->getName();

                $sender->sendMessage(MessageUtil::format("Tereny w tym miejscu: §8(§e".count($terrains)."§8)§r§7: §e".implode("§r§7, §e", $terrains)));
                break;

            default:
                $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Tworzy nowy teren" => ["create", "§8(§enazwa§8)"], "Usuwa teren" => ["remove", "§8(§enazwa§8)"], "Otwiera menu flag terenu" => ["flag"], "Pokazuje liste terenow" => ["list"]]));
                break;
        }
    }
}