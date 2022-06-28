<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};
use Gildie\Main;
use pocketmine\Player;

class WalkaCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("walka", "Komenda walka");
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
            $sender->sendMessage(Main::format("Musisz byc w gildii aby uzyc tej komendy!"));
            return;
        }

        $guild = $guildManager->getPlayerGuild($sender->getName());

        if(($time = time() - $guild->getBattleTime()) < 15) {
            $sender->sendMessage(Main::format("Tej komendy mozesz uzyc za: §4".(15-$time)." §7sekund"));
            return;
        }

        $guild->setBattleTime();

        $sender->getServer()->broadcastMessage(Main::formatLines([
            "Gildia §l§4{$guild->getTag()} §r§7zaprasza na §4klepe§7!",
            "Jego kordy to X: §l§4{$guild->getHeartPosition()->getFloorX()} §r§7Z: §l§4{$guild->getHeartPosition()->getFloorZ()}"
        ]));
    }
}