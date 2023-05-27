<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\Main;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use core\utils\Settings;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class StatsCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("stats", "", true, false, ["stat"]);

        $parameters = [
            0 => [
                $this->commandParameter("statsPlayer", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("statsAddOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "statsAddOption", ["add", "set", "reduce"]),
                $this->commandParameter("statisticAdd", AvailableCommandsPacket::ARG_TYPE_STRING, false, "statisticAdd", [
                    Settings::$STAT_POINTS,
                    Settings::$STAT_KILLS,
                    Settings::$STAT_DEATHS,
                    Settings::$STAT_ASSISTS,
                    Settings::$STAT_BREAK_BLOCKS,
                    Settings::$STAT_PLACE_BLOCKS,
                    Settings::$STAT_SPEND_TIME,
                    Settings::$STAT_LAST_JOIN_TIME,
                    Settings::$STAT_ENDER_PEARLS,
                    Settings::$STAT_GOLDEN_APPLES,
                    Settings::$STAT_ENCHANTED_APPLES,
                    Settings::$STAT_ARROWS,
                    Settings::$STAT_SNOWBALLS,
                    Settings::$STAT_THROWABLE_TNT,
                ]),
                $this->commandParameter("value", AvailableCommandsPacket::ARG_TYPE_INT, false),
            ],
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args) || !isset($args[1]) || !isset($args[2]) || !isset($args[3])) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["nick"], ["add", "set", "reduce"], [
                Settings::$STAT_POINTS,
                Settings::$STAT_KILLS,
                Settings::$STAT_DEATHS,
                Settings::$STAT_ASSISTS,
                Settings::$STAT_BREAK_BLOCKS,
                Settings::$STAT_PLACE_BLOCKS,
                Settings::$STAT_SPEND_TIME,
                Settings::$STAT_LAST_JOIN_TIME,
                Settings::$STAT_ENDER_PEARLS,
                Settings::$STAT_GOLDEN_APPLES,
                Settings::$STAT_ENCHANTED_APPLES,
                Settings::$STAT_ARROWS,
                Settings::$STAT_SNOWBALLS,
                Settings::$STAT_THROWABLE_TNT,
            ], ["value"]]));
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($args[0]);

        if(!$user) {
            $sender->sendMessage(MessageUtil::format("Ten gracz nigdy nie gral na serwerze!"));
            return;
        }

        $statManager = $user->getStatManager();

        if(!$statManager->existsStat($args[2])) {
            $sender->sendMessage(MessageUtil::format("Statystyka o podanej nazwie nie istenieje!"));
            return;
        }

        if(!is_numeric($args[3])) {
            $sender->sendMessage(MessageUtil::format("Wartosc musi byc numerczna!"));
            return;
        }

        switch($args[1]) {

            case "add":
                $statManager->addStat($args[2], (int)$args[3]);
                $sender->sendMessage(MessageUtil::format("Dodano §e" . $args[3] . " §7do statystyki §e" . $args[2] . " §7graczowi §e" . $user->getName()));
                AdminManager::sendMessage($sender, $sender->getName() . " dodal do statystyki " . $args[2] . " " . $args[3] . " graczowi " . $user->getName());
                break;

            case "set":
                $statManager->setStat($args[2], (int)$args[3]);
                $sender->sendMessage(MessageUtil::format("Ustawiono statystyke §e" . $args[1] . " §7na §e" . $args[3] . " §7graczowi §e" . $user->getName()));
                AdminManager::sendMessage($sender, $sender->getName() . " ustawil statystyke " . $args[2] . " na " . $args[3] . " graczowi " . $user->getName());
                break;

            case "reduce":
                $statManager->reduceStat($args[2], (int)$args[3]);
                $sender->sendMessage(MessageUtil::format("Zredukowano §e" . $args[3] . " §7w statystyce §e" . $args[2] . " §7graczowi §e" . $user->getName()));
                AdminManager::sendMessage($sender, $sender->getName() . " zredukowal statystyke " . $args[2] . " na " . $args[3] . " graczowi " . $user->getName());
                break;

            default:
                $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                break;
        }
    }
}