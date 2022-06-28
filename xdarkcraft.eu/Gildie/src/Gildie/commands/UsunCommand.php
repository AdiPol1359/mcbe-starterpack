<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender};
use Gildie\Main;
use pocketmine\Player;

class UsunCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("usun", "Komenda usun");
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
            $sender->sendMessage("§8§l>§r §7Musisz byc w gildii aby uzyc tej komendy!");
            return;
        }

        $guild = $guildManager->getPlayerGuild($nick);

        if($guild->getPlayerRank($nick) !== "Leader") {
            $sender->sendMessage("§8§l>§r §7Musisz byc liderem gildii aby to zrobic!");
            return;
        }

        $sender->getServer()->broadcastMessage(Main::format("Gracz §4$nick §7usunal gildie §8[§4{$guild->getTag()}§8] - §4{$guild->getName()}"));

        $guild->remove($sender->getLevel());
    }
}