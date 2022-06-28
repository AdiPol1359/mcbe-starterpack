<?php

namespace Core\commands;

use pocketmine\command\{
	Command, CommandSender, ConsoleCommandSender
};
use pocketmine\block\Block;
use Core\api\ProtectAPI;
use Core\Main;
use pocketmine\level\Level;

class ProtectCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("protect", "Komenda protect", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

	    if(empty($args)) {
	        $sender->sendMessage("użycie:");
	        return;
        }

	    $nick = $sender->getName();

	    switch($args[0]) {

            case "whiteblock":
                if(!isset($args[1])) {
                    $sender->sendMessage("§8§l>§r §7Poprawne uzycie: /protect whiteblock §8(§4set§8|§4remove§8)");
                    return;
                }

                switch($args[1]) {
                    case "set":
                        if(isset(Main::$setWhiteBlock[$nick])) {
                            $sender->sendMessage("§8§l>§r §7Ustawianie bialych blokow zostalo wylaczone");
                            unset(Main::$setWhiteBlock[$nick]);
                            return;
                        }

                        if(!isset($args[2])) {
                            $sender->sendMessage("§8§l>§r §7Poprawne uzycie: /protect whiteblock set §8(§4teren§8)");
                            return;
                        }

                        if(!ProtectAPI::isTerrainExists($args[2])) {
                            $sender->sendMessage("§8§l>§r §7Teren o takiej nazwie nie istnieje!");
                            return;
                        }

                        Main::$setWhiteBlock[$nick] = $args[2];
                        $sender->sendMessage("§8§l>§r §7Kliknij na blok ktory chcesz dodac do bialej listy");
                    break;

                    case "remove":
                        if(isset(Main::$removeWhiteBlock[$nick])) {
                            $sender->sendMessage("§8§l>§r §7Usuwanie bialych blokow zostalo wylaczone");
                            unset(Main::$removeWhiteBlock[$nick]);
                            return;
                        }

                        if(!isset($args[2])) {
                            $sender->sendMessage("§8§l>§r §7Poprawne uzycie: /protect whiteblock remove §8(§4teren§8)");
                            return;
                        }

                        if(!ProtectAPI::isTerrainExists($args[2])) {
                            $sender->sendMessage("§8§l>§r §7Teren o takiej nazwie nie istnieje!");
                            return;
                        }

                        Main::$removeWhiteBlock[$nick] = $args[2];
                        $sender->sendMessage("§8§l>§r §7Kliknij na blok ktory chcesz usunac z bialej listy");
                    break;
                }
            break;

            case "list":
                if(!isset($args[1])) {
                    $sender->sendMessage("§8§l>§r §7Stworzone tereny: §4" . implode("§8, §4", ProtectAPI::getTerrains()));
                    return;
                }

                if(!ProtectAPI::isTerrainExists($args[1])) {
                    $sender->sendMessage("§8§l>§r §7Teren o takiej nazwie nie istnieje!");
                    return;
                }

                $sender->sendMessage("§8§l>§r §7Dodani gracze: §4" . implode("§8, §4", ProtectAPI::getPlayers($args[1])));
            break;

            case "create":
                if(isset($args[2])) {
                    $terrainName = $args[1];
                    $terrainSize = $args[2];

                    if(!is_numeric($args[2])) {
                        $sender->sendMessage("§8§l>§r §7Argument §42 §7musi byc numeryczny!");
                        return;
                    }

                    if(ProtectAPI::isTerrainExists($terrainName)) {
                        $sender->sendMessage("§8§l>§r §7Teren o takiej nazwie juz istnieje!");
                        return;
                    }

                    $arm = floor($terrainSize / 2);

                    $pos1 = $sender->add($arm, 0, $arm);
                    $pos1->setComponents($pos1->getX(), 0, $pos1->getZ());

                    $pos2 = $sender->add(-$arm, 0, -$arm);
                    $pos2->setComponents($pos2->getX(), Level::Y_MAX, $pos2->getZ());

                    ProtectAPI::createTerrain($terrainName, [$pos1, $pos2]);
                    $sender->sendMessage("§8§l>§r §7Teren §4{$terrainName} §7zostal utworzony");
                    return;
                }
                if(!isset(ProtectAPI::$data[$nick])) {
                    ProtectAPI::$data[$nick] = [];
                    $sender->sendMessage("§8§l>§r §7Wybierz §41 §7pozycje");
                } else {
                    unset(ProtectAPI::$data[$nick]);
                    $sender->sendMessage("§8§l>§r §7Tworzenie terenu zostalo anulowane");
                }
            break;

            case "delete":
                if(!isset($args[1])) {
                    $sender->sendMessage("§8§l>§r §7Poprawne uzycie: /protect delete §8(§4nazwa§8)");
                    return;
                }

                if(!ProtectAPI::isTerrainExists($args[1])) {
                    $sender->sendMessage("§8§l>§r §7Teren o takiej nazwie nie istnieje!");
                    return;
                }

                ProtectAPI::deleteTerrain($args[1]);
                $sender->sendMessage("§8§l>§r §7Teren §4{$args[1]} §7zostal usuniety");
            break;

            case "flag":
                if(!isset($args[2])) {
                    $sender->sendMessage("/protect flag (nazwa terenu) (list/set/remove)");
                    return;
                }

                if(!ProtectAPI::isTerrainExists($args[1])) {
                    $sender->sendMessage("§8§l>§r §7Teren o takiej nazwie nie istnieje!");
                    return;
                }


                switch($args[2]) {
                    case "list":
                        $flagsFormat = ["Flagi terenu §4{$args[1]}§7:"];

                        foreach(ProtectAPI::getFlags($args[1]) as $flag => $status)
                                $flagsFormat[] = "{$flag}: ".($status ? "§4ON" : "§cOFF");

                        $sender->sendMessage(Main::formatLines($flagsFormat));
                    break;

                    case "set":
                        if(!isset($args[3])) {
                            $sender->sendMessage("§8§l>§r §7Poprawne uzycie: /protect flag $args[1] set §8(§4flaga§8)");
                            return;
                        }

                        if(!ProtectAPI::isFlagExists($args[3])) {
                            $sender->sendMessage("§8§l>§r §7Ta flaga nie istnieje!");
                            return;
                        }

                        ProtectAPI::setFlag($args[1], $args[3]);
                        $sender->sendMessage("§8§l>§r §7Wlaczono flage §4{$args[3]} §7dla terenu §4{$args[1]}");
                    break;

                    case "remove":
                        if(!isset($args[3])) {
                            $sender->sendMessage("§8§l>§r §7Poprawne uzycie: /protect flag $args[1] remove §8(§4flaga§8)");
                            return;
                        }

                        if(!ProtectAPI::isFlagExists($args[3])) {
                            $sender->sendMessage("§8§l>§r §7Ta flaga nie istnieje!");
                            return;
                        }

                        ProtectAPI::setFlag($args[1], $args[3], false);
                        $sender->sendMessage("§8§l>§r §7Wylaczono flage §4{$args[3]} §7dla terenu §4{$args[1]}");
                    break;
                }
            break;

            case "add":
                if(!isset($args[2])) {
                    $sender->sendMessage("§8§l>§r §7Poprawne uzycie: /protect add §8(§4teren§8) (§4nick§8)");
                    return;
                }

                if(!ProtectAPI::isTerrainExists($args[1])) {
                    $sender->sendMessage("§8§l>§r §7Teren o takiej nazwie nie istnieje!");
                    return;
                }

                ProtectAPI::addPlayer($args[1], $args[2]);
                $sender->sendMessage("§8§l>§r §7Dodano gracza §4{$args[2]} §7do terenu §4{$args[1]}");
            break;

            case "remove":
                if(!isset($args[2])) {
                    $sender->sendMessage("§8§l>§r §7Poprawne uzycie: /protect remove §8(§4teren§8) (§4nick§8)");
                    return;
                }

                if(!ProtectAPI::isTerrainExists($args[1])) {
                    $sender->sendMessage("§8§l>§r §7Teren o takiej nazwie nie istnieje!");
                    return;
                }

                ProtectAPI::removePlayer($args[1], $args[2]);
                $sender->sendMessage("§8§l>§r §7Usunieto gracza §4{$args[2]} §7z terenu §4{$args[1]}");
            break;
            default:
                $sender->sendMessage("§8§l>§r §7Nieznany argument!");
        }
    }
}