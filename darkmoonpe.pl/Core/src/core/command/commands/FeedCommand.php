<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\manager\managers\AdminManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

use core\manager\managers\SoundManager;

class FeedCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("feed", "Feed Command", true, false, "Komenda feed sluzy do uzupelniania glodu tylko dla rang premium!", ["najedz"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, true),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        $target = $this->selectPlayer($player, $args, 0);

        if(!empty($args)) {
            if(!$player->hasPermission(ConfigUtil::PERMISSION_TAG . "command.feed.other")) {
                SoundManager::addSound($player, $player->asVector3(), "block.false_permissions");
                $player->sendMessage(MessageUtil::formatLines($this->permissionMessage(ConfigUtil::PERMISSION_TAG . "command.feed.other")));
                return;
            }
        }

        if($target === null) {
            $player->sendMessage(MessageUtil::format("Ten gracz jest §l§9OFFLINE"));
            return;
        }

        $target->setFood(20);
        $target->setSaturation(20);
        if($target !== $player) {
            AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7wypelnil poziom glodu gracza §l§9" . $target->getName() . "§r§7!"), [$player->getName()]);
            $target->sendMessage(MessageUtil::format("Administrator o nicku §l§9" . $player->getName() . " §r§7wypelnil ci poziom glodu!"));
            $player->sendMessage(MessageUtil::format("Pomyslnie wypelniles poziom glodu gracza §9§l{$target->getName()}"));
        } else {
            if($player->hasPermission(ConfigUtil::PERMISSION_TAG."administrator"))
                AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7wypelnil sobie poziom glodu"), [$player->getName()]);
            $player->sendMessage(MessageUtil::format("Wypelniles sobie poziom glodu!"));
        }
    }
}