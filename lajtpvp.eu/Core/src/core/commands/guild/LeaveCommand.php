<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\guilds\GuildPlayer;
use core\Main;
use core\managers\nameTag\NameTagPlayerManager;
use core\utils\BroadcastUtil;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class LeaveCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("leave", "", false, false, ["opusc"]);
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

        if($guildPlayer->getRank() === GuildPlayer::LEADER) {
            $sender->sendMessage(MessageUtil::format("Jako lider nie mozesz opuscic gildii tylko mozesz ja usunac!"));
            return;
        }

        $guild->kickPlayer($sender->getName());

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($guild, $sender) : void {
            $onlinePlayer->sendMessage(MessageUtil::format("§e".$sender->getName()." §7opuscil gildie §e".$guild->getTag()));
        });

        NameTagPlayerManager::updatePlayersAround($sender);
    }
}