<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use core\utils\PermissionUtil;
use core\utils\Settings;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class PingCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("pingserver", "", false, false, [], [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(empty($args) || !PermissionUtil::has($sender, Settings::$PERMISSION_TAG."command.ping")) {
            $sender->sendMessage(MessageUtil::format("Twoj ping wynosi: §e" . $sender->getNetworkSession()->getPing() . "ms"));
            return;
        }

        $nick = implode(" ", $args);
        if(($player = $sender->getServer()->getPlayerByPrefix($nick)) === null) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest offline!"));
            return;
        }

        $sender->sendMessage(MessageUtil::format("Ping gracza &3" . $nick . " &fwynosi &3" . $player->getNetworkSession()->getPing() . "ms"));
    }
}