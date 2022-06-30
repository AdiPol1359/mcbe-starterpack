<?php

namespace core\command\commands;

use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

use core\{
    command\BaseCommand,
    manager\managers\AdminManager,
    util\utils\ConfigUtil,
    util\utils\MessageUtil};

use core\manager\managers\SoundManager;

class HealCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("heal", "Heal Command", true, false, "Komenda heal leczy gracza", ["ulecz"]);

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

        $target->setHealth($target->getMaxHealth());
        if($target !== $player) {
            AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7uleszyl gracza §l§9".$target->getName()."§r§7!"), [$player->getName()]);
            $target->sendMessage(MessageUtil::format("Administrator o nicku §l§9" . $player->getName() . " §r§7uleczyl cie!"));
            $player->sendMessage(MessageUtil::format("Pomyslnie uleczyles gracza §9§l{$target->getName()}"));
        } else {
            if($player->hasPermission(ConfigUtil::PERMISSION_TAG."administrator"))
                AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7ulczyl sie"), [$player->getName()]);
            $player->sendMessage(MessageUtil::format("Uleczyles sie!"));
        }
    }
}