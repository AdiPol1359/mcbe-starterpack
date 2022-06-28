<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};
use Gildie\form\PermissionsForm;
use Gildie\guild\GuildManager;
use Gildie\Main;
use pocketmine\Player;

class PermisjeCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("permisje", "Komenda permisje");
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

        if($rank !== "Leader" && !$guildManager->hasPermission($nick, GuildManager::PERMISSION_SET_PERMISSIONS)) {
            $sender->sendMessage("§8§l>§r §7Musisz byc liderem albo posiadac permisje aby to zrobic!");
            return;
        }

        if(empty($args)) {
            $sender->sendMessage("§8§l>§r §7Poprawne uzycie: /permisje §8(§4nick§8)");
            return;
        }

        if(strtolower($args[0]) == strtolower($sender->getName())) {
            $sender->sendMessage("§8§l>§r §7Nie mozesz zmienic sobie permisji!");
            return;
        }

        if(!$guildManager->isInGuild($args[0])) {
            $sender->sendMessage("§8§l>§r §7Nie ma tego gracza w twojej gildii!");
            return;
        }

        if(strtolower($guildManager->getPlayerGuild($args[0])->getLeader()) == strtolower($args[0])) {
            $sender->sendMessage(Main::format("Nie mozesz zmienic permisji liderowi gildii!"));
            return;
        }

        $sender->sendForm(new PermissionsForm($args[0]));
    }
}