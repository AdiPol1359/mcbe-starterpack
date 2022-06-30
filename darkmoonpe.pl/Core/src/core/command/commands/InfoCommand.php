<?php

namespace core\command\commands;

use core\caveblock\CaveManager;
use core\command\BaseCommand;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

class InfoCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("info", "Info Command", false, true, "Sluzy do sprawdzania informacji o jaskiniach", []);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        $selectedCave = null;

        if(empty($args)) {

            $caves = CaveManager::getCaves($player->getName());

            if(count($caves) > 1 || count($caves) <= 0) {
                $player->sendMessage($this->correctUse($this->getCommandLabel(), [["tag"]]));
                return;
            }

            foreach($caves as $cave)
                $selectedCave = $cave;
        }

        if(isset($args[0])) {

            $argsCave = CaveManager::getCaveByTag($args[0]);

            if(!$argsCave) {
                $player->sendMessage(MessageUtil::format("Jaskinia o takim tagu nie istnieje!"));
                return;
            }

            $selectedCave = $argsCave;
        }

        $members = $selectedCave->getPlayers();
        $onlineMembers = $selectedCave->getOnlinePlayers();

        $membersMessage = " ";
        $playersCount = 0;

        foreach($members as $nick => $settings) {
            $playersCount++;
            $this->getServer()->getPlayerExact($nick) ? $membersMessage .= "§a" . $nick . "§7".($playersCount < count($members) ? ", " : "")." " : $membersMessage .= "§c" . $nick . "§7".($playersCount < count($members) ? ", " : "")." ";
        }

        $player->sendMessage(MessageUtil::customFormat([
            "wlasciciel: §9" . $selectedCave->getOwner(),
            "punkty: §9100",
            "wykopany kamine: §91005",
            "czlonkowie: §8(§a" . $onlineMembers . "§7/§c" . (count($members) - $onlineMembers) . "§7/§9" . count($members) . "§8)" . $membersMessage
        ], "§r§7JASKINIA §l§9" . $selectedCave->getName() . "§r§7!"));
    }
}