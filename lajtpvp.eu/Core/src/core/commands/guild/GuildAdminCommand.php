<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\guild\panel\MainPanelInventory;
use core\inventories\fakeinventories\guild\TreasuryInventory;
use core\Main;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class GuildAdminCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("guildadmin", "", true, false, ["ga"]);

        $parameters = [
            0 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
                $this->commandParameter("deleteGuild", AvailableCommandsPacket::ARG_TYPE_STRING, false, "deleteGuild", ["delete"])
            ],

            1 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
                $this->commandParameter("tntGuild", AvailableCommandsPacket::ARG_TYPE_STRING, false, "tntSwitchGuild", ["tnt"]),
                $this->commandParameter("tntSwitchGuild", AvailableCommandsPacket::ARG_TYPE_STRING, false, "tntSwitchGuild", ["on", "off"]),
                $this->commandParameter("tntGuildTime", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ],

            2 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
                $this->commandParameter("setBaseGuild", AvailableCommandsPacket::ARG_TYPE_STRING, false, "setBaseGuild", ["setbase"])
            ],

            3 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
                $this->commandParameter("guildPanel", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildPanel", ["panel"])
            ],

            4 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
                $this->commandParameter("guildTreasury", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTreasury", ["skarbiec"])
            ],

            5 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
                $this->commandParameter("guildPlayer", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildPlayer", ["player"]),
                $this->commandParameter("guildPlayerAdd", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildPlayerAdd", ["add"]),
                $this->commandParameter("guildPlayer", AvailableCommandsPacket::ARG_TYPE_TARGET, false)
            ],

            6 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
                $this->commandParameter("guildPlayer", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildPlayer", ["player"]),
                $this->commandParameter("guildPlayerRemove", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildPlayerRemove", ["remove"]),
                $this->commandParameter("guildPlayer", AvailableCommandsPacket::ARG_TYPE_TARGET, false)
            ],

            7 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
                $this->commandParameter("guildHeart", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildHeart", ["hearts"]),
                $this->commandParameter("guildHeartAdd", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildHeartAdd", ["add"]),
                $this->commandParameter("guildHeartCount", AvailableCommandsPacket::ARG_TYPE_INT, false)
            ],

            8 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
                $this->commandParameter("guildHeart", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildHeart", ["hearts"]),
                $this->commandParameter("guildHeartRemove", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildHeartRemove", ["remove"]),
                $this->commandParameter("guildHeartCount", AvailableCommandsPacket::ARG_TYPE_INT, false)
            ],

            9 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
                $this->commandParameter("guildHeart", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildHeart", ["hearts"]),
                $this->commandParameter("guildHeartSet", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildHeartSet", ["set"]),
                $this->commandParameter("guildHeartCount", AvailableCommandsPacket::ARG_TYPE_INT, false)
            ],

            10 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
                $this->commandParameter("guildPoints", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildPoint", ["points"]),
                $this->commandParameter("guildPointsAdd", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildPointsAdd", ["add"]),
                $this->commandParameter("guildPointsCount", AvailableCommandsPacket::ARG_TYPE_INT, false)
            ],

            11 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
                $this->commandParameter("guildPoints", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildPoint", ["points"]),
                $this->commandParameter("guildPointsRemove", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildPointsRemove", ["remove"]),
                $this->commandParameter("guildPointsCount", AvailableCommandsPacket::ARG_TYPE_INT, false)
            ],

            12 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
                $this->commandParameter("guildPoints", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildPoint", ["points"]),
                $this->commandParameter("guildPointsSet", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildPointsSet", ["set"]),
                $this->commandParameter("guildPointsCount", AvailableCommandsPacket::ARG_TYPE_INT, false)
            ],

            13 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
                $this->commandParameter("teleportGuild", AvailableCommandsPacket::ARG_TYPE_STRING, false, "deleteGuild", ["teleport"])
            ],
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(empty($args) || !isset($args[1])) {
            $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Usuwa dana gildie" => ["§8(§etag§8)", "delete"], "Wlacza lub wylacza tnt na dany czas" => ["§8(§etag§8)", "tnt", "§8(§eon§7/§eoff§8)", "czas§8(§es§7/§em§7/§eh§7/§ed§8)"], "Ustawia baze gildii" => ["§8(§etag§8)", "setbase"], "Otwiera panel gildii" => ["§8(§etag§8)", "panel"], ["§8(§etag§8)", "skarbiec"], "Dodaje badz usuwa gracza z gildii" => ["§8(§etag§8)", "player", "§8(§eadd§7/§eremove§8)", "§8(§enick§8)"], "Dodaje, usuwa, badz ustawia serca gildii" => ["§8(§etag§8)", "hearts", "§8(§eadd§7/§eremove§7/§eset§8)", "§8(§eilosc§8)"]]));
            return;
        }

        if(($guild = Main::getInstance()->getGuildManager()->getGuild($args[0])) === null) {
            $sender->sendMessage(MessageUtil::format("Gildia o tym tagu nie istnieje!"));
            return;
        }

        switch($args[1]) {
            case "delete":

                if(($war = Main::getInstance()->getWarManager()->getWar($guild->getTag())) !== null)
                    $war->endWar(($war->getAttacker() === $guild->getTag() ? $war->getAttacked() : $war->getAttacker()), true);

                Main::getInstance()->getGuildManager()->deleteGuild($guild->getTag());

                $sender->sendMessage(MessageUtil::format("Poprawnie usunales gildie o tagu §e".$args[0]));
                AdminManager::sendMessage($sender, $sender->getName()." usunal gildie ".$guild->getTag());
                break;

            case "tnt":

                if(!isset($args[2]) || $args[2] !== "on" && $args[2] !== "off") {
                    $sender->sendMessage(MessageUtil::format("Nie podales poprawnejwartosci!"));
                    return;
                }

                if($args[2] === "off") {
                    $guild->setTnt(0);
                    $sender->sendMessage(MessageUtil::format("Poprawnie wylaczyles tnt na terenie gildii §e".$args[0]));
                    AdminManager::sendMessage($sender, $sender->getName()." wylaczyl tnt na terenie gildii ".$guild->getTag());
                    return;
                }

                $time = $args[3];

                switch(strtolower($time[strlen($time) - 1])) {
                    case "s":
                        $time = (int) str_replace('s', '', $time);
                        break;
                    case "m":
                        $time = (int) str_replace('m', '', $time);
                        $time = $time * 60;
                        break;
                    case "h":
                        $time = (int) str_replace('g', '', $time);
                        $time = $time * 3600;
                        break;
                    case "d":
                        $time = (int) str_replace('d', '', $time);
                        $time = $time * 86400;
                        break;

                    default:
                        $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                        return;
                }

                $guild->setTnt(($time + time()));
                $sender->sendMessage(MessageUtil::format("Poprawnie wlaczyles tnt na terenie gildii §e".$args[0]. " na §e".$args[3]));
                AdminManager::sendMessage($sender, $sender->getName()." wlaczyl tnt na terenie gildii ".$guild->getTag(). " na ".$args[3]);
                break;

            case "setbase":

                $guild->setBase($sender->getPosition());
                $sender->sendMessage(MessageUtil::format("Ustawiles spawna gildii §e".$guild->getTag()));

                AdminManager::sendMessage($sender, $sender->getName()." ustawil baze gildii ".$guild->getTag());
                break;

            case "panel":
                (new MainPanelInventory($sender, $guild))->openFor([$sender]);

                AdminManager::sendMessage($sender, $sender->getName()." otworzyl panel gildii ".$guild->getTag());
                break;

            case "skarbiec":
                (new TreasuryInventory($sender, $guild))->openFor([$sender]);

                AdminManager::sendMessage($sender, $sender->getName()." otworzyl skarbiec gildii ".$guild->getTag());
                break;

            case "player":

                if(!isset($args[2])) {
                    $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                    return;
                }

                if(!isset($args[3])) {
                    $sender->sendMessage(MessageUtil::format("Nie podales nicku!"));
                    return;
                }

                switch($args[2]) {
                    case "add":

                        if(($senderGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($args[3])) !== null)
                            $senderGuild->kickPlayer($args[3]);

                        $guild->addPlayer($args[3]);
                        $sender->sendMessage(MessageUtil::format("Dodales gracza §e".$args[3]." §7do gildii §e".$guild->getTag()));
                        AdminManager::sendMessage($sender, $sender->getName()." dodal ".$args[3]." do gildii ".$guild->getTag());
                        break;

                    case "remove":
                        if(($senderGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($args[3])) === null) {
                            $sender->sendMessage(MessageUtil::format("Ten gracz nie znajduje sie w zadnej gildii!"));
                            return;
                        }

                        $senderGuild->kickPlayer($args[2]);
                        $sender->sendMessage(MessageUtil::format("Wyrzuciles gracza §e".$args[3]." §7z gildii §e".$guild->getTag()));
                        AdminManager::sendMessage($sender, $sender->getName()." wyrzucil ".$args[3]." z gildii ".$guild->getTag());
                        break;
                }

                break;

            case "hearts":

                if(!isset($args[2])) {
                    $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                    return;
                }

                if(!isset($args[3])) {
                    $sender->sendMessage(MessageUtil::format("Nie podales wartosci!"));
                    return;
                }

                $count = round($args[3]);

                if(!is_numeric($count)) {
                    $sender->sendMessage(MessageUtil::format("Wartosc musi byc numeryczna"));
                    return;
                }

                switch($args[2]) {
                    case "add":

                        if($guild->getHearts() >= 5) {
                            $sender->sendMessage(MessageUtil::format("Ta gildia osiangela juz limit serc!"));
                            return;
                        }

                        if(($guild->getHearts() + $count) > 5) {
                            $sender->sendMessage(MessageUtil::format("Nie mozesz dodac tylu serc poniewaz przekracza one limit!"));
                            return;
                        }

                        $guild->addHearts((($guild->getHearts() + $count) > 5 ? 5 : $count));
                        $sender->sendMessage(MessageUtil::format("Dodalales §e".$count." §7serc do gildii §e".$guild->getTag()));
                        AdminManager::sendMessage($sender, $sender->getName()." dodal ".$count." serc gildii ".$guild->getTag());
                        break;

                    case "remove":

                        if($guild->getHearts() < 0) {
                            $sender->sendMessage(MessageUtil::format("Ta gildia ma juz 1 serce i nie mozna zabrac jej kolejnego!"));
                            return;
                        }

                        if(($guild->getHearts() + $count) < 0) {
                            $sender->sendMessage(MessageUtil::format("Nie mozesz zabrac tylu serc poniewaz przekroczy to minimalna ilosc!"));
                            return;
                        }

                        $guild->reduceHearts((($guild->getHearts() - $count) <= 0 ? 1 : $count));
                        $sender->sendMessage(MessageUtil::format("Usunales §e".$count." §7serc z gildii §e".$guild->getTag()));
                        AdminManager::sendMessage($sender, $sender->getName()." zredukowal ".$count." serc gildii ".$guild->getTag());
                        break;

                    case "set":

                        if($count >= 5) {
                            $sender->sendMessage(MessageUtil::format("Ta gildia osiangela juz limit serc!"));
                            return;
                        }

                        if($count < 0) {
                            $sender->sendMessage(MessageUtil::format("Ta gildia ma juz 1 serce i nie mozna zabrac jej kolejnego!"));
                            return;
                        }

                        $guild->setHearts($count);
                        $sender->sendMessage(MessageUtil::format("Ustawiles §e".$count." §7serc w gildii §e".$guild->getTag()));
                        AdminManager::sendMessage($sender, $sender->getName()." ustawil ".$count." serc gildii ".$guild->getTag());
                        break;

                    default:
                        $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                        break;
                }
                break;

            case "points":

                if(!isset($args[2])) {
                    $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                    return;
                }

                if(!isset($args[3])) {
                    $sender->sendMessage(MessageUtil::format("Nie podales wartosci!"));
                    return;
                }

                $count = round($args[3]);

                if(!is_numeric($count)) {
                    $sender->sendMessage(MessageUtil::format("Wartosc musi byc numeryczna"));
                    return;
                }

                switch($args[2]) {
                    case "add":
                        $guild->addPoints($count);
                        $sender->sendMessage(MessageUtil::format("Dodalales §e".$count." §7punktow do gildii §e".$guild->getTag()));
                        AdminManager::sendMessage($sender, $sender->getName()." dodal ".$count." punktow gildii ".$guild->getTag());
                        break;

                    case "remove":

                        $guild->reducePoints($count);
                        $sender->sendMessage(MessageUtil::format("Usunales §e".$count." §7punktow z gildii §e".$guild->getTag()));
                        AdminManager::sendMessage($sender, $sender->getName()." zredukowal ".$count." punktow gildii ".$guild->getTag());
                        break;

                    case "set":
                        $guild->setPoints($count);
                        $sender->sendMessage(MessageUtil::format("Ustawiles §e".$count." §7punktow w gildii §e".$guild->getTag()));
                        AdminManager::sendMessage($sender, $sender->getName()." ustawil ".$count." punktow gildii ".$guild->getTag());
                        break;

                    default:
                        $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                        break;
                }
                break;

            case "teleport":
                $sender->teleport($guild->getBaseSpawn());
                $sender->sendMessage(MessageUtil::format("Przeteleportowano do gildii §e".$guild->getTag()."§7!"));
                break;

            default:
                $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                break;
        }
    }
}