<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\guild\TreasuryInventory;
use core\guilds\GuildPlayer;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TreasuryCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("treasury", "", false, false, ["skarbiec"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(($guild = Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName())) === null) {
            $sender->sendMessage(MessageUtil::format("Nie znajdujesz sie w zadnej gildii!"));
            return;
        }

        $guildPlayer = $guild->getPlayer($sender->getName());

        if(!$guildPlayer->getSetting(GuildPlayer::TREASURY)) {
            $sender->sendMessage(MessageUtil::format("Nie masz uprawnien aby otwierac skarbiec!"));
            return;
        }

        (new TreasuryInventory($sender, $guild))->openFor([$sender]);
    }
}