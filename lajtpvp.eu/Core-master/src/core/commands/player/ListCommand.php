<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;

class ListCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("list", "", false, true, ["lista"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(PermissionUtil::has($sender, Settings::$PERMISSION_TAG."list")) {
            $senderNames = [];
            foreach($sender->getServer()->getOnlinePlayers() as $p)
                $senderNames[] = $p->getName();

            $nickNames = implode("§r§7,§e ", $senderNames);
            $sender->sendMessage(MessageUtil::format("Lista graczy: §8(§e".count($senderNames)."§8) §e".$nickNames));

        } else
            $sender->sendMessage(MessageUtil::format("Online graczy: §e" . count($sender->getServer()->getOnlinePlayers())));
    }
}