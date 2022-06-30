<?php

namespace core\listener\events;

use core\listener\BaseListener;
use core\Main;
use core\manager\managers\BanManager;
use core\manager\managers\SoundManager;
use core\manager\managers\terrain\TerrainManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class CommandListener extends BaseListener{

    /**
     * @param PlayerCommandPreprocessEvent $e
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function CommandCoolDown(PlayerCommandPreprocessEvent $e) {

        if($e->getMessage()[0] !== "/")
            return;

        $player = $e->getPlayer();
        $nick = $player->getName();

        if($player->hasPermission(ConfigUtil::PERMISSION_TAG."command.cooldown"))
            return;

        isset(Main::$lastCmd[$nick]) ? $time = Main::$lastCmd[$nick] : $time = 0;

        if(time() - $time < ConfigUtil::COMMAND_SPAM_TIME) {
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Za szybko wpisuejsz komend, odczekaj §l§9" . (ConfigUtil::COMMAND_SPAM_TIME - (time() - $time)) . " §r§7sekund za nim wpiszesz ponownie komende!"));
        }else
            Main::$lastCmd[$nick] = time();
    }

    /**
     * @param PlayerCommandPreprocessEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function TestPermission(PlayerCommandPreprocessEvent $e) : void {

        if($e->isCancelled())
            return;

        $player = $e->getPlayer();

        $cmd = explode(" ", $e->getMessage())[0];

        $cmd = $this->getServer()->getCommandMap()->getCommand(substr(strtolower($cmd), 1));

        if($cmd !== null && $cmd->getPermission() !== null && $cmd->getPermission() !== ConfigUtil::PERMISSION_TAG . "command.see" && !$player->hasPermission($cmd->getPermission())) {
            $player->sendMessage(MessageUtil::formatLines(["Nie posiadasz uprawnien, aby uzyc tej komendy! §8(§9§l{$cmd->getPermission()}§r§8)", "Sprawdz liste komend pod §8(§9§l/pomoc§r§8)"]));
            SoundManager::addSound($player, $player->asVector3(), "block.false_permissions");
            $e->setCancelled(true);
            return;
        }
    }

    public function isBannedOnChat(PlayerCommandPreprocessEvent $e) : void {
        $player = $e->getPlayer();
        if(BanManager::isBanned($player->getName()))
            $e->setCancelled(true);
    }

    public function BlokadaKomendSpr(PlayerCommandPreprocessEvent $e) : void {
        $gracz = $e->getPlayer();
        $nick = $gracz->getName();
        $cmd = explode(" ", strtolower($e->getMessage()));

        if(isset(Main::$sprawdzanie[$nick])) {
            if(!($cmd[0] === "/msg" || $cmd[0] === "/r" || $cmd[0] == "/przyznajesie")) {
                $e->setCancelled(true);
                $gracz->sendMessage(MessageUtil::format("Jestes sprawdzany, nie mozesz uzyc tej komendy"));
            }
        }
    }

    public function UnknownCommandMessage(PlayerCommandPreprocessEvent $e) : void {
        $player = $e->getPlayer();

        if($e->getMessage()[0] !== '/')
            return;

        $cmd = explode(" ", $e->getMessage())[0];
        $cmdMap = $this->getServer()->getCommandMap();

        if($cmdMap->getCommand(substr(strtolower($cmd), 1)) === null) {
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Nieznana komenda uzyj §9§l/pomoc§r§7 aby zobaczyc przydatne komendy"));
            SoundManager::addSound($player, $player->asVector3(), "block.false_permissions");
            return;
        }

        SoundManager::addSound($player, $player->asVector3(), "random.pop");
        $e->setMessage(strtolower($cmd) . str_replace($cmd, "", $e->getMessage()));
    }

    public function CommandTracker(PlayerCommandPreprocessEvent $e) : void {

        if($e->getMessage()[0] !== '/')
            return;

        $e->getPlayer()->getServer()->getLogger()->info("§l§8[§7LOG§8] §r§b" . $e->getPlayer()->getName() . " §l§7» " . $e->getMessage());
    }

    public function lobbyCommandsBlock(PlayerCommandPreprocessEvent $e) {
        if($e->getPlayer()->isOp())
            return;

        if($e->getPlayer()->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
            $e->setCancelled(true);
    }

    /**
     * @param PlayerCommandPreprocessEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function protectCommand(PlayerCommandPreprocessEvent $e) : void {
        $player = $e->getPlayer();

        if($player->isOp())
            return;

        $terrain = TerrainManager::getPriorityTerrain($player->asPosition());

        if($terrain !== null){
            if(!$terrain->isSettingEnabled("use_command")) {
                $e->setCancelled(true);
                $e->getPlayer()->sendTip("§cKorzystanie z komend na tym terenie jest zablokowane!");
            }
        }
    }

    /**
     * @param PlayerCommandPreprocessEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function antylogoutCommand(PlayerCommandPreprocessEvent $e) : void {
        $player = $e->getPlayer();

        if($player->isOp() || $player->getLevel()->getName() !== ConfigUtil::PVP_WORLD)
            return;

        if(!isset(Main::$antylogout[$player->getName()]))
            return;

        $cmd = explode(" ", $e->getMessage())[0];
        $cmdMap = $this->getServer()->getCommandMap();
        $founded = false;

        if(($command = $cmdMap->getCommand(substr(strtolower($cmd), 1))) !== null) {
            if(in_array($command->getName(), ConfigUtil::ANTYLOGOUT_COMMANDS))
                $founded = true;

            foreach($command->getAliases() as $alias) {
                if(in_array($alias, ConfigUtil::ANTYLOGOUT_COMMANDS))
                    $founded = true;
            }

            if($founded) {
                $e->setCancelled(true);
                $player->sendMessage(MessageUtil::format("Nie mozesz uzyc tej komendy podczas antylogouta!"));
                return;
            }
        }
    }
}