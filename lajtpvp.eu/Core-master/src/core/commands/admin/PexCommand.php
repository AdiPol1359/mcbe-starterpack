<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\Main;
use core\users\CorePlayer;
use core\utils\MessageUtil;
use core\utils\TimeUtil;
use JetBrains\PhpStorm\Pure;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\command\CommandSender;

class PexCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("pex", "", true, true);

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
                $this->commandParameter("pexUserAddOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserAddOption", ["add"]),
                $this->commandParameter("pexUserAddPermissionOption", AvailableCommandsPacket::ARG_TYPE_STRING, false),
                $this->commandParameter("pexUserAddPermissionTimeOption", AvailableCommandsPacket::ARG_TYPE_STRING, true),
            ],

            6 => [
                $this->commandParameter("pexUserOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUsernickOption", ["user"]),
                $this->commandParameter("pexUserNickOption", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("pexUserRemoveOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserRemoveOption", ["remove"]),
                $this->commandParameter("pexUserRemovePermissionOption", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ],

            7 => [
                $this->commandParameter("pexUserOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUsernickOption", ["user"]),
                $this->commandParameter("pexUserNickOption", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("pexUserGroupChooseOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupChooseOption", ["group"]),
                $this->commandParameter("pexUserGroupSetOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupSetOption", ["set"]),
                $this->commandParameter("pexUserGroupOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupOption", $this->getGroups()),
                $this->commandParameter("pexUserGroupSetGroupOption", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ],

            8 => [
                $this->commandParameter("pexUserOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUsernickOption", ["user"]),
                $this->commandParameter("pexUserNickOption", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("pexUserGroupChooseOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupChooseOption", ["group"]),
                $this->commandParameter("pexUserGroupAddOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupAddOption", ["add"]),
                $this->commandParameter("pexUserGroupOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupOption", $this->getGroups()),
                $this->commandParameter("pexUserGroupAddTimeOption", AvailableCommandsPacket::ARG_TYPE_STRING, true),
            ],

            9 => [
                $this->commandParameter("pexUserOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUsernickOption", ["user"]),
                $this->commandParameter("pexUserNickOption", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("pexUserGroupChooseOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupChooseOption", ["group"]),
                $this->commandParameter("pexUserGroupRemoveOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupRemoveOption", ["remove"]),
                $this->commandParameter("pexUserGroupOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "pexUserGroupOption", $this->getGroups()),
            ],

        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args) || isset($args[0]) && $args[0] == "help") {

            $sender->sendMessage(MessageUtil::formatLines([
                "",
                "§l§eOGOLNE",
                "",
                "§8/§epex help §8-§7 Wyswietla komendy do pexa",
                "§8/§epex reload §8-§7 Odswieza config",
                "",
                "§l§eUZYTKOWNICY",
                "",
                "§8/§epex user §8-§7 Pokazuje wszystkich graczy w bazie danych",
                "§8/§epex user (nick) §8-§7 Pokazuje liste rang gracza",
                "§8/§epex user (nick) list §8-§7 Pokazuje liste permisji gracza",
                "§8/§epex user (nick) add (permission) {time[s/m/h/d]} §8-§7 Dodaje permisje graczowi",
                "§8/§epex user (nick) remove (permission) §8-§7 Usuwa permisje graczowi",
                "§8/§epex user (nick) group set (group) §8-§7 Ustawia range graczowi",
                "§8/§epex user (nick) group add (group) {time[s/m/h/d]} §8-§7 Dodaje range graczowi",
                "§8/§epex user (nick) group remove (group) §8-§7 Usuwa range graczowi",
                "",
                "§l§eRANGI",
                "",
                "§8/§epex group §8-§7 Pokazuje wszystkie zarejestrowane rangi",
                "§8/§epex group (group) list §8-§7 Pokazuje wszystkie permisje rangi",
                "§8/§epex group (group) players §8-§7 Pokazuje graczy z ta ranga",
                "§8/§epex group (group) add (permission) §8-§7 Dodaje permisje randze",
                "§8/§epex group (group) remove (permission) §8-§7 Usuwa permisje randze",

            ]));
            return;
        }

        switch($args[0]) {

            case "reload":
                Main::getInstance()->getGroupManager()->reload();
                Main::getInstance()->getGroupManager()->reload();

                $sender->sendMessage(MessageUtil::format("Odswiezono config pluginu!"));
                break;

            case "group":

                if(!isset($args[1])) {
                    $sender->sendMessage("§7Zarejestrowane rangi: ");
                    foreach(Main::getInstance()->getGroupManager()->getGroups() as $group) {

                        $parentsFormat = function($group) : string {
                            $format = "";

                            foreach($group->getParents() as $g)
                                $format .= $g . ", ";

                            if($format != "")
                                $format = substr($format, 0, strlen($format) - 2);

                            return $format;
                        };

                        $sender->sendMessage(" §7{$group->getGroupName()} #{$group->getRank()} §e[{$parentsFormat($group)}]");
                    }
                    return;
                }

                if(!isset($args[2])) {
                    $sender->sendMessage(MessageUtil::format("Doszlo do bledu! sprawdz czy dobrze wpisales komende!"));
                    return;
                }

                if($args[2] == "list") {
                    if(!($group = Main::getInstance()->getGroupManager()->getGroupByName($args[1]))) {
                        $sender->sendMessage(MessageUtil::format("Ta ranga nie istnieje!"));
                        return;
                    }

                    $sender->sendMessage("§7Permisje rangi §e" . $group->getGroupName() . "§r§7:");


                    foreach($group->getPermissions() as $permission) {
                        foreach($group->getParents() as $parentGroup) {
                            $parentG = Main::getInstance()->getGroupManager()->getGroupByName($parentGroup);

                            if($parentG === null) {
                                continue;
                            }

                            if($parentG->hasPermission($permission)) {
                                $sender->sendMessage(" §7{$permission} ({$parentGroup->getName()})");
                                continue;
                            }
                            $sender->sendMessage(" §7{$permission} (posiadane)");
                        }
                    }
                }
                break;

            case "user":

                if(!isset($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Nie podales nazwy uzytkownika!"));
                    return;
                }

                if(!($senderGroup = Main::getInstance()->getPlayerGroupManager()->getPlayer($args[1]))) {
                    $sender->sendMessage(MessageUtil::format("Nie znaleziono uzytkownika!"));
                    return;
                }

                if(!isset($args[2])) {
                    $sender->sendMessage(MessageUtil::format("Rangi gracza §e{$args[1]}§r§7:"));
                    foreach(Main::getInstance()->getGroupManager()->getGroups() as $group) {
                        if(!$senderGroup->hasGroup($group->getGroupName()))
                            $sender->sendMessage("§c{$group->getGroupName()}");
                        else {
                            $expiryTime = $senderGroup->getGroupExpire($group->getGroupName());
                            $sender->sendMessage("§a{$group->getGroupName()} §7" . ($expiryTime === -1 ? "§ana zawsze" : TimeUtil::convertIntToStringTime((time() - $expiryTime), "§a", "§7", true, false)));
                        }
                    }
                    return;
                }

                switch($args[2]) {

                    case "list":
                        $sender->sendMessage("§7Permisje gracza §e{$args[1]}§r§7:");
                        foreach($senderGroup->getPermissions() as $permission => $expiryDate)
                            $sender->sendMessage("§e{$permission}§7: ".($expiryDate === -1 ? "§ena zawsze" : TimeUtil::convertIntToStringTime((time() - $expiryDate), "§e", "§7", true, false)));

                        break;

                    case "add":
                        if(!isset($args[3])) {
                            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["user"], [$args[1]], ["add"], ["permisja"], ["{time[s/m/h/d]}"]]));
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
                            $senderGroup->addPermission($args[3], $time);
                        } else
                            $senderGroup->addPermission($args[3]);

                        $sender->sendMessage(MessageUtil::format("Permisja §e{$args[3]}§r§7 dodana!" . ($time == null ? "" : " na §e{$args[4]}§r§7!")));

                        $player = $sender->getServer()->getPlayerByPrefix($args[1]);

                        if($player instanceof CorePlayer) {
                            $player->syncAvailableCommands();
                        }
                        break;

                    case "remove":
                        if(!isset($args[3])) {
                            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["user"], [$args[1]], ["remove"], ["permisja"]]));
                            return;
                        }

                        $senderGroup->removePermission($args[3]);
                        $sender->sendMessage(MessageUtil::format("Permisja §e{$args[3]}§r§7 zostala usunieta!"));
                        break;

                    case "group":
                        if(!isset($args[4])) {
                            $sender->sendMessage(MessageUtil::format("Doszlo do bledu! sprawdz czy dobrze wpisales komende!"));
                            return;
                        }

                        if(!Main::getInstance()->getGroupManager()->getGroupByName($args[4])) {
                            $sender->sendMessage(MessageUtil::format("Ta ranga nie istnieje!"));
                            return;
                        }

                        switch($args[3]) {
                            case "add":
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
                                    $senderGroup->addGroup($args[4], $time);
                                } else
                                    $senderGroup->addGroup($args[4]);

                                $sender->sendMessage(MessageUtil::format("Uzytkownikowi §e{$args[1]} §r§7dodano range §e{$args[4]}§r§7" . ($time == null ? "" : " na §e{$args[5]}")));
                                break;

                            case "remove":
                                $senderGroup->removeGroup($args[4]);

                                $sender->sendMessage(MessageUtil::format("Uzytkownikowi §e{$args[1]} §r§7usunieto range §e{$args[4]}§r§7!"));
                                break;

                            case "set":
                                $senderGroup->setGroup($args[4]);

                                $sender->sendMessage(MessageUtil::format("Uzytkownikowi §e{$args[1]} §r§7ustawiono range na §e{$args[4]}§r§7"));

                                $player = $sender->getServer()->getPlayerByPrefix($args[1]);

                                if($player instanceof CorePlayer) {
                                    $player->syncAvailableCommands();
                                }
                                break;
                            default:
                                $sender->sendMessage(MessageUtil::format("Doszlo do bledu! sprawdz czy dobrze wpisales komende!"));
                        }
                        break;

                    default:
                        $sender->sendMessage(MessageUtil::format("Doszlo do bledu! sprawdz czy dobrze wpisales komende!"));
                }
                break;
            default:
                $sender->sendMessage(MessageUtil::format("Doszlo do bledu! sprawdz czy dobrze wpisales komende!"));
        }
    }

    #[Pure] public function getGroups() : array {

        $groups = [];

        foreach(Main::getInstance()->getGroupManager()->getGroups() as $group)
            $groups[] = strtolower($group->getGroupName());

        return $groups;
    }
}