<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};
use Gildie\Main;
use pocketmine\Player;

class DolaczCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("dolacz", "Komenda dolacz");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->canUse($sender))
            return;

        if(!$sender instanceof Player) {
            $sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
            return;
        }

        if(empty($args)) {
            $sender->sendMessage(Main::format("Poprawne uzycie: /dolacz (tag)"));
            return;
        }

        $nick = $sender->getName();

        if(!isset(Main::$invite[$nick][strtolower($args[0])])) {
            $sender->sendMessage(Main::format("Ta gildia nie wyslala Ci prosby o dolaczenie!"));
            return;
        }

        if((time() - Main::$invite[$nick][strtolower($args[0])]) > 60) {
            $sender->sendMessage(Main::format("Zaproszenie wygaslo!"));
            unset(Main::$invite[$nick][strtolower($args[0])]);
            return;
        }

        $guildManager = Main::getInstance()->getGuildManager();

        $guild = $guildManager->getGuildByTag($args[0]);

        $guild->addPlayer($nick);

        $sender->getServer()->broadcastMessage(Main::format("Gracz §4{$nick} §7dolaczyl do gildii §4{$guild->getTag()}"));

        unset(Main::$invite[$nick][strtolower($args[0])]);
    }
}