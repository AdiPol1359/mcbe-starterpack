<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\guild\panel\MainPanelInventory;
use core\guilds\GuildPlayer;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class PanelCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("panel", "", false, false);
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

        $senderGuildUser = $senderGuild->getPlayer($sender->getName());

        if(!$senderGuildUser->getSetting(GuildPlayer::PANEL)) {
            $sender->sendMessage(MessageUtil::format("Nie masz uprawnien aby to zrobic!"));
            return;
        }

        (new MainPanelInventory($sender, $senderGuild))->openFor([$sender]);
    }
}