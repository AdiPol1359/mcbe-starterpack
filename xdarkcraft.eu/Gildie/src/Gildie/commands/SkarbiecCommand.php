<?php

namespace Gildie\commands;

use Gildie\guild\GuildManager;
use pocketmine\Player;

use pocketmine\command\CommandSender;

use Gildie\Main;

class SkarbiecCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("skarbiec", "Komenda skarbiec");
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

        if(($rank !== "Leader" && $rank !== "Officer") && !$guildManager->hasPermission($sender->getName(), GuildManager::PERMISSION_SKARBIEC_OPEN)) {
            $sender->sendMessage(Main::format("Musisz byc liderem, zastepca gildii albo posiadac permisje aby to zrobic! $rank"));
            return;
        }

        $guild->addSkarbiecInventory($sender);
    }
}