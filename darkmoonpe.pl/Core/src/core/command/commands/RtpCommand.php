<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

class RtpCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("rtp", "Rtp Command", true, false, "Komenda rtp sluzy do teleportacji losowego gracza do administratora");
    }

    public function onCommand(CommandSender $player, array $args) : void {
        $players = [];

        foreach($player->getServer()->getOnlinePlayers() as $p) {
            if($p->getName() === $player->getName())
                continue;
            $players[$p->getName()] = 1;
        }

        if(empty($players)){
            $player->sendMessage(MessageUtil::format("Na serwerze nie ma innych graczy!"));
            return;
        }

        $randomPlayer = $player->getServer()->getPlayerExact(array_rand($players, 1));

        $randomPlayer->teleport($player->asPosition());
        $player->sendMessage(MessageUtil::format("Przeteleportowano gracza o nicku: §9§l" . $randomPlayer->getName()));
    }
}