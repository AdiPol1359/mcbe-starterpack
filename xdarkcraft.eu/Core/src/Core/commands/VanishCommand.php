<?php

namespace Core\commands;

use pocketmine\command\{
    Command, CommandSender, ConsoleCommandSender
};
use Core\Main;
use permissionex\managers\NameTagManager;

class VanishCommand extends CoreCommand {

    public function __construct() {
        parent::__construct("vanish", "Komenda vanish", true, ["v"]);
    }

    public function execute(CommandSender $sender, string $label, array $args) : void {
        if(!$this->canUse($sender))
            return;

        $player = $sender;

        if(isset($args[0])) {
            $player = $sender->getServer()->getPlayer($args[0]);

            if($player == null) {
                $sender->sendMessage("§8§l>§r §7Ten gracz jest §coffline§7!");
                return;
            }
        }

        if($player instanceof ConsoleCommandSender) {
            $player->sendMessage("§8§l>§r §7Tej komendy mozesz uzyc tylko w grze!");
            return;
        }

        if(!in_array($player->getName(), Main::$vanish)) {
            Main::$vanish[] = $player->getName();

            foreach($player->getServer()->getOnlinePlayers() as $p) {
                if(!$p->hasPermission("xdarkcraft.vanish.see"))
                    $p->hidePlayer($player);
            }

            $player->setNametag("§8[§4V§8] §r".NameTagManager::getNameTag($player));

            if(!$player === $sender || $sender instanceof ConsoleCommandSender)
                $sender->sendMessage("§8§l>§r §7Pomyslnie wlaczono vanish graczu §2{$player->getName()}§7!");

            $player->sendMessage(Main::format("Twoj vanish zostal wlaczony"));
        } else {
            $key = array_search($player->getName(), Main::$vanish);
            unset(Main::$vanish[$key]);

            $player->spawnToAll();

            $ntag = NameTagManager::getNametag($player);
            $player->setNametag("$ntag");

            if(!$player === $sender || $sender instanceof ConsoleCommandSender)
                $sender->sendMessage("§8§l>§r §7Pomyslnie wylaczono vanish graczu §2{$player->getName()}§7!");

            foreach($player->getServer()->getOnlinePlayers() as $p)
                $p->showPlayer($player);

            $player->sendMessage(Main::format("Twoj vanish zostal wylaczony"));
        }
    }
}