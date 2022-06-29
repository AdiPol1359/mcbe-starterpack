<?php

namespace core\listener\events;

use core\form\forms\JoinForm;
use core\listener\BaseListener;
use core\Main;
use core\manager\managers\BanManager;
use core\manager\managers\bossbar\BossbarManager;
use core\manager\managers\CpsManager;
use core\manager\managers\pet\PetManager;
use core\manager\managers\quest\QuestManager;
use core\manager\managers\SettingsManager;
use core\manager\managers\SkinManager;
use core\manager\managers\StatsManager;
use core\manager\managers\WhitelistManager;
use core\manager\managers\wing\WingsManager;
use core\permission\managers\NameTagManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use core\util\utils\SkinUtil;
use pocketmine\entity\Skin;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\WrittenBook;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class JoinListener extends BaseListener{

    public function loadUser(PlayerJoinEvent $e) {
        $player = $e->getPlayer();

        if(!UserManager::userExists($player->getName()))
            UserManager::createUser($player);
    }

    public function setDateBase(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();

        QuestManager::registerPlayer($player->getName());
    }

    public function joinMessage(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();
        $e->setJoinMessage("");

        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
            function() use ($player) : void {
                if(!$player)
                    return;

                $players = count($player->getServer()->getOnlinePlayers());
                $player->sendMessage(MessageUtil::formatLines(["Ilosc graczy na serwerze: §l§9$players", "Nasz Discord: §l§9" . ConfigUtil::DISCORD_INVITE, "Nasza strona www: §l§9DarkMoonPE.PL"]));
                if($player->hasPlayedBefore())
                    $player->addTitle("§7Witaj ponownie!", "§l§9" . $player->getName() . "§r§7!");
                else
                    $player->addTitle("§7Witaj nowy graczu!", "§l§9" . $player->getName() . "§r§7!" . "\n" . "§8(§7Przeczytaj ksiazke§8)");
            }
        ), 3);
    }

    public function isBanedOnJoin(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();
        if(BanManager::isBanned($player->getName())) {
            $player->teleport($this->getServer()->getLevelByName(ConfigUtil::LOBBY_WORLD)->getSafeSpawn());
            $player->sendMessage(MessageUtil::customFormat(BanManager::getBannedMessage($player), "§l§9JESTES ZBANOWANY!"));
            foreach($player->getServer()->getOnlinePlayers() as $p) {
                $player->hidePlayer($p);
                $p->hidePlayer($player);
            }
        } else {
            if($player->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
                $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());

            foreach($this->getServer()->getOnlinePlayers() as $p) {
                $player->showPlayer($p);
                $p->showPlayer($player);
            }
        }
    }

    public function sendFormOnJoin(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();

        if(!$player->hasPlayedBefore())
            $player->sendForm(new JoinForm());
    }

    public function variableOnJoin(PlayerJoinEvent $e) {
        Main::$tp[$e->getPlayer()->getName()] = [];
        Main::$request[$e->getPlayer()->getName()] = [];
    }

    public function SettingsOnJoin(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();

        $userManager = UserManager::getUser($player->getName());

        $cords = $userManager->isSettingEnabled(SettingsManager::COORDINATES);
        $quest = $userManager->isSettingEnabled(SettingsManager::QUEST_BOSSBAR);

        Main::$sb[$player->getName()] = true;

        if($cords) {
            $pk = new GameRulesChangedPacket();
            $pk->gameRules = ["showcoordinates" => [1, true]];
            $player->dataPacket($pk);
        } else {
            $pk = new GameRulesChangedPacket();
            $pk->gameRules = ["showcoordinates" => [0, false]];
            $player->dataPacket($pk);
        }

        if($quest)
            QuestManager::send($player);
        elseif(BossbarManager::getBossbar($player) !== null)
            BossbarManager::unsetBossbar($player);
    }

    public function permissionsOnJoin(PlayerJoinEvent $e) {
        $player = $e->getPlayer();
        $groupManager = Main::getInstance()->getGroupManager();
        $groupManager->registerPlayer($player);

        if(!$groupManager->getPlayer($player->getName())->hasGroup()) {
            if($groupManager->getDefaultGroup() == null) {
                $player->sendMessage(MessageUtil::format("Default group not found!"));
                return;
            }
            $groupManager->getPlayer($player->getName())->addDefaultGroup();
        }
    }

    public function updateNametagOnJoin(PlayerJoinEvent $e) {
        NameTagManager::updateNameTag($e->getPlayer());
    }

    public function addPlayerToArray(PlayerJoinEvent $e) {
        Main::$request[$e->getPlayer()->getName()] = [];
    }

    public function onJoinBook(PlayerJoinEvent $e) {

        $player = $e->getPlayer();

        if(!$player->hasPlayedBefore()) {

            $book = new WrittenBook();

            $book->setTitle("§l§9Witaj na DarkMoonPE");
            $book->setCustomName("§l§9DarkMoonPE.PL" . "\n" . "§r§8(§7Wazne informacje§8)");

            $book->setPageText(0, "§l§9Witaj na DarkMoonPE§r\n\n§l§k1§r§l§8Informacje§r§l§k1\n\n§r§8Server jest oparty na trybie caveblock ktorego jeszcze nie bylo na polskich serwerach MCBE dlatego jesli spotkasz i zglosisz bledy mozesz dostac nagrode! Kontakt: §l§7(§9iDarkQ#0001§7)");
            $book->setPageText(1, "\n\n§l§k1§r§l§8Jak grac?§r§l§k1§r§8\n\nNa poczatku aby moc cokolwiek robic trzeba dolaczyc lub stworzyc swoja jaskinie poprzez wpisanie komendy §l§8/§9caveblock §r§8wybranie opcji stworz jaskinie i podaniu informacji");
            $book->setPageText(2, "§r§8nastepnie warto byloby sie przeteleportowac na swoja nowo stworzona jaskinie, w tym celu otwierasz menu zarzadzania jaskinia, wybierasz swoja jaskinie. Nastepnie klikasz teleportacja do jaskini, teraz tak naprawde mozesz robic co tylko zechcesz, §l§9KOPAC§r§8, §l§9BUDOWAC§r§8, §l§9TWORZYC FARMY");
            $book->setPageText(3, "§r§8Albo wykonywac §l§9QUESTY§r§8 za ktore otrzymujesz rozne nagrody, zarzadzac nimi mozesz klikajac na §l§9QUEST MASTERA§r§8 w jaskini.");
            $book->addPage(0);
            $book->addPage(1);

            $player->getInventory()->addItem($book);
        }
    }

    public function teleportToLobby(PlayerJoinEvent $e) {
        $player = $e->getPlayer();

        if(!WhitelistManager::isWhitelistEnabled() || $player->hasPermission(ConfigUtil::PERMISSION_TAG . "whitelist") || $player->isOp()) {
            if($player->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
                $player->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());

            return;
        }

        $player->teleport($player->getServer()->getLevelByName(ConfigUtil::LOBBY_WORLD)->getSafeSpawn());

        $player->setHealth($player->getMaxHealth());
        $player->setFood($player->getMaxFood());

        foreach($player->getServer()->getOnlinePlayers() as $p) {
            $player->hidePlayer($p);
            $p->hidePlayer($player);
        }
    }

    public function setCpsOnJoin(PlayerJoinEvent $e) : void {
        CpsManager::setDefaultData($e->getPlayer());
    }

    public function onJoinWings(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();
        $skin = $player->getSkin();

        $newSkin = new Skin($skin->getSkinId(), SkinUtil::skinImageToBytes(SkinManager::getPlayerSkinImage($player->getName())), "", SkinManager::getDefaultGeometryName(), SkinManager::getDefaultGeometryData());

        $wings = WingsManager::getPlayerWings($player->getName());

        if($wings !== null)
            WingsManager::setWings($player, $wings);
        else {
            $player->setSkin($newSkin);
            $player->sendSkin();
        }

        SkinManager::setPlayerSkin($player, $newSkin);
    }

    public function spawnPet(PlayerJoinEvent $e) : void{
        $player = $e->getPlayer();
        $user = UserManager::getUser($player->getName());

        if(!$user)
            return;

        if(($pet = $user->getSelectedPet()) !== null)
            PetManager::spawnPet($pet, $player, "§7Zwierzak gracza: §l§9".$player->getName());
    }

    public function playerParticleCheck(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();

        $user = UserManager::getUser($player->getName());

        if(!$user)
            return;

        if($user->isSettingEnabled(SettingsManager::PLAYER_PARTICLES))
            Main::$playerParticles[] = $player->getName();
    }

    public function adminJoin(PlayerJoinEvent $e) : void {
        $player = $e->getPlayer();

        if($player->hasPermission(ConfigUtil::PERMISSION_TAG . "administrator") && array_search($player->getName(), Main::$adminsOnline) === false)
            Main::$adminsOnline[] = $player->getName();
    }

    public function userPlayInfo(PlayerJoinEvent $e) : void {

        $player = $e->getPlayer();

        $user = UserManager::getUser($player->getName());

        if(!$user)
            return;

        $user->setStat(StatsManager::LAST_PLAYED, time());
    }
}