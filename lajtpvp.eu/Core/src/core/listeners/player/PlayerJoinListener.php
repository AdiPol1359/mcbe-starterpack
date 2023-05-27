<?php

declare(strict_types=1);

namespace core\listeners\player;

use core\guilds\GuildPlayer;
use core\Main;
use core\managers\BorderPlayerManager;
use core\users\CorePlayer;
use core\utils\MessageUtil;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\SkinUtil;
use core\utils\SoundUtil;
use core\utils\TimeUtil;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Skin;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;

class PlayerJoinListener implements Listener {

    public function messageOnJoin(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();
        $e->setJoinMessage("");

        $player->sendMessage(MessageUtil::formatLines([
            "Witaj §e" . $player->getName() . "§r§7!",
            "Graczy online: §e" . count($player->getServer()->getOnlinePlayers()),
            "Discord: §e" . Settings::$DISCORD_LINK,
            "Strona www: §e" . Settings::$SERVER_NAME
        ]));
    }

    public function coordinatesOnJoin(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();

        $pk = GameRulesChangedPacket::create(["showcoordinates" => new BoolGameRule(true, true)]);
        $player->getNetworkSession()->sendDataPacket($pk);
    }

    public function onJoinWings(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();

        if(($wings = Main::getInstance()->getWingsManager()->getPlayerWings($player->getName())) !== null) {
            Main::getInstance()->getWingsManager()->setWings($player, $wings);
        }

        Main::getInstance()->getSkinManager()->setPlayerSkin($player, $player->getSkin());
    }

    public function startGame(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();

        if(!$player->hasPlayedBefore()) {
            $user = Main::getInstance()->getUserManager()->getUser($player->getName());

            if(!$user)
                return;

            $user->startSafeGame();
        }
    }

    public function addAdminLogger(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();

        if(!PermissionUtil::has($player, Settings::$PERMISSION_TAG."adminLogger"))
            return;

        if(($admin = Main::getInstance()->getAdminLoggerManager()->getAdminDataByName($player->getName())) === null)
            Main::getInstance()->getAdminLoggerManager()->createAdminData($player->getName());
    }

    public function lastJoinStat(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        if(!$user)
            return;

        $user->getStatManager()->setStat(Settings::$STAT_LAST_JOIN_TIME, time());
    }

    public function nightVision(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();
        $effect = $player->getEffects();

        if(!$effect->has(VanillaEffects::NIGHT_VISION()))
            $effect->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 2147483647, 0, false));
    }

    public function incognitoOnJoin(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        if(!$user)
            return;

        if($user->getIncognitoManager()->getIncognitoData(Settings::$DATA_SKIN))
            $player->setSkin(new Skin($player->getSkin()->getSkinId(), SkinUtil::getSkinFromPath(Main::getInstance()->getDataFolder() . "default/incognito.png"), "", Main::getInstance()->getSkinManager()->getDefaultGeometryName(), Main::getInstance()->getSkinManager()->getDefaultGeometryData()));

        $player->sendSkin();
    }

    public function infoOnJoin(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();
        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        $info = false;

        if(($guild = Main::getInstance()->getGuildManager()->getPlayerGuild($player->getName())) !== null) {
            if($guild->getExpireTime() <= (time() + 60 * 60 * 24 * 3)) {
                $guildPlayer = $guild->getPlayer($player->getName());
                if($guildPlayer->getRank() === GuildPlayer::OFFICER || $guildPlayer->getRank() === GuildPlayer::LEADER) {
                    if(!Main::getInstance()->getGuildManager()->existsGuild($guild->getTag()))
                        return;

                    $info = true;
                    $player->sendMessage(MessageUtil::format("§cTWOJA GILDIA WYGASA ZA " . strtoupper(TimeUtil::convertIntToStringTime(($guild->getExpireTime() - time()), "§4", "§c")) . " §8(§7/§epanel§8)"));
                }
            }

            if(($war = Main::getInstance()->getWarManager()->getWar($guild->getTag())) !== null) {
                if(!$war->hasEnded()) {
                    $info = true;
                    $player->sendMessage(MessageUtil::format("§cTWOJA GILDIA MA WOJNE ZA " . strtoupper(TimeUtil::convertIntToStringTime(($war->getStartTime() - time()), "§4", "§c")) . " §8(§7/§ewojna§8)"));
                    SoundUtil::addSound([$player], $player->getPosition(), "random.explode");
                }
            }
        }

        if($user) {
            if(!empty($user->getServicesManager()->getServicesToCollect())) {
                $player->sendMessage(MessageUtil::format("§eMASZ §7".count($user->getServicesManager()->getServicesToCollect())." §eUSLUG DO ODEBRANIA POD §8/§7itemshop"));
                $info = true;
            }
        }

        if($info) {
            $player->sendTitle("§l§eSPOJRZ NA CZAT", "", 10, 20 * 3, 10);
        }
    }

    public function setCpsOnJoin(PlayerJoinEvent $e) : void {
        Main::getInstance()->getCpsManager()->setDefaultData($e->getPlayer());
    }

    public function pexExpire(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();

        $playerGroup = Main::getInstance()->getPlayerGroupManager()->getPlayer($player->getName());

        if(!$playerGroup)
            return;

        foreach($playerGroup->getPlayerGroups() as $groupName => $expiryDate) {
            if($expiryDate <= time() && $expiryDate !== -1)
                $playerGroup->removeGroup($groupName);
        }

        foreach($playerGroup->getPlayerPermissions() as $permissionName => $expiryDate) {
            if($expiryDate <= time() && $expiryDate !== -1)
                $playerGroup->removePermission($permissionName);
        }
    }

    public function onJoinBorder(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();
        $position = $player->getPosition();

        $x = $position->getFloorX();
        $z = $position->getFloorZ();

        $border = Settings::$BORDER_DATA["border"];

        if(abs($x) >= ($border + 20) || abs($z) >= ($border + 20) && Settings::$BORDER_DATA["damage"])
            BorderPlayerManager::removePlayer($player->getName());
        else if(!BorderPlayerManager::isInBorder($player->getName())) {
            BorderPlayerManager::addPlayer($player);
        }
    }

    public function spawnSnow(PlayerJoinEvent $e) : void {
//        $e->getPlayer()->getNetworkSession()->sendDataPacket(LevelEventPacket::create(
//            eventId: LevelEvent::START_RAIN,
//            eventData: 100000, // Around 24 hours of snowing, hope player won't be afking that long time
//            position: null
//        ));
    }

    public function renderDistancePacket(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();
        $player->setViewDistance($player->getServer()->getViewDistance());
    }

    public function commandsOnJoin(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();

        if($player instanceof CorePlayer) {
            $player->syncAvailableCommands();
        }
    }
}