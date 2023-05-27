<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\managers\AdminManager;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use core\utils\SoundUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class FeedCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("feed", "", true, false, ["najedz"]);

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
            if(!PermissionUtil::has($sender, Settings::$PERMISSION_TAG."command.feed.other")) {
                SoundUtil::addSound([$sender], $sender->getPosition()(), "block.false_permissions");
                $sender->sendMessage(MessageUtil::formatLines($this->permissionMessage(Settings::$PERMISSION_TAG . "command.feed.other")));
                return;
            }
        }

        if($target === null) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest §eOFFLINE"));
            return;
        }

        $target->getHungerManager()->setFood(20);
        $target->getHungerManager()->setSaturation(20);

        if($target !== $sender) {
            $target->sendMessage(MessageUtil::format("Administrator o nicku §e" . $sender->getName() . " §r§7wypelnil ci poziom glodu!"));
            $sender->sendMessage(MessageUtil::format("Pomyslnie wypelniles poziom glodu gracza §e{$target->getName()}"));
            AdminManager::sendMessage($sender, $sender->getName() . " wypelnil glod graczowi " . $target->getName());
        } else {
            $sender->sendMessage(MessageUtil::format("Wypelniles sobie poziom glodu!"));
            AdminManager::sendMessage($sender, $sender->getName() . " wypelnil swoj poziom glodu");
        }
    }
}