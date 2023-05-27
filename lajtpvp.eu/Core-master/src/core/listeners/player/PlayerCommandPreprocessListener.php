<?php

declare(strict_types=1);

namespace core\listeners\player;

use core\Main;
use core\utils\MessageUtil;
use core\utils\PermissionUtil;
use core\utils\Settings;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class PlayerCommandPreprocessListener implements Listener {

    /**
     * @param PlayerCommandPreprocessEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function protectCommand(PlayerCommandPreprocessEvent $e) : void {
        $player = $e->getPlayer();

        if($player->getServer()->isOp($player->getName()))
            return;

        $terrain = Main::getInstance()->getTerrainManager()->getPriorityTerrain($player->getPosition());

        if($terrain !== null) {
            if(!$terrain->isSettingEnabled(Settings::$TERRAIN_USE_COMMAND)) {
                $e->cancel();
            }
        }
    }

    public function UnknownCommandMessage(PlayerCommandPreprocessEvent $e) {
        $player = $e->getPlayer();

        if($e->getMessage()[0] !== '/')
            return;

        $cmd = explode(" ", $e->getMessage())[0];

        $commandMap = $player->getServer()->getCommandMap();

        if($commandMap->getCommand(substr(strtolower($cmd), 1)) == null) {
            $e->cancel();
            $player->sendMessage(MessageUtil::format("Nieznana komenda! Uzyj §e/pomoc §r§7aby zobaczyc liste dostepnych komend"));
            return;
        }

        $e->setMessage(strtolower($cmd) . str_replace($cmd, "", $e->getMessage()));
    }

    public function lobbyCommandsBlock(PlayerCommandPreprocessEvent $e) {
        $player = $e->getPlayer();

        if($player->getServer()->isOp($player->getName())) {
            return;
        }

        if($player->getWorld()->getDisplayName() === Settings::$LOBBY_WORLD)
            $e->cancel();
    }

    public function CommandTracker(PlayerCommandPreprocessEvent $e) : void {
        if($e->getMessage()[0] !== '/') {
            return;
        }

        Main::getInstance()->getLogger()->info("§l§8[§7LOG§8] §r§e" . $e->getPlayer()->getName() . " §l§7» " . $e->getMessage());
    }

    public function verifyBlock(PlayerCommandPreprocessEvent $e) : void {
        $player = $e->getPlayer();
        $command = explode(" ", strtolower($e->getMessage()));

        if(isset(Settings::$VERIFY[$player->getName()])) {
            if(!($command[0] === "/msg" || $command[0] === "/r")) {
                $e->cancel();
                $player->sendMessage(MessageUtil::format("Nie mozesz uzyc tej komendy poniewaz jestes sprawdzany!"));
            }
        }
    }

    /**
     * @param PlayerCommandPreprocessEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function antiLogoutCommand(PlayerCommandPreprocessEvent $e) : void {
        $player = $e->getPlayer();

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        if(!$user)
            return;

        if(PermissionUtil::has($player, Settings::$PERMISSION_TAG."antylogout.use.commands"))
            return;

        if(!$user->hasAntyLogout())
            return;

        $cmd = explode(" ", $e->getMessage())[0];
        $cmdMap = $player->getServer()->getCommandMap();
        $playerCommand = substr(strtolower($cmd), 1);
        $founded = false;

        foreach($cmdMap->getCommands() as $serverCommand) {
            if($playerCommand !== $serverCommand->getName() && !in_array($playerCommand, $serverCommand->getAliases())) {
                continue;
            }

            if(in_array($serverCommand->getName(), Settings::$ANTYLOGOUT_COMMANDS)) {
                $founded = true;
                break;
            }

            foreach($serverCommand->getAliases() as $alias) {
                if(in_array($alias, Settings::$ANTYLOGOUT_COMMANDS)) {
                    $founded = true;
                    break;
                }
            }
        }

        if($founded) {
            $e->cancel();
            $player->sendMessage(MessageUtil::format("Nie mozesz uzyc tej komendy podczas antylogouta!"));
        }
    }

    public function lockCheck(PlayerCommandPreprocessEvent $e) : void {
        $player = $e->getPlayer();

        if($player->getServer()->isOp($player->getName()))
            return;

        $cmd = explode(" ", $e->getMessage())[0];
        $playerCommand = substr(strtolower($cmd), 1);

        if(Main::getInstance()->getCommandLockManager()->isLocked($playerCommand)) {
            $player->sendMessage(MessageUtil::format("Ta komenda jest zablokowana"));
            $e->cancel();
        }
    }
}