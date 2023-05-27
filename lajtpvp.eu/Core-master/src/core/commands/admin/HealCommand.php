<?php

declare(strict_types=1);

namespace core\commands\admin;

use pocketmine\command\CommandSender;

use core\{
    commands\BaseCommand,
    managers\AdminManager,
    utils\PermissionUtil,
    utils\Settings,
    utils\MessageUtil,
    utils\SoundUtil};

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class HealCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("heal", "", true, false, ["ulecz"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $target = $this->selectPlayer($sender, $args, 0);

        if(!empty($args)) {
            if(!PermissionUtil::has($sender, Settings::$PERMISSION_TAG."command.heal.other")) {
                SoundUtil::addSound([$sender], $sender->getPosition(), "block.false_permissions");
                $sender->sendMessage(MessageUtil::formatLines($this->permissionMessage(Settings::$PERMISSION_TAG . "command.heal.other")));
                return;
            }
        }

        if($target === null) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest §eOFFLINE"));
            return;
        }

        $target->setHealth($target->getMaxHealth());
        if($target !== $sender) {
            $target->sendMessage(MessageUtil::format("Administrator o nicku §e" . $sender->getName() . " §r§7uleczyl cie!"));
            $sender->sendMessage(MessageUtil::format("Pomyslnie uleczyles gracza §e{$target->getName()}"));
            AdminManager::sendMessage($sender, $sender->getName() . " uleczyl gracza " . $target->getName());
        } else {
            $sender->sendMessage(MessageUtil::format("Uleczyles sie!"));
            AdminManager::sendMessage($sender, $sender->getName() . " uleczyl sie");
        }
    }
}