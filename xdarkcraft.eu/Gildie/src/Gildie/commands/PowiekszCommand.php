<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};
use Gildie\Main;
use pocketmine\Player;
use pocketmine\item\Item;

class PowiekszCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("powieksz", "Komenda powieksz");
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

        if($guild->getPlayerRank($nick) !== "Leader" && $guild->getPlayerRank($nick) !== "Officer") {
            $sender->sendMessage("§8§l>§r §7Musisz byc liderem albo zastepca gildii aby uzyc tej komendy!");
            return;
        }

        if($guild->getPlotSize() >= $guild->getMaxPlotSize()) {
            $sender->sendMessage("§8§l>§r §7Twoj teren jest juz powiekszony na do maksymalnej wielkosci!");
            return;
        }

        if(!$sender->getInventory()->contains(Item::get(264, 0, 64))) {
            $sender->sendMessage("§8§l>§r §7Do przedluzenia gildii potrzebujesz §464 §7diaxy!");
            return;
        }

        $sender->getInventory()->removeItem(Item::get(264, 0, 64));

        $guild->addPlotSize(4);

        $sender->sendMessage(Main::format("Pomyslnie powiekszono teren gildii do rozmiaru §4".$guild->getPlotSize()."§7x§4".$guild->getPlotSize()));
    }
}