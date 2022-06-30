<?php

namespace core\listener\events;

use core\caveblock\CaveManager;
use core\fakeinventory\FakeInventoryAPI;
use core\fakeinventory\inventory\TradeInventory;
use core\listener\BaseListener;
use core\Main;
use core\manager\managers\bossbar\BossbarManager;
use core\manager\managers\SkinManager;
use core\manager\managers\SoundManager;
use core\manager\managers\StatsManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\entity\Entity;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;

class QuitListener extends BaseListener{

    public function quitMessage(PlayerQuitEvent $e) : void {
        $e->setQuitMessage("");
    }

    public function LogOutSpr(PlayerQuitEvent $e) : void {
        $player = $e->getPlayer();
        $nick = $player->getName();
        if(isset(Main::$sprawdzanie[$nick])) {
            unset(Main::$sprawdzanie[$nick]);

            foreach($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
                if($onlinePlayer->getLevel()->getName() !== ConfigUtil::LOBBY_WORLD)
                    $onlinePlayer->sendMessage(MessageUtil::formatLines(["Gracz o nicku §9§l{$nick}§r", "§7Wylogowal sie podczas sprawdzania!"]));
            }
        }
    }

    /**
     * @param PlayerQuitEvent $e
     * @priority MONITOR
     */
    public function onPlayerQuit(PlayerQuitEvent $e) : void {
        $player = $e->getPlayer();
        unset(Main::$callbacks[$player->getId()]);
    }

    public function bossbarOnQuit(PlayerQuitEvent $e) : void {
        $player = $e->getPlayer();
        if(BossbarManager::getBossbar($player) != null)
            BossbarManager::getBossbar($player)->hideFrom($player);
    }

    public function unregisterPlayer(PlayerQuitEvent $e) {
        Main::getGroupManager()->unregisterPlayer($e->getPlayer());
    }

    public function unLoadCaveOnQuit(PlayerQuitEvent $e) {

        $player = $e->getPlayer();

        foreach(CaveManager::getCaves($player->getName()) as $cave) {

            if($cave->getOnlinePlayers() <= 1){
                Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($cave) : void {
                    if($this->getServer()->isLevelLoaded(ConfigUtil::LEVEL.$cave->getName()))
                        $this->getServer()->unloadLevel($this->getServer()->getLevelByName(ConfigUtil::LEVEL . $cave->getName()));
                }), 20);
            }
        }
    }

    public function onQuitScoreboard(PlayerQuitEvent $e) : void{
        if(isset(Main::$sb[$e->getPlayer()->getName()]))
            unset(Main::$sb[$e->getPlayer()->getName()]);
    }

    public function onQuitTrade(PlayerQuitEvent $e) : void{

        $player = $e->getPlayer();

        if(!FakeInventoryAPI::isOpening($player))
            return;

        $gui = FakeInventoryAPI::getInventory($player->getName());

        if($gui instanceof TradeInventory) {

            if($gui->isCounting())
                $gui->resetCountDown();

            $items = $gui->getContents(false);

            $senderItems = [];
            $playerItems = [];

            foreach($items as $slot => $item){
                if(in_array($slot, $gui::$player1area))
                    $senderItems[] = $item;

                if(in_array($slot, $gui::$player2area))
                    $playerItems[] = $item;
            }

            foreach($senderItems as $senderItem)
                $gui::$owner->getInventory()->addItem($senderItem);

            foreach($playerItems as $playerItem)
                $gui::$tradePlayer->getInventory()->addItem($playerItem);


            foreach([$gui::$owner, $gui::$tradePlayer] as $tradePlayer){
                if(!$tradePlayer)
                    continue;

                $gui->closeFor($tradePlayer);
                $tradePlayer->addTitle("§l§cANULOWANO WYMIANE!");
            }
        }
    }

    public function onQuitWings(PlayerQuitEvent $e) : void {
        $player = $e->getPlayer();
        SkinManager::removePlayerSkin($player);
    }

    public function playerParticleCheck(PlayerQuitEvent $e) : void {
        $player = $e->getPlayer();

        $user = UserManager::getUser($player->getName());

        if(!$user)
            return;

        if(($key = array_search($player->getName(), Main::$playerParticles)) !== false)
            unset(Main::$playerParticles[$key]);
    }

    public function adminQuit(PlayerQuitEvent $e) : void {
        $player = $e->getPlayer();

        if($player->hasPermission(ConfigUtil::PERMISSION_TAG . "administrator")) {

            foreach(Main::$adminsOnline as $key => $admin) {
                if($player->getName() === $admin) {
                    unset(Main::$adminsOnline[$key]);
                    break;
                }
            }
        }
    }

    /**
     * @param PlayerQuitEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function userPlayInfo(PlayerQuitEvent $e) : void {

        $player = $e->getPlayer();

        $user = UserManager::getUser($player->getName());

        if(!$user)
            return;

        $user->addToStat(StatsManager::TIME_PLAYED, (time() - $user->getStat(StatsManager::LAST_PLAYED)));
    }

    public function antylogoutOnQuit(PlayerQuitEvent $e) : void {

        $player = $e->getPlayer();

        if(isset(Main::$antylogout[$player->getName()])) {

            if(!Main::$antylogout[$player->getName()]["lastAttacker"])
                return;

            $damagerName = Main::$antylogout[$player->getName()]["lastAttacker"];

            $assistPlayerData = ["nick" => "", "damage" => 0.0];

            foreach(Main::$antylogout[$player->getName()]["assists"] as $nick => $damage) {

                if($nick === $damagerName || $nick === $player->getName())
                    continue;

                if($assistPlayerData["nick"] === "") {
                    $assistPlayerData["nick"] = $nick;
                    $assistPlayerData["damage"] = $damage;
                }else{
                    if($damage > $assistPlayerData["damage"]) {
                        $assistPlayerData["nick"] = $nick;
                        $assistPlayerData["damage"] = $damage;
                    }
                }
            }

            $damager = self::getServer()->getPlayerExact($damagerName);

            UserManager::getUser($assistPlayerData["nick"]) === "" ? $assistUser = null : $assistUser = UserManager::getUser($assistPlayerData["nick"]);

            $assistUser !== null ? $assistPlayer = self::getServer()->getPlayerExact($assistUser->getName()) : $assistPlayer = null;

            $playerUser = UserManager::getUser($player->getName());
            $damagerUser = UserManager::getUser($damagerName);

            if(!$playerUser || !$damagerUser)
                return;

            $assistFormat = "";

            if($assistPlayerData["nick"] !== "")
                $assistFormat = "§7 z pomoca §9".$assistPlayerData["nick"];

            if(isset(Main::$antylogout[$player->getName()]))
                unset(Main::$antylogout[$player->getName()]);

            $player->addTitle("§l§cSMIERC","§r§8(§7" . $damagerName . "§8)", 20, 40, 20);

            $addMoney = (ConfigUtil::KILL_MONEY + (0.14 * (($ks = $damagerUser->getStat(StatsManager::KILL_STREAK)) >= 10 ? 10 : $ks)));

            if($damagerUser->hasKilled($player->getName(), $player->getAddress()))
                $addMoney = 0;
            else
                $damagerUser->addToStat(StatsManager::KILL_STREAK, 1);

            if($damager) {
                SoundManager::addSound($damager, $damager->asPosition(), "ambient.weather.lightning.impact", 1);
                $damager->addTitle("§l§aZABOJSTWO", "§r§7" . $player->getName() . " §8(§a+".$addMoney."§7zl§8)", 20, 40, 20);
            }

            $damagerUser->addPlayerMoney($addMoney);
            $damagerUser->addKilledPlayer($player->getName(), $player->getAddress());
            $damagerUser->addToStat(StatsManager::KILLS, 1);

            if($assistPlayer) {
                SoundManager::addSound($damager, $damager->asPosition(), "ambient.weather.lightning.impact", 1);
                $assistPlayer->addTitle("§l§eASYSTA", "§r§8(§7" . $player->getName() . "§8)", 20, 40, 20);
            }

            if($assistUser)
                $assistUser->addToStat(StatsManager::ASSISTS, 1);

            $playerUser->addToStat(StatsManager::DEATHS, 1);
            $playerUser->setStat(StatsManager::KILL_STREAK, 0);

            $pk = new AddActorPacket();
            $pk->type = "minecraft:lightning_bolt";
            $pk->entityRuntimeId = Entity::$entityCount++;
            $pk->position = $player->asVector3();

            $player->getServer()->broadcastPacket($player->getLevel()->getPlayers(), $pk);

            SoundManager::addSound($player, $player->asPosition(), "ambient.weather.lightning.impact", 1);

            foreach(self::getServer()->getOnlinePlayers() as $onlinePlayer) {
                if($onlinePlayer->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
                    continue;

                $onlinePlayer->sendMessage(MessageUtil::format("§9" . $player->getName() . " §7zostal zabity przez §9" . $damagerName . " §8(§9" . $addMoney . "§7zl§8)" . $assistFormat));
            }
        }
    }
}