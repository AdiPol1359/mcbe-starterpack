<?php

namespace awzaw\notell;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerQuitEvent;

class Main extends PluginBase implements Listener {

    private $enabled;

    public function onEnable() {
        $this->enabled = [];
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $issuer, Command $cmd, $label, array $args) {

        if (strtolower($cmd->getName()) !== "msgoff")
            return false;

        if (!(isset($args[0])) && ($issuer instanceof Player)) {
            if (isset($this->enabled[strtolower($issuer->getName())])) {
                unset($this->enabled[strtolower($issuer->getName())]);
            } else {
                $this->enabled[strtolower($issuer->getName())] = strtolower($issuer->getName());
            }

            if (isset($this->enabled[strtolower($issuer->getName())])) {
                $issuer->sendMessage("§f• §8[§eMSG§8] §eWyłączyłeś §7prywatne wiadomości do Ciebie! §f•");
            } else {
                $issuer->sendMessage("§f• §8[§eMSG§8] §eWłączyłeś §7prywatne wiadomości do Ciebie! §f•");
            }
            return true;
        } else {
            return false;
        }
    }

    public function onPlayerCommand(PlayerCommandPreprocessEvent $event) {
        if ($event->isCancelled()) return;
        $message = $event->getMessage();
        if (strtolower(substr($message, 0, 5) === "/tell") || strtolower(substr($message, 0, 4) === "/msg") || strtolower(substr($message, 0, 4) === "/t")) { //Command
            $command = substr($message, 1);
            $args = explode(" ", $command);
            if (!isset($args[1])) {
                return true;
            }
            $sender = $event->getPlayer();

            foreach ($this->enabled as $noteller) {

                if (strpos(strtolower($noteller), strtolower($args[1])) !== false) {
                    $sender->sendMessage("§f• §8[§eMSG§8] §7Ten gracz ma §ewyłączone §7prywatne wiadomości! §f•");
                    $event->setCancelled(true);
                }
            }
        }
    }

    public function onQuit(PlayerQuitEvent $e) {
        if (isset($this->enabled[strtolower($e->getPlayer()->getName())])) {
            unset($this->enabled[strtolower($e->getPlayer()->getName())]);
        }
    }

}
