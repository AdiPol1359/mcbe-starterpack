<?php

namespace core;

use core\anticheat\AntiCheatManager;
use core\block\BlockManager;
use core\caveblock\CaveManager;
use core\command\CommandManager;
use core\entity\EntityManager;
use core\generator\GeneratorManager;
use core\item\EnchantManager;
use core\item\ItemManager;
use core\listener\ListenerManager;
use core\manager\BaseManager;

use core\manager\managers\{
    BanManager,
    CobblestoneManager,
    DropManager,
    hazard\HazardManager,
    MoneyManager,
    MuteManager,
    MySQLManager,
    particle\ParticleManager,
    RecipeManager,
    ServerManager,
    SettingsManager,
    StatsManager,
    terrain\TerrainManager,
    WarpManager};

use core\manager\managers\market\MarketManager;
use core\manager\managers\pet\PetManager;
use core\manager\managers\privatechest\ChestManager;
use core\manager\managers\quest\QuestManager;
use core\manager\managers\service\ServicesManager;
use core\manager\managers\skill\SkillManager;
use core\manager\managers\SkinManager;
use core\manager\managers\wing\WingsManager;
use core\permission\group\GroupManager;
use core\permission\provider\SQLite3Provider;
use core\task\TaskManager;
use core\task\tasks\MySQLSaveAsyncTask;
use core\tile\TileManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use core\util\utils\FileUtil;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use SQLite3;

class Main extends PluginBase {

    private static self $instance;
    private static CaveManager $caveManager;
    private static SQLite3 $db;
    private static Config $whitelist;
    private static array $administration;
    private static Config $adminLog;
    private static Config $cfg;
    private static GroupManager $groupManager;
    private static Config $settings;
    private static SQLite3Provider $provider;
    private static Config $group;
    private static Config $event;
    private static Config $magicCase;

    public static bool $chatoff = false;

    public static array $adminsOnline = [];

    public static array $services;
    public static array $quest = [];
    public static array $skills = [];

    public static array $caveNames = [];
    public static array $selectedPlayer = [];
    public static array $sPlayer = [];
    public static array $selectedRequest = [];
    public static array $sb = [];
    public static array $off = [];
    public static array $lastChatMsg = [];
    public static array $lastCmd = [];
    public static array $lastPosition = [];

    public static array $sprawdzanie = [];
    public static array $tp = [];
    public static array $msg = [];
    public static array $callbacks = [];
    public static array $request = [];
    public static array $tradeRequests = [];
    public static array $ignore = [];
    public static array $invSeePlayers = [];
    public static array $playerParticles = [];
    public static array $vanish = [];
    public static array $antylogout = [];
    public static array $teleportPlayers = [];

    public function onLoad() : void {
        BaseManager::init();
        GeneratorManager::init();
    }

    public function onEnable() : void {

        date_default_timezone_set('Europe/Warsaw');

        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder()."/logs");
        @mkdir($this->getDataFolder()."/data/backup");
        self::$instance = $this;

        FileUtil::copyFolder((dirname(__DIR__, 2)) . '/resources/caves/', $this->getDataFolder());

        $this->saveResource("data/quest.json");
        $this->saveResource("data/groups.yml");
        $this->saveResource("data/settings.yml");
        $this->saveResource("data/shop.yml");
        $this->saveResource("data/administration.json");
        $this->saveResource("data/service.json");
        $this->saveResource("data/skill.json");
        $this->saveResource("data/event.json");

        if(!is_dir($this->getDataFolder() . "data"))
            mkdir($this->getDataFolder() . "data");

        if(!is_dir($this->getDataFolder() . "data/database"))
            mkdir($this->getDataFolder() . "data/database");

        $this->saveDefaultConfig();

        @mkdir($this->getDataFolder() . "wings");
        @mkdir($this->getDataFolder() . "playersSkins");

        self::$db = new SQLite3($this->getDataFolder() . 'data/database/DataBase.db');
        self::$cfg = new Config($this->getDataFolder() . "data/config.json", Config::JSON);
        self::$adminLog = new Config($this->getDataFolder() . 'data/adminlog.txt', Config::ENUM);
        self::$magicCase = new Config($this->getDataFolder(). 'data/magic_case.yml', Config::YAML);

        if(!self::$cfg->get("wiadomosci"))
            self::$cfg->set("wiadomosci", ["§9§lDarkMoonPE §7»§r§7 Przyklad"]);

        if(!self::$cfg->get("sprawdzarka"))
            self::$cfg->set("sprawdzarka", ["x" => null,
                "y" => null,
                "z" => null
            ]);

        self::$whitelist = new Config($this->getDataFolder() . "data/whitelist.json", Config::JSON, [
            "status" => false,
            "date" => null,
            "players" => []
        ]);

        self::$event = new Config($this->getDataFolder() . "data/event.json", Config::JSON, [
            "players" => []
        ]);

        self::$cfg->save();

        self::$group = new Config($this->getDataFolder() . 'data/groups.yml', Config::YAML);
        self::$provider = new SQLite3Provider();
        self::$groupManager = new GroupManager();
        self::$settings = new Config($this->getDataFolder() . 'data/settings.yml', Config::YAML);
        self::$caveManager = new CaveManager();
        self::$administration = json_decode(file_get_contents($this->getDataFolder() . "data/administration.json"), true);
        self::$quest = json_decode(file_get_contents($this->getDataFolder() . "data/quest.json"), true);
        self::$skills = json_decode(file_get_contents($this->getDataFolder() . "data/skills.json"), true);
        self::$services = json_decode(file_get_contents($this->getDataFolder() . "data/services.json"), true);

        ServerManager::init();
        SettingsManager::init();
        BanManager::init();
        WarpManager::init();
        DropManager::init();
        MoneyManager::init();
        MuteManager::init();
        QuestManager::init();
        BlockManager::init();
        EntityManager::init();
        CaveManager::init();
        SkillManager::init();
        CobblestoneManager::init();
        ItemManager::init();
        MySQLManager::init();
        TileManager::init();
        ServicesManager::init();
        UserManager::init();
        TaskManager::init();
        MarketManager::init();
        SkinManager::init();
        WingsManager::init();
        PetManager::init();
        RecipeManager::init();
        ChestManager::init();
        HazardManager::init();
        TerrainManager::init();
        StatsManager::init();
        AntiCheatManager::init();
        EnchantManager::init();

        ServerManager::loadSettings();
        UserManager::loadAllUsers();
        QuestManager::loadQuests();
        SkillManager::loadSkills();
        ServicesManager::loadServices();
        ChestManager::loadChests();

        CaveManager::setDefaultCaves();

        MySQLManager::setPlayers();

        ListenerManager::registerEvents();
        MarketManager::load();

        ParticleManager::init();

        CommandManager::unregisterCommands();
        CommandManager::registerCommands();

        $this->setMotto();
        $this->loadDefaultLevel();
    }

    public function onDisable() {

        foreach(UserManager::getUsers() as $user) {

            if($this->getServer()->getPlayerExact($user->getName()))
                $user->addToStat(StatsManager::TIME_PLAYED, (time() - $user->getStat(StatsManager::LAST_PLAYED)));

            $user->saveDrop();
            $user->saveSettings();
            $user->saveMoney();
            $user->saveQuests();
            $user->saveSkills();
            $user->saveCobblestone();
            $user->saveServices();
            $user->savePets();
            $user->saveParticles();
            $user->saveStats();
        }

        CaveManager::saveCaves();
        UserManager::saveAllUsers();
        MarketManager::save();
        TerrainManager::saveTerrain();
        HazardManager::save();
        ServerManager::saveSettings();
        ChestManager::saveChests();

        Main::getInstance()->getServer()->getAsyncPool()->submitTask(new MySQLSaveAsyncTask(UserManager::getUsers(), CaveManager::getRegisteredCaves()));
    }

    private function loadDefaultLevel() : void {
        foreach(ConfigUtil::AUTO_LOAD_WORLDS as $world)
            $this->getServer()->loadLevel($world);
    }

    private function setMotto() : void {
        $this->getServer()->getNetwork()->setName("§9§lDark§7Moon§9PE");
    }

    public static function getInstance() : self {
        return self::$instance;
    }

    public static function getDb() : SQLite3 {
        return self::$db;
    }

    public static function getGroupManager() : GroupManager {
        return self::$groupManager;
    }

    public static function getSettings() : Config {
        return self::$settings;
    }

    public static function getShopConfig() : Config {
        return new Config(self::getInstance()->getDataFolder() . "/data/shop.yml", Config::YAML);
    }

    public static function getCfg() : Config{
        return self::$cfg;
    }

    public static function getAdministration() : array{
        return self::$administration;
    }

    public static function getWhitelist() : Config {
        return self::$whitelist;
    }

    public static function getProvider() : SQLite3Provider{
        return self::$provider;
    }

    public static function getGroup() : Config{
        return self::$group;
    }

    public static function setGroup(Config $cfg) : void{
        self::$group = $cfg;
    }

    public static function getEvent() : Config{
        return self::$event;
    }

    public static function setEvent(Config $event) : void{
        self::$event = $event;
    }

    public static function getMagicCase() : Config {
        return self::$magicCase;
    }
}