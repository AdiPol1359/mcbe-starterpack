<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};
use Gildie\Main;
use pocketmine\Player;
use pocketmine\item\Item;

class PrzedluzCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("przedluz", "Komenda przedluz");
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

        $guild = $guildManager->getPlayerGuild($nick);

        if($guild->getPlayerRank($nick) !== "Leader" && $guild->getPlayerRank($nick) !== "Officer") {
            $sender->sendMessage(Main::format("Musisz byc liderem albo zastepca gildii aby uzyc tej komendy!"));
            return;
        }

        if(!$sender->getInventory()->contains(Item::get(264, 0, 256))) {
            $sender->sendMessage(Main::format("Do przedluzenia gildii potrzebujesz 3 staki diaxow"));
            return;
        }

        $sender->getInventory()->removeItem(Item::get(264, 0, 256));

        $date = date_create($guild->getExpiryDate());
        date_add($date,date_interval_create_from_date_string("1 days"));
        $guild->setExpiryDate(date_format($date,"d.m.Y H:i:s"));

        $sender->sendMessage(Main::format("Pomyslnie przedluzono waznosc gildii o 1 dzien!"));
    }
}