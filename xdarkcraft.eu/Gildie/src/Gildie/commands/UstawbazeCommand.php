<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};
use Gildie\Main;
use pocketmine\Player;

class UstawbazeCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("ustawbaze", "Komenda ustawbaze");
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
            $sender->sendMessage("§8§l>§r §7Musisz byc w gildii, aby uzyc tej komendy!");
            return;
        }

        $guild = $guildManager->getPlayerGuild($nick);

        $rank = $guild->getPlayerRank($nick);

        if($rank !== "Leader" && $rank !== "Officer") {
            $sender->sendMessage("§8§l>§r §7Musisz byc liderem albo oficerem gildii aby to zrobic!");
            return;
        }

        if(!$guildManager->isInOwnPlot($sender, $sender)) {
            $sender->sendMessage("§8§l>§r §7Nie mozesz ustawic bazy za terenem gildii!");
            return;
        }
        
        $guild->setBase($sender);

        $sender->sendMessage(Main::format("Baza gildii zostala ustawiona!"));
    }
}