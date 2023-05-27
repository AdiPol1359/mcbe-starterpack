<?php

declare(strict_types=1);

namespace core\commands\admin;

use pocketmine\command\CommandSender;

use core\{
    commands\BaseCommand,
    Main,
    managers\AdminManager,
    utils\PermissionUtil,
    utils\Settings,
    utils\MessageUtil,
    utils\SoundUtil};

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class GodCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("god", "", true, false);

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
            if(!PermissionUtil::has($sender, Settings::$PERMISSION_TAG."command.god.other")) {
                SoundUtil::addSound([$sender], $sender->getPosition(), "block.false_permissions");
                $sender->sendMessage(MessageUtil::formatLines($this->permissionMessage(Settings::$PERMISSION_TAG . "command.heal.other")));
                return;
            }
        }

        if($target === null) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest §eOFFLINE"));
            return;
        }

        if(!($targetUser = Main::getInstance()->getUserManager()->getUser($target->getName()))) {
            $sender->sendMessage(MessageUtil::format("Ten gracz nie jest zarejestrowany!"));
            return;
        }

        $targetUser->setGodMode(!$targetUser->hasGod());
        $message = $targetUser->hasGod() ? "Wlaczyl" : "Wylaczyl";
        if($target !== $sender) {
            $target->sendMessage(MessageUtil::format("Administrator o nicku §e" . $sender->getName() . " §r§7".strtolower($message)." ci godmode'a!"));
            $sender->sendMessage(MessageUtil::format("Pomyslnie ".strtolower($message)."es godmode'a graczowi §e{$target->getName()}"));
            AdminManager::sendMessage($sender, $sender->getName() . " ".strtolower($message)." godmode'a graczowi " . $target->getName());
        } else {
            $sender->sendMessage(MessageUtil::format($message ."es sobie godmode'a!"));
            AdminManager::sendMessage($sender, $sender->getName() . " ".strtolower($message)." sobie godmode'a");
        }
    }
}