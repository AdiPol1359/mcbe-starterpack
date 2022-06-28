<?php

namespace Core;

use pocketmine\plugin\PluginBase;

use pocketmine\{
    Server, Player
};

use pocketmine\entity\Creature;

use pocketmine\utils\Config;

use Core\commands\{
    DepozytCommand,
    ParticlesyCommand,
    SklepCommand,
    StoniarkaCommand,
    DropCommand,
    SpawnCommand,
    PktCommand,
    PcaseCommand,
    PallCommand,
    ChatCommand,
    VipCommand,
    SvipCommand,
    SponsorCommand,
    YtCommand,
    YtPCommand,
    EfektyCommand,
    PomocCommand,
    AlertCommand,
    HelpopCommand,
    AchatCommand,
    ClearlagCommand,
    BanCommand,
    BanIpCommand,
    TempbanCommand,
    UnbanCommand,
    UnbanIpCommand,
    ListCommand,
    SprawdzanieCommand,
    PrzyznajesieCommand,
    FlyCommand,
    KordyCommand,
    RepairCommand,
    GamemodeCommand,
    KitCommand,
    CobblexCommand,
    VshopCommand,
    StartEdycjiCommand,
    HealCommand,
    FeedCommand,
    VanishCommand,
    GodCommand,
    TpaCommand,
    TpacceptCommand,
    TpdenyCommand,
    EcCommand,
    ClearCommand,
    MuteCommand,
    UnmuteCommand,
    MsgCommand,
    RCommand,
    StatyCommand,
    TopkaCommand,
    WarpCommand,
    DajCommand,
    HelpCommand,
    CaseCommand,
    ApvpCommand,
    LobbyCommand,
    HomeCommand,
    SethomeCommand,
    DelhomeCommand,
    EnchantCommand,
    ProtectCommand};

use pocketmine\inventory\{
    ShapedRecipe, ShapelessRecipe
};

use pocketmine\item\Item;

use pocketmine\item\enchantment\Enchantment;

use pocketmine\item\enchantment\EnchantmentInstance;

use pocketmine\block\BlockFactory;

use pocketmine\math\Vector3;

use pocketmine\level\Level;

use Core\task\{BotTask,
    AntyLogoutTask,
    ClearLagTask,
    BanTask,
    KityTask,
    AlwaysDayTask,
    NameTagsTask,
    StartEdycjiTask,
    MuteTask,
    LobbyTask,
    ParticlesyTask
    };

use Core\api\{PointsAPI, DropAPI, BanAPI, MuteAPI, KitsAPI, ProtectAPI, StatsAPI, WarpsAPI, LobbyAPI};

use Core\block\BlockManager;

use Core\item\ItemManager;

use Core\entity\EntityManager;

use Core\network\PacketManager;

use Core\enchantment\KnockbackEnchantment;

use Core\level\generator\GeneratorManager;
use Core\tile\Tile;

class Main extends PluginBase {

	private $pointsAPI;
	private $dropAPI;
	private $banAPI;
	private $kitsAPI;
	private $muteAPI;
	private $statsAPI;
    private $warpsAPI;

	private $db;
	private $mysql;

	private static $instance;

	public static $spawnTask;
	public static $antylogoutPlayers = [];
	public static $lastDamager;
	public static $assists = [];
	public static $last;
	public static $chatOn = true;
	public static $lastChatMsg = [];
	public static $lastCmd = [];
	public static $spr = [];
	public static $removeVillager = [];
	public static $villagerName = [];
	public static $addVillagerRecipe = [];
	public static $removeVillagerRecipe = [];
	public static $tpVillager = [];
	public static $copyVillager = [];
	public static $vanish = [];
	public static $god = [];
	public static $tp = [];
	public static $tpTask = [];
	public static $warpTask = [];
	public static $homeTask = [];
	public static $msgR = [];
	public static $ec = [];
	public static $lastPosition = [];
	public static $setWhiteBlock = [];
	public static $removeWhiteBlock = [];

	public const LIMIT_KOXY = 2;
	public const LIMIT_REFY = 8;
	public const LIMIT_PERLY = 4;

	public const MIN_TP = -690;
	public const MAX_TP = 690;

    public const BORDER = 1400;

    public const CPS_MAX = 9;
    public const CPS_COOLDOWN = 5;

	public const ANTYLOGOUT_TIME = 30;

	public const ANTYLOGOUT_KOMENDY = ["/tpa", "/tpaccept", "/tpdeny", "/dolacz", "/home", "/spawn", "/depozyt", "/kit", "/ec", "/schowek", "/ustawbaze", "/lider", "baza", "/warp", "/sojusz", "/permisje", "/oficer", "/repair", "/repair-all", "/heal", "/feed"];
	
	private const MYSQL_HOST = "HOST";
	private const MYSQL_USER = "USER";
	private const MYSQL_PASSWORD = "PASSWORD";
	private const MYSQL_DB = "DB";


	public function onEnable() : void {
        $this->saveResource("shop.yml");

		json_encode([]);

		date_default_timezone_set('Europe/Warsaw');

		self::$instance = $this;

		$this->pointsAPI = new PointsAPI;
		$this->dropAPI = new DropAPI;
		$this->banAPI = new BanAPI;
		$this->muteAPI = new MuteAPI;
		$this->kitsAPI = new KitsAPI;
		$this->statsAPI = new StatsAPI;
		$this->warpsAPI = new WarpsAPI;

		$this->getServer()->getPluginManager()->registerEvents(new EventListener, $this);

		$this->unregisterCommands();
		$this->registerCommands();
		$this->registerRecipes();
		
		EntityManager::init();
		PacketManager::init();
		BlockManager::init();
		ItemManager::init();
		LobbyAPI::init();
		ProtectAPI::init();
		Tile::init();
		
		Enchantment::registerEnchantment(new KnockbackEnchantment(Enchantment::KNOCKBACK, "%enchantment.knockback", Enchantment::RARITY_UNCOMMON, Enchantment::SLOT_SWORD, Enchantment::SLOT_NONE, 2));
		
		$this->db = new \SQLite3($this->getDataFolder(). 'DataBase.db');

		$this->db->exec("CREATE TABLE IF NOT EXISTS depozyt (nick TEXT, koxy INT, refy INT, perly INT)");
		$this->db->exec("CREATE TABLE IF NOT EXISTS 'drop' (nick TEXT, diamenty TEXT, zloto TEXT, emeraldy TEXT, zelazo TEXT, wegiel TEXT, redstone TEXT, bookshelfy TEXT, obsydian TEXT, perly TEXT, slimeball TEXT, jablko TEXT, nicie TEXT, tnt TEXT, cobblestone TEXT)");
		$this->db->exec("CREATE TABLE IF NOT EXISTS stoniarki (x INT, y INT, z INT, time DOUBLE)");
		$this->db->exec("CREATE TABLE IF NOT EXISTS points (nick TEXT, points INT)");
		$this->db->exec("CREATE TABLE IF NOT EXISTS ban (nick TEXT, reason TEXT, date TEXT, ip TEXT, adminNick TEXT)");
		$this->db->exec("CREATE TABLE IF NOT EXISTS mute (nick TEXT, reason TEXT, date TEXT, adminNick TEXT)");
		$this->db->exec("CREATE TABLE IF NOT EXISTS kity (nick TEXT, kit TEXT, date TEXT)");
		$this->db->exec("CREATE TABLE IF NOT EXISTS groups (nick TEXT, groupName TEXT)");
		$this->db->exec("CREATE TABLE IF NOT EXISTS stats (nick TEXT, kills INT, deaths INT, koxy INT, refy INT, perly INT)");
		$this->db->exec("CREATE TABLE IF NOT EXISTS warps (name TEXT, x DOUBLE, y DOUBLE, z DOUBLE)");
		$this->db->exec("CREATE TABLE IF NOT EXISTS home (nick TEXT, name TEXT, x DOUBLE, y DOUBLE, z DOUBLE)");
		$this->db->exec("CREATE TABLE IF NOT EXISTS 'case' (nick TEXT)");
		
		$botCfg = new Config($this->getDataFolder(). "BotMessages.yml", Config::YAML, ["messages" => ["wiadomosc 1", "wiadomosc 2"]]);

		$this->config = new Config($this->getDataFolder(). "Config.yml", Config::YAML, [
		 "motto" => "motto",
		 "whitelist" => [
		  "white",
		  "list"
		 ],
		 "sprawdzanie" => [
		  "x" => 0,
		  "y" => 80,
		  "z" => 0
		  ],
		  "startedycji" => false,
		  "startedycji-time" => "20:00"
		]);
		
		$this->getServer()->getNetwork()->setName($this->config->get("motto"));
		
		GeneratorManager::init();

		$this->getScheduler()->scheduleRepeatingTask(new BotTask($botCfg), 20*10);
		$this->getScheduler()->scheduleRepeatingTask(new AntyLogoutTask($botCfg), 20);
		$this->getScheduler()->scheduleRepeatingTask(new ClearLagTask(300), 20);
		$this->getScheduler()->scheduleDelayedRepeatingTask(new BanTask, 20, 20);
		$this->getScheduler()->scheduleDelayedRepeatingTask(new KityTask, 20, 20);
		$this->getScheduler()->scheduleDelayedRepeatingTask(new MuteTask, 20, 20);
		$this->getScheduler()->scheduleRepeatingTask(new AlwaysDayTask, (20 * 60) * 5);
		$this->getScheduler()->scheduleRepeatingTask(new StartEdycjiTask, 20);
		$this->getScheduler()->scheduleRepeatingTask(new LobbyTask(), 20);
		$this->getScheduler()->scheduleRepeatingTask(new ParticlesyTask(), 2);
		//$this->getScheduler()->scheduleRepeatingTask(new NameTagsTask(), 20*10);

        Enchantment::registerEnchantment(new Enchantment(Enchantment::FORTUNE, "%enchantment.fortune", Enchantment::RARITY_COMMON, Enchantment::SLOT_DIG, Enchantment::SLOT_SHEARS, 3));
		$this->getLogger()->info("Plugin włączono");
	}
	
	public function onDisable() : void {
		$this->getLogger()->info("Plugin wyłączono");
		
		$this->db->close();
	}

	private function unregisterCommands() : void {

		$cmds = [
		    "list",
            "ban",
            "ban-ip",
            "pardon",
            "pardon-ip",
            "list",
            "gamemode",
            "msg",
            "help",
            "me",
            "checkperm",
            "suicide",
            "about",
            "version"
		];

		foreach($cmds as $cmdName) {
			$cmd = $this->getServer()->getCommandMap()->getCommand($cmdName);

			if($cmd != null)
		 	$this->getServer()->getCommandMap()->unregister($cmd);
		}
	}

	private function registerCommands() : void {

		$cmds = [
		    new DepozytCommand(),
            new StoniarkaCommand(),
            new DropCommand(),
            new SpawnCommand(),
            
            new PcaseCommand(),
            new PallCommand(),
            new ChatCommand(),
            new VipCommand(),
            new SvipCommand(),
            new SponsorCommand(),
            new YtCommand(),
            new YtPCommand(),
            new EfektyCommand(),
            new PomocCommand(),
            new AlertCommand(),
            new HelpopCommand(),
            new AchatCommand(),
            new ClearlagCommand(),
            new BanCommand(),
            new BanIpCommand(),
            new TempbanCommand(),
            new UnbanCommand(),
            new UnbanIpCommand(),
            new MuteCommand(),
            new UnmuteCommand(),
            new ListCommand(),
            new SprawdzanieCommand(),
            new PrzyznajesieCommand(),
            new FlyCommand(),
            new KordyCommand,
            new RepairCommand(),
            new GamemodeCommand(),
            new KitCommand(),
            new CobblexCommand(),
            new VshopCommand(),
            new StartEdycjiCommand(),
            new HealCommand(),
            new FeedCommand(),
            new VanishCommand(),
            new GodCommand(),
            new ClearCommand(),
            new TpaCommand(),
            new TpacceptCommand(),
            new TpdenyCommand(),
            new MsgCommand(),
            new RCommand(),
            new StatyCommand(),
            new TopkaCommand(),
            new WarpCommand(),
            new DajCommand(),
            new HelpCommand(),
            new CaseCommand(),
            new EcCommand(),
            new ApvpCommand(),
            new LobbyCommand(),
            new HomeCommand(),
            new SethomeCommand(),
            new DelhomeCommand(),
            new EnchantCommand(),
            new ParticlesyCommand(),
            new ProtectCommand(),
            new SklepCommand()
		];

		$this->getServer()->getCommandMap()->registerAll("core", $cmds);
	}

	private function registerRecipes() : void {
        $kox_recipe = new ShapedRecipe(["GGG", "GJG", "GGG"], ["G" => Item::get(41), "J" => Item::get(260)], [Item::get(466)]);

        $boyfarmer_item = Item::get(49);
        $boyfarmer_item->setCustomName("§r§l§9BoyFarmer");
        $boyfarmer_item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));
        $boyfarmer_recipe = new ShapedRecipe(["GGG", "GJG", "GGG"], ["G" => Item::get(49), "J" => Item::get(278)], [$boyfarmer_item]);

        $sandfarmer_item = Item::get(12);
        $sandfarmer_item->setCustomName("§r§l§9SandFarmer");
        $sandfarmer_item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));
        $sandfarmer_recipe = new ShapedRecipe(["GGG", "GJG", "GGG"], ["G" => Item::get(12), "J" => Item::get(278)], [$sandfarmer_item]);

        $kopaczfosy_item = Item::get(1);
        $kopaczfosy_item->setCustomName("§r§l§9Kopacz Fosy");
        $kopaczfosy_item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));
        $kopaczfosy_recipe = new ShapedRecipe(["GGG", "GJG", "GGG"], ["G" => Item::get(1), "J" => Item::get(278)], [$kopaczfosy_item]);

        $enderchest_recipe = new ShapedRecipe(["GGG", "GJG", "GGG"], ["G" => Item::get(49), "J" => Item::get(368)], [Item::get(130)]);

        $rzucak_item = Item::get(46);
        $rzucak_item->setCustomName("§r§l§4Rzucane TNT");
        $rzucak_item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));
        $rzucak_recipe = new ShapedRecipe(["GGG", "GJG", "GGG"], ["G" => Item::get(46), "J" => Item::get(138)], [$rzucak_item]);

        $stoniarka05 = Item::get(1);
        $stoniarka05->setCustomName("§r§7Generator Kamienia§4 0.5s");
        $stoniarka05->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));
        $stoniarka05_recipe = new ShapedRecipe(["GGG", "GJG", "GGG"], ["G" => Item::get(1), "J" => Item::get(Item::EMERALD)], [$stoniarka05]);

        $stoniarka15 = Item::get(1);
        $stoniarka15->setCustomName("§r§7Generator Kamienia§4 1.5s");
        $stoniarka15->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));
        $stoniarka15_recipe = new ShapedRecipe(["GGG", "GJG", "GGG"], ["G" => Item::get(1), "J" => Item::get(Item::DIAMOND)], [$stoniarka15]);

        $stoniarka3 = Item::get(1);
        $stoniarka3->setCustomName("§r§7Generator Kamienia§4 3s");
        $stoniarka3->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));
        $stoniarka3_recipe = new ShapedRecipe(["GGG", "GJG", "GGG"], ["G" => Item::get(1), "J" => Item::get(Item::IRON_INGOT)], [$stoniarka3]);


        $this->getServer()->getCraftingManager()->registerRecipe($stoniarka3_recipe);
        $this->getServer()->getCraftingManager()->registerRecipe($stoniarka05_recipe);
        $this->getServer()->getCraftingManager()->registerRecipe($stoniarka15_recipe);
        $this->getServer()->getCraftingManager()->registerRecipe($kox_recipe);
        $this->getServer()->getCraftingManager()->registerRecipe($boyfarmer_recipe);
        $this->getServer()->getCraftingManager()->registerRecipe($sandfarmer_recipe);
        $this->getServer()->getCraftingManager()->registerRecipe($kopaczfosy_recipe);
        $this->getServer()->getCraftingManager()->registerRecipe($enderchest_recipe);
        $this->getServer()->getCraftingManager()->registerRecipe($rzucak_recipe);
    }

	public static function format(string $w) : string {
		return " \n§8          xDarkCraft.EU\n\n§r§8§l>§r §7$w\n ";
	}

	public static function formatLines(array $w) : string {
		return " \n§8          xDarkCraft.EU\n\n§8> §7".implode("\n§8> §7", $w)."\n ";
	}


	public static function getInstance() : Main {
		return self::$instance;
	}

	public function getDb() : \SQLite3 {
		return $this->db;
	}
	
	/*public function getDb() : \mysqli {
		return $this->mysql;
	}*/

	public function getPointsAPI() : PointsAPI {
		return $this->pointsAPI;
	}

	public function getDropAPI() : DropAPI {
		return $this->dropAPI;
	}

	public function getBanAPI() : BanAPI {
		return $this->banAPI;
	}
	
	public function getMuteAPI() : MuteAPI {
		return $this->muteAPI;
	}
	
	public function getKitsAPI() : KitsAPI {
		return $this->kitsAPI;
	}

	public function getGroupsAPI() : GroupsAPI {
	 return $this->groupsAPI;
	}
	
	public function getStatsAPI() : StatsAPI {
		return $this->statsAPI;
	}
	
	public function getChatFormatAPI() : ChatFormatAPI {
		return $this->chatFormatAPI;
	}
	
	public function getWarpsAPI() : WarpsAPI {
		return $this->warpsAPI;
	}

	public function getWhitelistMessage() : string {
		return implode(PHP_EOL, $this->config->get("whitelist"));
	}

	public function clearLag() : void {
		$count = 0;

		foreach($this->getServer()->getLevels() as $level) {
			foreach($level->getEntities() as $entity) {
				if(!$entity instanceof Creature) {
					$entity->close();

					$count++;
				}
			}
		}
		
		foreach($this->getServer()->getDefaultLevel()->getPlayers() as $p)
		 $p->sendMessage(self::format("Pomyslnie usunieto §4$count §7itemow ze swiata!"));
	}
	
	public function startEdycji() : bool {
		return (bool) $this->config->get("startedycji");
	}
	
	public function setStartEdycji(bool $set) : void {
		$this->config->set("startedycji", $set);
		$this->config->save();
	}
	
	public function getStartEdycjiTime() : string {
		return $this->config->get("startedycji-time");
	}
	
	public function setStartEdycjiTime(string $time) : void {
		$this->config->set("startedycji-time", $time);
		$this->config->save();
	}
	
	public function getBookshelfsCount(Vector3 $pos, Level $level) : int {
		
		$count = 0;
		
        if($level->getBlock($pos->add(2))->getId() == 47) $count++;
		if($level->getBlock($pos->add(2, 0, 1))->getId() == 47) $count++;		
		if($level->getBlock($pos->add(2, 0, -1))->getId() == 47) $count++;
		
		if($level->getBlock($pos->add(-2, 0))->getId() == 47) $count++;
		if($level->getBlock($pos->add(-2, 0, -1))->getId() == 47) $count++;
		if($level->getBlock($pos->add(-2, 0, 1))->getId() == 47) $count++;
		
		if($level->getBlock($pos->add(0, 0, 2))->getId() == 47) $count++;
		if($level->getBlock($pos->add(1, 0, 2))->getId() == 47) $count++;
		if($level->getBlock($pos->add(-1, 0, 2))->getId() == 47) $count++;
		
		if($level->getBlock($pos->add(0, 0, -2))->getId() == 47) $count++;
		if($level->getBlock($pos->add(-1, 0, -2))->getId() == 47) $count++;
		if($level->getBlock($pos->add(1, 0, -2))->getId() == 47) $count++;



		if($level->getBlock($pos->add(2, 1))->getId() == 47) $count++;		
		if($level->getBlock($pos->add(2, 1, 1))->getId() == 47) $count++;		
		if($level->getBlock($pos->add(2, 1, -1))->getId() == 47) $count++;
		
		if($level->getBlock($pos->add(-2, 1))->getId() == 47) $count++;
		if($level->getBlock($pos->add(-2, 1, -1))->getId() == 47) $count++;
		if($level->getBlock($pos->add(-2, 1, 1))->getId() == 47) $count++;
		
		if($level->getBlock($pos->add(0, 1, 2))->getId() == 47) $count++;
		if($level->getBlock($pos->add(1, 1, 2))->getId() == 47) $count++;
		if($level->getBlock($pos->add(-1, 1, 2))->getId() == 47) $count++;
		
		if($level->getBlock($pos->add(0, 1, -2))->getId() == 47) $count++;
		if($level->getBlock($pos->add(-1, 1, -2))->getId() == 47) $count++;
		if($level->getBlock($pos->add(1, 1, -2))->getId() == 47) $count++;
		
		return (int) $count;
	}

	public function getTeleportTime(Player $player) : int {
	    $time = 10;

	    if($player->hasPermission("xdarkcraft.tp.7"))
            $time = 7;

        if($player->hasPermission("xdarkcraft.tp.5"))
            $time = 5;

        return $time;
    }

    public function getShopConfig() : Config {
        return new Config($this->getDataFolder(). 'shop.yml', Config::YAML);
    }
}