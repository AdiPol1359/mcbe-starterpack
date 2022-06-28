<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};
use Gildie\guild\GuildManager;
use Gildie\Main;
use pocketmine\Player;

class WyrzucCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("wyrzuc", "Komenda wyrzuc");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->canUse($sender))
            return;

        $guildManager = Main::getInstance()->getGuildManager();

        $nick = $sender->getName();

        if(!$sender instanceof Player) {
            $sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
            return;
        }


        if(!$guildManager->isInGuild($nick)) {
            $sender->sendMessage(Main::format("Musisz byc w gildii, aby uzyc tej komendy!"));
            return;
        }

        $guild = $guildManager->getPlayerGuild($nick);

        $rank = $guild->getPlayerRank($nick);

        if(($rank !== "Leader" && $rank !== "Officer") && !$guildManager->hasPermission($nick, GuildManager::PERMISSION_KICK_MEMBERS)) {
            $sender->sendMessage(Main::format("Musisz byc liderem, oficerem gildii albo posiadac permisje aby to zrobic!"));
            return;
        }

        if(empty($args)) {
            $sender->sendMessage(Main::format("Poprawne uzycie: /wyrzuc (nick)"));
            return;
        }

        $player = $sender->getServer()->getPlayer($args[0]);

        $pNick = $player === null ? $args[0] : $player->getName();

        if(!$guild->isPlayerInGuild($pNick)) {
            $sender->sendMessage(Main::format("Tego gracza nie ma w twojej gildii!"));
            return;
        }

        if($pNick === $sender->getName()) {
            $sender->sendMessage(Main::format("Nie mozesz wyrzucic siebie z gildii!"));
            return;
        }

        if($guild->getPlayerRank($pNick) === "Leader") {
            $sender->sendMessage(Main::format("Nie mozesz wyrzucic tego gracza z gildii!"));
            return;
        }

        $guild->removePlayer($pNick);

        $sender->getServer()->broadcastMessage(Main::format("Gracz §4$pNick §7zostal wyrzucony z gildii §8[§4{$guild->getTag()}§8]"));
    }
}