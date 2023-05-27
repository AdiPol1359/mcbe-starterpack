<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class RenderDistanceCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("renderdistance", "", true, true, ["rd"]);

        $parameters = [
            1 => [
                $this->commandParameter("gamemodeOptions", AvailableCommandsPacket::ARG_TYPE_INT, false),
            ],
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(!$sender instanceof Player) {
            return;
        }

        $rd = $sender->getServer()->getViewDistance();

        if(empty($args)) {
            $sender->sendMessage(MessageUtil::format("Aktutalny render distance wynosi §e".$rd."§8x§e".$rd));
            return;
        }

        if(!is_int((int)$args[0])) {
            $sender->sendMessage(MessageUtil::format("Wartosc dystansu musi byc numeryczna!"));
            return;
        }

        $sender->getServer()->getConfigGroup()->setConfigInt("view-distance", (int)$args[0]);

        foreach($sender->getServer()->getOnlinePlayers() as $onlinePlayer)
            $onlinePlayer->setViewDistance($sender->getServer()->getViewDistance());

        $sender->sendMessage(MessageUtil::format("Poprawnie zmieniono render distance na §e".(int)$args[0]."§8x§e".(int)$args[0]." §7chunki"));
    }
}