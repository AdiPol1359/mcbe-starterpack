<?php

declare(strict_types=1);

namespace core\listeners\player;

use core\Main;
use core\tasks\async\ChatFormatAsyncTask;
use core\utils\MessageUtil;
use core\utils\PermissionUtil;
use core\utils\Settings;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\player\Player;

class PlayerChatListener implements Listener {

    /**
     * @param PlayerChatEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function chatOff(PlayerChatEvent $e) : void {

        $player = $e->getPlayer();

        if(!PermissionUtil::has($player, Settings::$PERMISSION_TAG . "chatoff") && !Settings::$CHAT) {
            $player->sendMessage(MessageUtil::format("Chat jest §eWYLACZONY§r§7!"));
            $e->cancel();
        }
    }

    public function blockCustomColors(PlayerChatEvent $e) : void {
        if(!PermissionUtil::has($e->getPlayer(), Settings::$PERMISSION_TAG."chat.format"))
            $e->setMessage(str_replace(["§", "&"], "", $e->getMessage()));
    }

    public function antiSpam(PlayerChatEvent $e) : void{
        $player = $e->getPlayer();

        if(PermissionUtil::has($player, Settings::$PERMISSION_TAG."antyspam"))
            return;

        if(!($user = Main::getInstance()->getUserManager()->getUser($player->getName())))
            return;

        if(!$user->canType()) {
            $e->cancel();
            $player->sendMessage(MessageUtil::format("Nastepna wiadomosc mozesz napisac za §e" . ($user->getLastChatMessage() - time()) . " §r§7sekund!"));
        } else {
            $user->setLastChatMessage(time() + Settings::$ANTI_SPAM);
        }
    }

    public function guildChat(PlayerChatEvent $e) {
        $player = $e->getPlayer();
        $msg = $e->getMessage();

        $playerGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($player->getName());

        if(!$playerGuild)
            return;

        if(!isset($msg[1]))
            return;

        if($msg[0] === "#" && $msg[1] !== "#") {
            $msg = substr($msg, 1);
            $e->cancel();

            foreach($playerGuild->getPlayers() as $guildPlayer){
                $guildMember = Main::getInstance()->getServer()->getPlayerExact($guildPlayer->getName());

                if(!$guildMember)
                    continue;

                $guildMember->sendMessage("§8[§a§lGILDIA§r§8] §r§7".$player->getName()."§r§8: §a".$msg);
            }
        }
    }

    public function allianceChat(PlayerChatEvent $e) {
        $player = $e->getPlayer();
        $msg = $e->getMessage();

        $playerGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($player->getName());

        if(!$playerGuild)
            return;

        if(!isset($msg[1]))
            return;

        if($msg[0] === "#" && $msg[1] === "#") {
            $server = Main::getInstance()->getServer();

            $msg = substr($msg, 2);
            $e->cancel();

            $players = [];

            foreach($playerGuild->getPlayers() as $guildPlayer){
                $guildMember = $server->getPlayerExact($guildPlayer->getName());

                if(!$guildMember)
                    continue;

                $players[] = $guildMember->getName();
            }

            foreach($playerGuild->getAlliances() as $alliance) {
                if(($allianceGuild = Main::getInstance()->getGuildManager()->getGuild($alliance)) === null)
                    continue;

                foreach($allianceGuild->getPlayers() as $allianceGuildPlayer) {
                    if($allianceGuildPlayer->getName() === $player->getName())
                        continue;

                    if(($alliancePlayer = $server->getPlayerExact($allianceGuildPlayer->getName())) !== null)
                        $players[] = $alliancePlayer->getName();
                }
            }

            foreach($players as $gPlayer) {
                if(!($sendPlayer = $server->getPlayerExact($gPlayer)))
                    continue;

                $sendPlayer->sendMessage("§8[§e§lSOJUSZ§r§8] §r§7" . $player->getName() . "§r§8: §e" . $msg);
            }
        }
    }

    /**
     * @param PlayerChatEvent $e
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function chatFormat(PlayerChatEvent $e) {
        $player = $e->getPlayer();
        $recipients = $e->getRecipients();

        $group = Main::getInstance()->getPlayerGroupManager()->getPlayer($player->getName())->getGroup();
        $e->cancel();

        if($group !== null && $group->getFormat() !== null) {

            $resultRecipients = [];
            $alliances = [];
            $members = [];
            $tag = null;

            if(($guild = Main::getInstance()->getGuildManager()->getPlayerGuild($player->getName()))) {
                foreach($guild->getAlliances() as $alliance) {
                    if(($allianceGuild = Main::getInstance()->getGuildManager()->getGuild($alliance))) {
                        foreach($allianceGuild->getPlayers() as $nick => $data)
                            $alliances[$nick] = $data;
                    }
                }

                $members = $guild->getPlayers();
                $tag = $guild->getTag();
            }

            foreach($recipients as $key => $recipient) {
                if(!$recipient instanceof Player) {
                    continue;
                }

                $resultRecipients[] = $recipient->getName();
            }

            $user = Main::getInstance()->getUserManager()->getUser($player->getName());

            $player->getServer()->getAsyncPool()->submitTask(new ChatFormatAsyncTask($resultRecipients, $user->getStatManager()->getStat(Settings::$STAT_POINTS), $members, $alliances, $tag ?? "", $group, $player->getName(), $group->getFormat(), $e->getMessage(), PermissionUtil::has($player, Settings::$PERMISSION_TAG."ignore.bypass")));
        }
    }

    public function adminLoggerMessages(PlayerChatEvent $e) : void {
        $player = $e->getPlayer();

        if(!PermissionUtil::has($player, Settings::$PERMISSION_TAG."adminLogger"))
            return;

        $adminLogger = Main::getInstance()->getAdminLoggerManager();

        if(($admin = $adminLogger->getAdminDataByName($player->getName())) === null)
            $adminLogger->createAdminData($player->getName());

        $adminLogger->getAdminDataByName($player->getName())->addMessage(1);
    }

    public function muteCheck(PlayerChatEvent $e) : void {
        $player = $e->getPlayer();

        if(Main::getInstance()->getMuteManager()->isMuted($player->getName())) {
            $player->sendMessage(MessageUtil::formatLines(Main::getInstance()->getMuteManager()->getMuteFormat(Main::getInstance()->getMuteManager()->getMuteNickInfo($player->getName())), "MUTE"));
            $e->cancel();
        }
    }
}