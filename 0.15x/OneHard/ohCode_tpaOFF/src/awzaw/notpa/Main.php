<?php

namespace awzaw\notpa;

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

        if (strtolower($cmd->getName()) !== "tpaoff")
            return false;

        if (!(isset($args[0])) && ($issuer instanceof Player)) {
            if (isset($this->enabled[strtolower($issuer->getName())])) {
                unset($this->enabled[strtolower($issuer->getName())]);
            } else {
                $this->enabled[strtolower($issuer->getName())] = strtolower($issuer->getName());
            }

            if (isset($this->enabled[strtolower($issuer->getName())])) {
                $issuer->sendMessage("§f• §8[§3Teleportacja§8] §3Wyłączyłeś §7prośby o teleportacje do Ciebie! §f•");
            } else {
                $issuer->sendMessage("§f• §8[§3Teleportacja§8] §3Włączyłeś §7prośby o teleportacje do Ciebie! §f•");
            }
            return true;
        } else {
            return false;
        }
    }

    public function onPlayerCommand(PlayerCommandPreprocessEvent $event) {
        if ($event->isCancelled()) return;
        $message = $event->getMessage();
        if (strtolower(substr($message, 0, 5) === "/tpahere") || strtolower(substr($message, 0, 4) === "/tpa")) { //Command
            $command = substr($message, 1);
            $args = explode(" ", $command);
            if (!isset($args[1])) {
                return true;
            }
            $sender = $event->getPlayer();

            foreach ($this->enabled as $noteller) {

                if (strpos(strtolower($noteller), strtolower($args[1])) !== false) {
                    $sender->sendMessage("§f• §8[§3Teleportacja§8] §7Ten gracz ma §3wyłączone §7prośby o teleportacje! §f•");
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
