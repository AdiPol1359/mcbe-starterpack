<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\Main;
use core\utils\MessageUtil;
use core\utils\Settings;
use core\utils\TimeUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class PlayerCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("player", "", false, false, ["gracz"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        $selected = $sender->getName();

        if(isset($args[0]))
            $selected = implode(" ", $args);

        $user = Main::getInstance()->getUserManager()->getUser($selected);

        if(!$user) {
            $sender->sendMessage(MessageUtil::format("Ten gracz nigdy nie gral na tym serwerze!"));
            return;
        }

        $statManager = $user->getStatManager();

        $guild = Main::getInstance()->getGuildManager()->getPlayerGuild($user->getName());

        $lastPlayed = $sender->getServer()->getPlayerByPrefix($user->getName()) ? time() : $statManager->getStat(Settings::$STAT_LAST_JOIN_TIME);
        $timePlayed = ($statManager->getStat(Settings::$STAT_SPEND_TIME) + ($sender->getServer()->getPlayerExact($user->getName()) ? (time() - $statManager->getStat(Settings::$STAT_LAST_JOIN_TIME)) : 0));

        $sender->sendMessage(MessageUtil::formatLines(
            [
                "§7Nick§8: ".($guild ? ("§8[".$guild->getColorForPlayer($sender->getName()).$guild->getTag()."§8] ") : "")."§e".$user->getName(),
                "§7Punkty§8: §e".$statManager->getStat(Settings::$STAT_POINTS),
                "§7Zabojstwa§8: §e".$statManager->getStat(Settings::$STAT_KILLS),
                "§7Smierci§8: §e".$statManager->getStat(Settings::$STAT_DEATHS),
                "§7Asysty§8: §e".$statManager->getStat(Settings::$STAT_ASSISTS),
                "§7Zniszczone bloki§8: §e".$statManager->getStat(Settings::$STAT_BREAK_BLOCKS),
                "§7Postawione bloki§8: §e".$statManager->getStat(Settings::$STAT_PLACE_BLOCKS),
                "§7Ostatnio widziany: §e".date("d.m.Y H:i:s", $lastPlayed),
                "§7Spedzony czas: §e".TimeUtil::convertIntToStringTime($timePlayed, "§e", "§7", true, false)
                ]
            , "STATYSTYKI"));
    }
}