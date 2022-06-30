<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

class ListCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("list", "List Command", false, true, "Komenda list sluzy do wyswietlania graczy online na serwerze", ["lista"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if($player->hasPermission(ConfigUtil::PERMISSION_TAG . "list")) {

            $playerNames = [];
            foreach($this->getServer()->getOnlinePlayers() as $p)
                $playerNames[] = $p->getName();

            $pnicks = implode("§r§7,§l§9 ", $playerNames);
            $player->sendMessage(MessageUtil::format("Lista graczy: §l§9".$pnicks." §8(§9".count($playerNames)."§8)"));

        } else {

            $gracze = count($this->getServer()->getOnlinePlayers());
            $player->sendMessage(MessageUtil::format("Online graczy: §9§l" . $gracze));
        }
    }
}