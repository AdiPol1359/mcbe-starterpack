<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\Main;
use core\util\utils\MessageUtil;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Player;
use pocketmine\command\CommandSender;
use core\permission\group\GroupManager;

class PexCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("pex", "Pex Command", true, true, "Komenda pex sluzy do zarzadzania rangami");

        $parameters = [
            0 => [
                $this->commandParameter("pexHelpOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexHelpOption", ["help"])
            ],

            1 => [
                $this->commandParameter("pexReloadOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexReloadOption", ["reload"])
            ],

            2 => [
                $this->commandParameter("pexUserOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserOption", ["user"])
            ],

            3 => [
                $this->commandParameter("pexUserOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUsernickOption", ["user"]),
                $this->commandParameter("pexUserNickOption", AvailableCommandsPacket::ARG_TYPE_TARGET, false)
            ],

            4 => [
                $this->commandParameter("pexUserOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUsernickOption", ["user"]),
                $this->commandParameter("pexUserNickOption", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("pexUserListOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserListOption", ["list"]),
            ],

            5 => [
                $this->commandParameter("pexUserOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUsernickOption", ["user"]),
                $this->commandParameter("pexUserNickOption", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("pexUserDeleteOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserDeleteOption", ["delete"]),
            ],

            6 => [
                $this->commandParameter("pexUserOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUsernickOption", ["user"]),
                $this->commandParameter("pexUserNickOption", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("pexUserAddOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserAddOption", ["add"]),
                $this->commandParameter("pexUserAddPermissionOption", AvailableCommandsPacket::ARG_TYPE_STRING, false),
                $this->commandParameter("pexUserAddPermissionTimeOption", AvailableCommandsPacket::ARG_TYPE_STRING, true),
            ],

            7 => [
                $this->commandParameter("pexUserOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUsernickOption", ["user"]),
                $this->commandParameter("pexUserNickOption", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("pexUserRemoveOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserRemoveOption", ["remove"]),
                $this->commandParameter("pexUserRemovePermissionOption", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ],

            8 => [
                $this->commandParameter("pexUserOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUsernickOption", ["user"]),
                $this->commandParameter("pexUserNickOption", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("pexUserGroupChooseOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupChooseOption", ["group"]),
                $this->commandParameter("pexUserGroupSetOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupSetOption", ["set"]),
                $this->commandParameter("pexUserGroupOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupOption", $this->getGroups()),
                $this->commandParameter("pexUserGroupSetGroupOption", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ],

            9 => [
                $this->commandParameter("pexUserOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUsernickOption", ["user"]),
                $this->commandParameter("pexUserNickOption", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("pexUserGroupChooseOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupChooseOption", ["group"]),
                $this->commandParameter("pexUserGroupAddOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupAddOption", ["add"]),
                $this->commandParameter("pexUserGroupOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupOption", $this->getGroups()),
                $this->commandParameter("pexUserGroupAddTimeOption", AvailableCommandsPacket::ARG_TYPE_STRING, true),
            ],

            10 => [
                $this->commandParameter("pexUserOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUsernickOption", ["user"]),
                $this->commandParameter("pexUserNickOption", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("pexUserGroupChooseOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupChooseOption", ["group"]),
                $this->commandParameter("pexUserGroupRemoveOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupRemoveOption", ["remove"]),
                $this->commandParameter("pexUserGroupOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupOption", $this->getGroups()),
            ],

        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        $groupManager = Main::getInstance()->getGroupManager();

        if(empty($args) || isset($args[0]) && $args[0] == "help") {

            $player->sendMessage(MessageUtil::customFormat([
                "",
                "§l§9OGOLNE",
                "",
                "§l§8» §r§8/§9pex help §8-§7 Wyswietla komendy do pexa",
                "§l§8» §r§8/§9pex reload §8-§7 Odswieza config",
                "",
                "§l§9UZYTKOWNICY",
                "",
                "§l§8» §r§8/§9pex user §8-§7 Pokazuje wszystkich graczy w bazie danych",
                "§l§8» §r§8/§9pex user (nick) §8-§7 Pokazuje liste rang gracza",
                "§l§8» §r§8/§9pex user (nick) list §8-§7 Pokazuje liste permisji gracza",
                "§l§8» §r§8/§9pex user (nick) delete  §8-§7 Usuwa dane gracza",
                "§l§8» §r§8/§9pex user (nick) add (permission) {time[s/m/h/d]} §8-§7 Dodaje permisje graczowi",
                "§l§8» §r§8/§9pex user (nick) remove (permission) §8-§7 Usuwa permisje graczowi",
                "§l§8» §r§8/§9pex user (nick) group set (group) §8-§7 Ustawia range graczowi",
                "§l§8» §r§8/§9pex user (nick) group add (group) {time[s/m/h/d]} §8-§7 Dodaje range graczowi",
                "§l§8» §r§8/§9pex user (nick) group remove (group) §8-§7 Usuwa range graczowi",
                "",
                "§l§9RANGI",
                "",
                "§l§8» §r§8/§9pex group §8-§7 Pokazuje wszystkie zarejestrowane rangi",
                "§l§8» §r§8/§9pex group (group) list §8-§7 Pokazuje wszystkie permisje rangi",
                "§l§8» §r§8/§9pex group (group) players §8-§7 Pokazuje graczy z ta ranga",
                "§l§8» §r§8/§9pex group (group) delete §8-§7 Usuwa range",
                "§l§8» §r§8/§9pex group (group) add (permission) §8-§7 Dodaje permisje randze",
                "§l§8» §r§8/§9pex group (group) remove (permission) §8-§7 Usuwa permisje randze",

            ], "DarkMoonPE.PL", "§r§7"));
            return;
        }

        switch($args[0]) {

            case "reload":

                Main::getGroupManager()->reload();

                foreach(Main::getGroupManager()->getAllGroups() as $group)
                    $group->updatePlayersPermissions();

                $player->sendMessage(MessageUtil::format("Odswiezono config pluginu!"));
                break;

            case "set":

                if(!isset($args[1])) {
                    $player->sendMessage(MessageUtil::format("Doszlo do bledu! sprawdz czy dobrze wpisales komende!"));
                    return;
                }
                switch($args[1]) {
                    case "default":
                        switch($args[2]) {
                            case "group":
                                if(!isset($args[3])) {
                                    $player->sendMessage($this->correctUse($this->getCommandLabel(), [["set"], ["default"], ["group"], ["ranga"]]));
                                    return;
                                }

                                if(!$groupManager->isGroupExists($args[3])) {
                                    $player->sendMessage(MessageUtil::format("Nie znaleziono takiej rangi"));
                                    return;
                                }

                                $groupManager->setDefaultGroup($groupManager->getGroup($args[3]));
                                $player->sendMessage(MessageUtil::format("Ustawiono domyslna range na §l§9".$args[3]."§r§7!"));
                                break;
                            default:
                                $player->sendMessage(MessageUtil::format("Doszlo do bledu! sprawdz czy dobrze wpisales komende!"));
                        }
                        break;
                    default:
                        $player->sendMessage(MessageUtil::format("Doszlo do bledu! sprawdz czy dobrze wpisales komende!"));
                }
                break;

            case "group":

                if(!isset($args[1])) {
                    $player->sendMessage("§7Zarejestrowane rangi: ");
                    foreach($groupManager->getAllGroups() as $group) {

                        $parentsFormat = function($group) : string {
                            $format = "";

                            foreach($group->getParents() as $g)
                                $format .= $g->getName() . ", ";

                            if($format != "")
                                $format = substr($format, 0, strlen($format) - 2);

                            return $format;
                        };

                        $player->sendMessage(" §7{$group->getName()} #{$group->getRank()} §9[{$parentsFormat($group)}]");
                    }
                    return;
                }

                if(!isset($args[2])) {
                    $player->sendMessage(MessageUtil::format("Doszlo do bledu! sprawdz czy dobrze wpisales komende!"));
                    return;
                }

                switch($args[2]) {
                    case "list":
                        if(!$groupManager->isGroupExists($args[1])) {
                            $player->sendMessage(MessageUtil::format("Ta ranga nie istnieje!"));
                            return;
                        }

                        $group = $groupManager->getGroup($args[1]);

                        $player->sendMessage("§7Permisje rangi §9§l{$args[1]}§r§7:");


                        foreach($group->getPermissions() as $permission) {
                            foreach($group->getParents() as $parentGroup) {
                                if($parentGroup->hasPermission($permission)) {
                                    $player->sendMessage(" §7{$permission} ({$parentGroup->getName()})");
                                    continue;
                                }
                                $player->sendMessage(" §7{$permission} (posiadane)");
                            }
                        }
                        break;

                    case "players":
                        if(!$groupManager->isGroupExists($args[1])) {
                            $player->sendMessage(MessageUtil::format("Ta ranga nie istnieje!"));
                            return;
                        }

                        $player->sendMessage("§7Gracze rangi §l§9{$args[1]}§r§7:");
                        foreach($groupManager->getGroup($args[1])->getPlayers() as $nick)
                            $player->sendMessage(" §7{$nick}");
                        break;

                    case "delete":
                        if(!$groupManager->isGroupExists($args[1])) {
                            $player->sendMessage(MessageUtil::format("Ta ranga nie istnieje!"));
                            return;
                        }

                        $groupManager->getGroup($args[1])->delete();
                        $player->sendMessage(MessageUtil::format("Usunieto range §l§9{$args[1]}§r§7!"));
                        break;

                    case "add":
                        if(!$groupManager->isGroupExists($args[1])) {
                            $player->sendMessage(MessageUtil::format("Ta ranga nie istnieje!"));
                            return;
                        }

                        if(!isset($args[3])) {
                            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["group"], [$args[1]], ["add"], ["permisja"]]));
                            return;
                        }
                        $perm = strtolower($args[3]);

                        $groupManager->getGroup($args[1])->addPermission($perm);
                        $player->sendMessage(MessageUtil::format("Dodano permisje §l§9{$perm}§r§7 do rangi §l§9{$args[1]}§r§7!"));
                        break;

                    case "remove":
                        if(!$groupManager->isGroupExists($args[1])) {
                            $player->sendMessage(MessageUtil::format("Ta ranga nie istnieje!"));
                            return;
                        }

                        if(!isset($args[3])) {
                            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["group"], [$args[1]], ["remove"], ["permisja"]]));
                            return;
                        }
                        $perm = strtolower($args[3]);

                        $groupManager->getGroup($args[1])->removePermission($perm);
                        $player->sendMessage(MessageUtil::format("Usunieto permisje §l§9{$perm}§r§7 randze §l§9{$args[1]}§r§7!"));
                        break;
                    default:
                        $player->sendMessage(MessageUtil::format("Doszlo do bledu! sprawdz czy dobrze wpisales komende!"));
                }
                break;

            case "user":

                if(!isset($args[1])) {
                    $player->sendMessage("§7Zarejestrowani uzytkownicy:");
                    foreach(Main::getProvider()->getAllUsers() as $nick)
                        $player->sendMessage(" §7{$nick}");

                    return;
                }

                if(!$groupManager->userExists($args[1])) {
                    $player->sendMessage(MessageUtil::format("Nie znaleziono uzytkownika!"));
                    return;
                }

                if(!isset($args[2])) {
                    $player->sendMessage("Rangi gracza §l§9{$args[1]}§r§7:");
                    foreach($groupManager->getAllGroups() as $group) {
                        if(!$groupManager->getPlayer($args[1])->hasGroup($group))
                            $player->sendMessage("§7Nie posiada §9{$group->getName()}§7!");
                        else {
                            $expiryTime = $groupManager->getPlayer($args[1])->getGroupExpiry($group);
                            $expiryFormat = GroupManager::expiryFormat($expiryTime);

                            $player->sendMessage("§7Ranga §l§9{$group->getName()}§r§7: §7" . ($expiryTime == null ? "§l§9na zawsze" : "§l§9{$expiryFormat['days']}§r§7d §l§9{$expiryFormat['hours']}§r§7h §l§9{$expiryFormat['minutes']}§r§7m §l§9{$expiryFormat['seconds']}§r§7s"));
                        }
                    }
                    return;
                }

                switch($args[2]) {

                    case "list":
                        $player->sendMessage("§7Permisje gracza §l§9{$args[1]}§r§7:");
                        foreach($groupManager->getPlayer($args[1])->getPermissions() as $permission)
                            $player->sendMessage(" §7{$permission}");
                        break;

                    case "delete":
                        $groupManager->getPlayer($args[1])->delete();
                        $player->sendMessage("Usunieto §l§9{$args[1]}§r§7!");
                        break;

                    case "add":
                        if(!isset($args[3])) {
                            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["user"], [$args[1]], ["add"], ["permisja"], ["{time[s/m/h/d]}"]]));
                            return;
                        }
                        $time = null;

                        if(isset($args[4])) {
                            if(strpos($args[4], "d"))
                                $time = intval(explode("d", $args[4])[0]) * 86400;

                            if(strpos($args[4], "h"))
                                $time = intval(explode("h", $args[4])[0]) * 3600;

                            if(strpos($args[4], "m"))
                                $time = intval(explode("h", $args[4])[0]) * 60;

                            if(strpos($args[4], "s"))
                                $time = intval(explode("s", $args[4])[0]);
                            $groupManager->getPlayer($args[1])->addPermission($args[3], $time);
                        } else
                            $groupManager->getPlayer($args[1])->addPermission($args[3]);

                        $player->sendMessage(MessageUtil::format("Permisja §l§9{$args[3]}§r§7 dodana!" . ($time == null ? "" : " na §l§9{$args[4]}§r§7!")));
                        break;

                    case "remove":
                        if(!isset($args[3])) {
                            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["user"], [$args[1]], ["remove"], ["permisja"]]));
                            return;
                        }

                        $groupManager->getPlayer($args[1])->removePermission($args[3]);
                        $player->sendMessage(MessageUtil::format("Permisja §l§9{$args[3]}§r§7! usunieta!"));
                        break;

                    case "group":
                        if(!isset($args[4])) {
                            $player->sendMessage(MessageUtil::format("Doszlo do bledu! sprawdz czy dobrze wpisales komende!"));
                            return;
                        }

                        if(!$groupManager->isGroupExists($args[4])) {
                            $player->sendMessage(MessageUtil::format("Ta ranga nie istnieje!"));
                            return;
                        }

                        switch($args[3]) {
                            case "add":
                                $playerManager = $groupManager->getPlayer($args[1]);

                                $p = $playerManager->getPlayer();
                                $nick = $player instanceof Player ? $p->getName() : $args[1];

                                $time = null;

                                if(isset($args[5])) {
                                    if(strpos($args[5], "d"))
                                        $time = intval(explode("d", $args[5])[0]) * 86400;

                                    if(strpos($args[5], "h"))
                                        $time = intval(explode("h", $args[5])[0]) * 3600;

                                    if(strpos($args[5], "m"))
                                        $time = intval(explode("h", $args[5])[0]) * 60;

                                    if(strpos($args[5], "s"))
                                        $time = intval(explode("s", $args[5])[0]);
                                    $playerManager->addGroup($groupManager->getGroup($args[4]), $time);
                                } else
                                    $playerManager->addGroup($groupManager->getGroup($args[4]));

                                $player->sendMessage(MessageUtil::format("Uzytkownikowi §l§9{$nick} §r§7dodano range §l§9{$args[4]}§r§7" . ($time == null ? "" : " na §9{$args[5]}")));
                                break;

                            case "remove":
                                $playerManager = $groupManager->getPlayer($args[1]);

                                $p = $playerManager->getPlayer();
                                $nick = $player instanceof Player ? $p->getName() : $args[1];

                                $playerManager->removeGroup($groupManager->getGroup($args[4]));

                                $player->sendMessage(MessageUtil::format("Uzytkownikowi §l§9{$nick} §r§7usunieto range §l§9{$args[4]}§r§7!"));
                                break;

                            case "set":
                                $playerManager = $groupManager->getPlayer($args[1]);

                                $p = $playerManager->getPlayer();
                                $nick = $player instanceof Player ? $p->getName() : $args[1];

                                $time = null;

                                if(isset($args[5])) {
                                    if(strpos($args[5], "d"))
                                        $time = intval(explode("d", $args[5])[0]) * 86400;

                                    if(strpos($args[5], "h"))
                                        $time = intval(explode("h", $args[5])[0]) * 3600;

                                    if(strpos($args[5], "m"))
                                        $time = intval(explode("h", $args[5])[0]) * 60;

                                    if(strpos($args[5], "s"))
                                        $time = intval(explode("s", $args[5])[0]);
                                    $playerManager->setGroup($groupManager->getGroup($args[4]), $time);
                                } else
                                    $playerManager->setGroup($groupManager->getGroup($args[4]));

                                $player->sendMessage(MessageUtil::format("Uzytkownikowi §l§9{$nick} §r§7ustawiono range na §l§9{$args[4]}§r§7" . ($time == null ? "" : " na §l§9{$args[5]}§r§7!")));

                                break;
                            default:
                                $player->sendMessage(MessageUtil::format("Doszlo do bledu! sprawdz czy dobrze wpisales komende!"));
                        }
                        break;

                    default:
                        $player->sendMessage(MessageUtil::format("Doszlo do bledu! sprawdz czy dobrze wpisales komende!"));
                }
                break;
            default:
                $player->sendMessage(MessageUtil::format("Doszlo do bledu! sprawdz czy dobrze wpisales komende!"));
        }
    }

    public function getGroups() : array {

        $groups = [];

        foreach((new GroupManager())->getAllGroups() as $group)
            $groups[] = strtolower($group->getName());

        return $groups;
    }
}