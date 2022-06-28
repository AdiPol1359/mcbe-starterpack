<?php

namespace Gildie\commands;

use Gildie\guild\GuildManager;
use pocketmine\Player;

use pocketmine\command\{Command, CommandSender, utils\CommandException};

use Gildie\Main;

class ZaprosCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("zapros", "Komenda zapros");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->canUse($sender))
            return;

        $guildManager = Main::getInstance()->getGuildManager();

        if(!$sender instanceof Player) {
            $sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
            return;
        }

        if(!$guildManager->isInGuild($sender->getName())) {
            $sender->sendMessage(Main::format("Musisz byc w gildii aby uzyc tej komendy!"));
            return;
        }

        $guild = $guildManager->getPlayerGuild($sender->getName());

        $rank = $guild->getPlayerRank($sender->getName());

        if(($rank !== "Leader" && $rank !== "Officer") && !$guildManager->hasPermission($sender->getName(), GuildManager::PERMISSION_INVITE_MEMBERS)) {
            $sender->sendMessage(Main::format("Musisz byc liderem, zastepca gildii albo posiadac permisje aby to zrobic! $rank"));
            return;
        }

        if(empty($args)) {
            $sender->sendMessage(Main::format("Poprawne uzycie: /zapros (nick)"));
            return;
        }

        $player = $sender->getServer()->getPlayer($args[0]);

        if($player === null) {
            $sender->sendMessage("§8§l>§r §7Ten gracz jest offline!");
            return;
        }

        $nick = $player->getName();

        if($guildManager->isInGuild($nick)) {
            $sender->sendMessage("§8§l>§r §7Ten gracz jest juz w gildii!");
            return;
        }

        $sender->sendMessage(Main::format("Pomyslnie zaproszono do gildii gracza §4$nick"));
        $player->sendMessage(Main::formatLines(["Zostales zaproszony do gildii §4{$guild->getTag()}", "Aby zaakceptowac, uzyj §4/dolacz §4{$guild->getTag()}"]));

        Main::$invite[$nick][strtolower($guild->getTag())] = time();
    }
}