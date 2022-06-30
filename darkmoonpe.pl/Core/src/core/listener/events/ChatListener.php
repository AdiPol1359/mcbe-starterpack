<?php

namespace core\listener\events;

use core\caveblock\CaveManager;
use core\listener\BaseListener;
use core\Main;
use core\manager\managers\BanManager;
use core\manager\managers\WhitelistManager;
use core\manager\managers\MuteManager;
use core\manager\managers\SoundManager;
use core\permission\managers\ChatManager;
use core\permission\managers\FormatManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;

class ChatListener extends BaseListener{

    /**
     * @param PlayerChatEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function caveChat(PlayerChatEvent $e) {
        $player = $e->getPlayer();
        $msg = $e->getMessage();

        if(!CaveManager::isInCave($player))
            return;

        $cave = CaveManager::getCave($player);

        if(!$cave->isMember($player->getName()))
            return;

        if(!isset($msg[1]))
            return;

        if($msg[0] === "!" && $msg[1] !== "!") {
            $msg = substr($msg, 1);
            $e->setRecipients([]);
            $e->setFormat("caveChat");
            foreach($cave->getPlayers() as $cavePlayerNick => $perms){

                $cavePlayer = $this->getServer()->getPlayerExact($cavePlayerNick);

                if(!$cavePlayer)
                    continue;

                if(CaveManager::getCave($cavePlayer) !== $cave)
                    return;

                $cavePlayer->sendMessage("§l§8[§9JASKINIA§8] §r§7".$player->getName()."§8: §7".$msg);
            }
        }
    }

    /**
     * @param PlayerChatEvent $e
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function disableChat(PlayerChatEvent $e) : void{
        $player = $e->getPlayer();

        if(!$player->isOp() && !$player->hasPermission(ConfigUtil::PERMISSION_TAG."chatoff") && Main::$chatoff && $e->getFormat() !== "caveChat") {
            $player->sendMessage(MessageUtil::format("Czat jest §l§9wylaczony§r§7!"));
            $e->setCancelled(true);
        }
    }

    /**
     * @param PlayerChatEvent $e
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function AntiSpam(PlayerChatEvent $e) : void {

        if($e->isCancelled())
            return;

        $player = $e->getPlayer();
        $nick = $player->getName();

        if($player->hasPermission(ConfigUtil::PERMISSION_TAG . "spam"))
            return;

        if($e->getFormat() === "caveChat")
            return;

        isset(Main::$lastChatMsg[$nick]) ? $time = Main::$lastChatMsg[$nick] : $time = 0;

        $cooldown = 5;
        if($player->hasPermission(ConfigUtil::PERMISSION_TAG . "cooldown.3"))
            $cooldown = 3;

        if(time() - $time < $cooldown) {
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Nastepna wiadomosc mozesz napisac za §9§l" . ($cooldown - (time() - $time)) . " §r§7sekund!"));
        } else
            Main::$lastChatMsg[$nick] = time();
    }

    public function isMutedOnChat(PlayerChatEvent $e) : void {
        $player = $e->getPlayer();
        $nick = $player->getName();
        if(MuteManager::isMuted($nick)) {
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::formatLines(MuteManager::getMutedMessage($player)));
            SoundManager::addSound($player, $player->asVector3(), "random.pop2");
        }
    }

    public function receiveMessage(PlayerChatEvent $e) : void {
        $recipients = $e->getRecipients();
        foreach($recipients as $key => $recipient) {
            if($recipient instanceof Player) {
                if(!WhitelistManager::isInWhitelist($recipient->getName()) && WhitelistManager::isWhitelistEnabled() && !$recipient->isOp() && !$recipient->hasPermission(ConfigUtil::PERMISSION_TAG."whitelist") || BanManager::isBanned($recipient->getName()))
                    unset($recipients[$key]);
            }
        }
        $e->setRecipients($recipients);
    }

    /**
     * @param PlayerChatEvent $e
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function chatFormat(PlayerChatEvent $e) {
        $player = $e->getPlayer();
        $groupManager = Main::getInstance()->getGroupManager();
        $recipients = $e->getRecipients();

        $group = $groupManager->getPlayer($player->getName())->getGroup();

        if($group != null && $group->getFormat() != null) {
            $format = FormatManager::getFormat($player, $group->getFormat(), $e->getMessage());

            foreach($recipients as $key => $recipient) {
                if(!$recipient instanceof Player)
                    continue;

                if($recipient->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
                    unset($recipients[$key]);
            }

            $e->setRecipients($recipients);

            if(!ChatManager::isChatPerWorld())
                $e->setFormat($format);
            else {
                $e->setCancelled(true);

                foreach($player->getLevel()->getPlayers() as $p)
                    $p->sendMessage($format);
            }
        }
    }

    public function antiLink(PlayerChatEvent $e) : void {
        $player = $e->getPlayer();
        if($player->hasPermission(ConfigUtil::PERMISSION_TAG . "link"))
            return;

        $message = $e->getMessage();
        $links = ["https://", "http://", ".pl", ".com", ".eu", "www."];
        foreach($links as $link)
            if(strpos($message, $link)) {
                $e->setCancelled(true);
                $player->sendMessage(MessageUtil::format("Nie masz uprawnien aby wysylac linki!"));
                return;
            }
    }

    public function removeColors(PlayerChatEvent $e) : void {
        $e->setMessage(str_replace(["§", "&"], "", $e->getMessage()));
    }

    public function lobbyChatBlock(PlayerChatEvent $e) {
        if($e->getPlayer()->isOp())
            return;

        if($e->getPlayer()->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
            $e->setCancelled(true);
    }

    public function blackList(PlayerChatEvent $e) : void{

        $message = $e->getMessage();

        foreach(ConfigUtil::BLACK_LIST_WORDS as $word) {
            if(strpos($message, $word) !== null)
                $message = str_replace($word, str_repeat("*", strlen($word)), $message);
        }

        $e->setMessage($message);
    }

    public function ignorePlayers(PlayerChatEvent $e) : void{

        $recipients = [];
        $player = $e->getPlayer();

        foreach($this->getServer()->getOnlinePlayers() as $p){
            if(!isset(Main::$ignore[$p->getName()])){
                $recipients[] = $p;
                continue;
            }

            if(($key = array_search($player->getName(), Main::$ignore[$p->getName()])) === false)
                $recipients[] = $p;
        }

        foreach($e->getRecipients() as $key => $recipient){
            if(!$recipient instanceof Player)
                $recipients[] = $recipient;
        }

        $e->setRecipients($recipients);
    }
}