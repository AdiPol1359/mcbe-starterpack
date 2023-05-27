<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\Main;
use core\managers\TeleportManager;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;

class BaseTeleportCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("base", "", false, false, ["baza"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $senderGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName());

        if(!$senderGuild) {
            $sender->sendMessage(MessageUtil::format("Nie znajdujesz sie w zadnej gildii!"));
            return;
        }

        if(TeleportManager::isTeleporting($sender->getName())) {
            $sender->sendMessage(MessageUtil::format("Jestes w trakcje teleportacji!"));
            return;
        }

        TeleportManager::teleport($sender, Position::fromObject($senderGuild->getBaseSpawn(), $sender->getServer()->getWorldManager()->getDefaultWorld()));
    }
}