<?php

declare(strict_types=1);

namespace core;

use core\anticheat\AntiCheatManager;
use core\blocks\Bamboo;
use core\blocks\Chest;
use core\blocks\Farmland;
use core\blocks\Lava;
use core\blocks\Obsidian;
use core\blocks\Sand;
use core\blocks\StoneButton;
use core\blocks\TNT;
use core\blocks\Water;
use core\commands\admin\TurboDropCommand;
use core\commands\admin\VillagerShopCommand;
use core\commands\guild\AcceptCommand;
use core\commands\guild\AfCommand;
use core\commands\guild\AllianceCommand;
use core\commands\guild\BaseTeleportCommand;
use core\commands\guild\BattleCommand;
use core\commands\guild\BreakAllianceCommand;
use core\commands\guild\DeleteCommand;
use core\commands\guild\FfCommand;
use core\commands\guild\GuildAdminCommand;
use core\commands\guild\GuildKickCommand;
use core\commands\guild\IncreaseCommand;
use core\commands\guild\InfoCommand;
use core\commands\guild\InviteCommand;
use core\commands\guild\ItemsCommand;
use core\commands\guild\LeaveCommand;
use core\commands\guild\PanelCommand;
use core\commands\guild\RegenerationCommand;
use core\commands\guild\RenewalCommand;
use core\commands\guild\ReportGuildCommand;
use core\commands\guild\SetBaseCommand;
use core\commands\guild\TreasuryCommand;
use core\commands\guild\WarCommand;
use core\commands\guild\WarsCommand;
use core\commands\player\WarpCommand;
use core\commands\admin\ServerCommand;
use core\commands\admin\VanishCommand;
use core\commands\admin\VerificationCommand;
use core\commands\admin\WhoCommand;
use core\commands\admin\WingsCommand;
use core\commands\player\DepositCommand;
use core\commands\admin\APlayerCommand;
use core\commands\admin\InvSeeCommand;
use core\commands\admin\StatsCommand;
use core\commands\guild\CreateCommand;
use core\commands\player\DiscordCommand;
use core\commands\player\EffectCommand;
use core\commands\player\HelpCommand;
use core\commands\player\HomeCommand;
use core\commands\admin\AntiCheatCommand;
use core\commands\admin\CrowbarCommand;
use core\commands\admin\DeviceCommand;
use core\commands\admin\GarbageCollectorCommand;
use core\commands\admin\GodCommand;
use core\commands\admin\KickCommand;
use core\commands\admin\LockCommand;
use core\commands\admin\RenameCommand;
use core\commands\admin\RenderDistanceCommand;
use core\commands\admin\RtpCommand;
use core\commands\admin\SayCommand;
use core\commands\admin\ScaleCommand;
use core\commands\admin\SetSpawnCommand;
use core\commands\admin\SetwarpCommand;
use core\commands\admin\AchatCommand;
use core\commands\admin\AlertCommand;
use core\commands\admin\AntiCheatAlertsCommand;
use core\commands\admin\BorderCommand;
use core\commands\admin\CallCommand;
use core\commands\admin\CaseCommand;
use core\commands\admin\ChatCommand;
use core\commands\admin\ClearCommand;
use core\commands\admin\DamageCommand;
use core\commands\admin\DelwarpCommand;
use core\commands\admin\DeopCommand;
use core\commands\admin\FeedCommand;
use core\commands\admin\GamemodeCommand;
use core\commands\admin\HealCommand;
use core\commands\admin\IdCommand;
use core\commands\admin\MuteCommand;
use core\commands\admin\OpCommand;
use core\commands\admin\PexCommand;
use core\commands\admin\SafeCommand;
use core\commands\admin\StatusCommand;
use core\commands\admin\TeleportCommand;
use core\commands\admin\TerrainCommand;
use core\commands\admin\TimingsCommand;
use core\commands\admin\UnMuteCommand;
use core\commands\admin\WhitelistCommand;
use core\commands\player\AboutCommand;
use core\commands\player\AbyssCommand;
use core\commands\player\BackpackCommand;
use core\commands\player\BlocksCommand;
use core\commands\player\CraftingCommand;
use core\commands\player\DelHomeCommand;
use core\commands\player\DescriptionCommand;
use core\commands\player\DropCommand;
use core\commands\player\IgnoreCommand;
use core\commands\player\IncognitoCommand;
use core\commands\player\KitCommand;
use core\commands\player\ListCommand;
use core\commands\player\MarketCommand;
use core\commands\player\MsgCommand;
use core\commands\player\PatternCommand;
use core\commands\player\PingCommand;
use core\commands\player\PlayerCommand;
use core\commands\player\PluginsCommand;
use core\commands\player\RCommand;
use core\commands\player\ReportCommand;
use core\commands\player\ResetRankCommand;
use core\commands\player\ServicesCommand;
use core\commands\player\SetHomeCommand;
use core\commands\player\SpawnCommand;
use core\commands\player\SponsorCommand;
use core\commands\player\SvipCommand;
use core\commands\player\TopCommand;
use core\commands\player\TpacceptCommand;
use core\commands\player\TpaCommand;
use core\commands\player\UnIgnoreCommand;
use core\commands\player\VipCommand;
use core\commands\player\WebsiteCommand;
use core\commands\player\YtCommand;
use core\commands\premium\EnchantCommand;
use core\commands\premium\EnderChestCommand;
use core\commands\premium\RepairCommand;
use core\enchantment\KnockbackEnchantment;
use core\enchantment\LootEnchantment;
use core\entities\custom\GuildHeart;
use core\entities\custom\VillagerShopEntity;
use core\entities\object\FireworksRocket;
use core\entities\object\PrimedTNT;
use core\entities\projectile\Arrow;
use core\entities\projectile\Snowball;
use core\guilds\GuildManager;
use core\items\Bucket;
use core\items\custom\BoyFarmer;
use core\items\custom\CobbleX;
use core\items\custom\Crowbar;
use core\items\custom\FastPickaxe;
use core\items\custom\FosMiner;
use core\items\custom\PremiumCase;
use core\items\custom\StoneGenerator;
use core\items\custom\TerrainAxe;
use core\items\custom\ThrownTNT;
use core\items\EnchantedGoldenApple;
use core\items\Fireworks;
use core\items\FlintSteel;
use core\items\GoldenApple;
use core\items\LiquidBucket;
use core\items\ProjectileEnderPearl;
use core\listeners\block\BlockBreakListener;
use core\listeners\block\BlockDecayListener;
use core\listeners\block\BlockFormListener;
use core\listeners\block\BlockPlaceListener;
use core\listeners\block\SignChangeListener;
use core\listeners\entity\EntityDamageListener;
use core\listeners\entity\EntityDeSpawnListener;
use core\listeners\entity\EntityExplodeListener;
use core\listeners\entity\ExplosionPrimeListener;
use core\listeners\inventory\InventoryTransactionListener;
use core\listeners\inventory\ItemHeldListener;
use core\listeners\inventory\PickupItemListener;
use core\listeners\item\ItemDeSpawnListener;
use core\listeners\item\ItemSpawnListener;
use core\listeners\packet\DataPacketReceiveListener;
use core\listeners\packet\DataPacketSendListener;
use core\listeners\player\CraftItemListener;
use core\listeners\player\PlayerChangeSkinListener;
use core\listeners\player\PlayerChatListener;
use core\listeners\player\PlayerCommandPreprocessListener;
use core\listeners\player\PlayerCreationListener;
use core\listeners\player\PlayerDeathListener;
use core\listeners\player\PlayerDropItemListener;
use core\listeners\player\PlayerExhaustListener;
use core\listeners\player\PlayerInteractListener;
use core\listeners\player\PlayerItemConsumeListener;
use core\listeners\player\PlayerItemUseListener;
use core\listeners\player\PlayerJoinListener;
use core\listeners\player\PlayerLoginListener;
use core\listeners\player\PlayerQuitListener;
use core\managers\CpsManager;
use core\managers\AbyssManager;
use core\managers\admin\AdminLoggerManager;
use core\managers\chestlocker\ChestLockerManager;
use core\managers\market\MarketManager;
use core\managers\ServerManager;
use core\managers\service\ServicesManager;
use core\managers\SkinManager;
use core\managers\turbodrop\TurboDropManager;
use core\managers\villager\VillagerShopManager;
use core\managers\war\WarManager;
use core\managers\WhitelistManager;
use core\managers\CommandLockManager;
use core\managers\ban\BanManager;
use core\managers\drop\DropManager;
use core\managers\mute\MuteManager;
use core\managers\safe\SafeManager;
use core\managers\terrain\TerrainManager;
use core\managers\warp\WarpManager;
use core\managers\WaterManager;
use core\managers\wing\WingsManager;
use core\permissions\group\GroupManager;
use core\permissions\group\PlayerGroupManager;
use core\providers\data\SQLiteProvider;
use core\providers\ProviderInterface;
use core\providers\query\DataBaseQuery;
use core\tasks\sync\AbyssTask;
use core\tasks\sync\AntyLogoutTask;
use core\tasks\sync\BorderTask;
use core\tasks\sync\BotTask;
use core\tasks\sync\GuildTask;
use core\tasks\sync\NotifyTask;
use core\tasks\sync\ServerSettingsTask;
use core\tasks\sync\UpdateUserTask;
use core\tasks\sync\WarCheckerTask;
use core\users\UserManager;
use core\utils\Settings;
use pocketmine\block\BlockFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\crafting\ShapedRecipe;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\entity\projectile\Arrow as ArrowAlias;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\network\mcpe\cache\StaticPacketCache;
use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\Config;
use pocketmine\world\World;
use RuntimeException;
use Throwable;
use Webmozart\PathUtil\Path;
use const pocketmine\BEDROCK_DATA_PATH;

class Main extends PluginBase {

    public const LOCAL_DEFINITIONS_PATH = "biome_definitions.nbt";

    private static self $instance;

    private Config $settings;
    private Config $group;
    private Config $whitelist;

    private ProviderInterface $provider;
    private DataBaseQuery $dataBaseQuery;

    private GroupManager $groupManager;
    private PlayerGroupManager $playerGroupManager;

    private MuteManager $muteManager;
    private BanManager $banManager;
    private UserManager $userManager;
    private DropManager $dropManager;
    private TerrainManager $terrainManager;
    private WaterManager $waterManager;
    private SafeManager $safeManager;
    private AntiCheatManager $antiCheatManager;
    private WarpManager $warpManager;
    private CommandLockManager $commandLockManager;
    private WhitelistManager $whitelistManager;
    private GuildManager $guildManager;
    private AbyssManager $abyssManager;
    private SkinManager $skinManager;
    private WingsManager $wingsManager;
    private ServerManager $serverManager;
    private MarketManager $marketManager;
    private ServicesManager $servicesManager;
    private TurboDropManager $turboDropManager;
    private VillagerShopManager $villagerShopManager;
    private AdminLoggerManager $adminLoggerManager;
    private WarManager $warManager;
    private ChestLockerManager $chestLockerManager;
    private CpsManager $cpsManager;

    public function onLoad() : void {
        self::$instance = $this;
    }

    public function onEnable() : void {
        date_default_timezone_set("Europe/Warsaw");

        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . "/data/");
        @mkdir($this->getDataFolder() . "/data/guilds");
        @mkdir($this->getDataFolder() . "/wings");
        @mkdir($this->getDataFolder() . "/playersSkins");

        $this->saveResource("data/groups.yml");
        $this->group = new Config($this->getDataFolder() . 'data/groups.yml', Config::YAML);

        $this->saveResource("data/settings.yml");
        $this->settings = new Config($this->getDataFolder() . 'data/settings.yml', Config::YAML);

        $this->whitelist = new Config($this->getDataFolder() . "data/whitelist.json", Config::JSON, [
            "status" => false,
            "date" => null,
            "players" => []
        ]);

        $this->initEnchantments();

        Settings::__init();

        $this->provider = new SQLiteProvider($this);

        $this->dataBaseQuery = new DataBaseQuery($this);
        $this->dataBaseQuery->sendDefaultQueries();

        $this->muteManager = new MuteManager($this);
        $this->muteManager->loadMutes();

        $this->banManager = new BanManager($this);
        $this->banManager->loadBans();

        $this->dropManager = new DropManager();
        $this->dropManager->loadDrop();

        $this->groupManager = new GroupManager($this);
        $this->groupManager->load();

        $this->playerGroupManager = new PlayerGroupManager($this);
        $this->playerGroupManager->loadAll();

        $this->terrainManager = new TerrainManager($this);
        $this->terrainManager->load();

        $this->waterManager = new WaterManager();

        $this->safeManager = new SafeManager($this);
        $this->safeManager->loadSafes();

        $this->servicesManager = new ServicesManager($this);
        $this->servicesManager->loadServices();

        $this->userManager = new UserManager($this);
        $this->userManager->loadAllUsers();

        $this->antiCheatManager = new AntiCheatManager($this);
        $this->antiCheatManager->init();

        $this->warpManager = new WarpManager($this);
        $this->warpManager->loadWarps();

        $this->commandLockManager = new CommandLockManager();

        $this->whitelistManager = new WhitelistManager($this);

        $this->guildManager = new GuildManager($this);
        $this->guildManager->loadGuilds();

        $this->abyssManager = new AbyssManager();

        $this->skinManager = new SkinManager($this);

        $this->wingsManager = new WingsManager($this);
        $this->wingsManager->load();

        $this->serverManager = new ServerManager($this);

        $this->marketManager = new MarketManager($this);
        $this->marketManager->load();

        $this->turboDropManager = new TurboDropManager($this);
        $this->turboDropManager->load();

        $this->villagerShopManager = new VillagerShopManager($this);
        $this->villagerShopManager->loadVillagers();

        $this->adminLoggerManager = new AdminLoggerManager($this);
        $this->adminLoggerManager->loadAdmins();

        $this->warManager = new WarManager($this);
        $this->warManager->loadWars();

        $this->chestLockerManager = new ChestLockerManager($this);
        $this->chestLockerManager->load();

        $this->cpsManager = new CpsManager();

        $this->initCommands();
        $this->initBlocks();
        $this->initListeners();
        $this->initTasks();
        $this->initEntities();
        $this->initItems();
        $this->initRecipes();
        $this->initWorlds();
        //$this->initSnow();
    }

    public function onDisable() : void {
        $this->userManager->save();
        $this->guildManager->save();

        $this->muteManager->save();
        $this->banManager->save();
        $this->safeManager->save();
        $this->serverManager->save();
        $this->terrainManager->save();
        $this->adminLoggerManager->save();
        $this->villagerShopManager->save();
        $this->warManager->save();
        $this->marketManager->save();
        $this->playerGroupManager->save();
        $this->turboDropManager->save();
        $this->chestLockerManager->save();
        $this->waterManager->save();
        $this->warpManager->save();

        //HACK: nie trzeba przenosić instancji
        foreach($this->getServer()->getWorldManager()->getWorlds() as $world) {
            foreach($world->getEntities() as $entity) {
                if($entity instanceof VillagerShopEntity) {
                    $entity->close();
                }
            }
        }
    }

    public static function getInstance() : self {
        return self::$instance;
    }

    public function getProvider() : ProviderInterface {
        return $this->provider;
    }

    public function getDataBaseQuery() : DataBaseQuery {
        return $this->dataBaseQuery;
    }

    public function getGroup() : Config{
        return $this->group;
    }

    public function getSettings() : Config {
        return $this->settings;
    }

    public function getDataBase() : DataBaseQuery {
        return $this->dataBaseQuery;
    }

    public function getUserManager() : UserManager {
        return $this->userManager;
    }

    public function getBanManager() : BanManager {
        return $this->banManager;
    }

    public function getDropManager() : DropManager {
        return $this->dropManager;
    }

    public function getGroupManager() : GroupManager {
        return $this->groupManager;
    }

    public function getPlayerGroupManager() : PlayerGroupManager {
        return $this->playerGroupManager;
    }

    public function getMuteManager() : MuteManager {
        return $this->muteManager;
    }

    public function getTerrainManager() : TerrainManager {
        return $this->terrainManager;
    }

    public function getWaterManager() : WaterManager {
        return $this->waterManager;
    }

    public function getSafeManager() : SafeManager {
        return $this->safeManager;
    }

    public function getAntiCheatManager() : AntiCheatManager {
        return $this->antiCheatManager;
    }

    public function getWarpManager() : WarpManager {
        return $this->warpManager;
    }

    public function getCommandLockManager() : CommandLockManager {
        return $this->commandLockManager;
    }

    public function getWhitelist() : Config {
        return $this->whitelist;
    }

    public function getWhitelistManager() : WhitelistManager {
        return $this->whitelistManager;
    }

    public function getGuildManager() : GuildManager {
        return $this->guildManager;
    }

    public function getAbyssManager() : AbyssManager {
        return $this->abyssManager;
    }

    public function getSkinManager() : SkinManager {
        return $this->skinManager;
    }

    public function getWingsManager() : WingsManager {
        return $this->wingsManager;
    }

    public function getServerManager() : ServerManager {
        return $this->serverManager;
    }

    public function getMarketManager() : MarketManager {
        return $this->marketManager;
    }

    public function getServicesManager() : ServicesManager {
        return $this->servicesManager;
    }

    public function getTurboDropManager() : TurboDropManager {
        return $this->turboDropManager;
    }

    public function getVillagerShopManager() : ?VillagerShopManager {
        return $this->villagerShopManager ?? null;
    }

    public function getAdminLoggerManager() : AdminLoggerManager {
        return $this->adminLoggerManager;
    }

    public function getWarManager() : WarManager {
        return $this->warManager;
    }

    public function getChestLockerManager() : ChestLockerManager {
        return $this->chestLockerManager;
    }

    public function getCpsManager() : CpsManager {
        return $this->cpsManager;
    }

    private function initCommands() : void {
        $commands = [
            "list",
            "ban",
            "ban-ip",
            "pardon",
            "pardon-ip",
            "msg",
            "me",
            "checkperm",
            "suicide",
            "help",
            "?",
            "clear",
            "say",
            "reload",
            "whitelist",
            "mixer",
            "version",
            "banlist",
            "playsound",
            "seed",
            "stopsound",
            "title",
            "transferserver",
            "dumpmemory",
            "enchant",
            "particle",
            "status",
            "teleport",
            "gamemode",
            "deop",
            "op",
            "plugins",
            "kick",
            "effect",
            "help",
            "spawnpoint",
            "timings",
            "defaultgamemode",
            "difficulty",
            "gc",
        ];

        foreach($commands as $cmdName) {
            $cmd = $this->getServer()->getCommandMap()->getCommand($cmdName);

            if($cmd != null)
                $this->getServer()->getCommandMap()->unregister($cmd);
        }

        $this->getServer()->getCommandMap()->registerAll("core", [
            new PingCommand(),
            //new BanCommand(),
            //new UnbanCommand(),
            new GamemodeCommand(),
            new AlertCommand(),
            new ChatCommand(),
            new DropCommand(),
            new EnderChestCommand(),
            new RepairCommand(),
            new AboutCommand(),
            new AbyssCommand(),
            new BackpackCommand(),
            new TeleportCommand(),
            new IdCommand(),
            new ClearCommand(),
            new PexCommand(),
            new BlocksCommand(),
            new MuteCommand(),
            new UnMuteCommand(),
            new DeopCommand(),
            new OpCommand(),
            //TODO: /backup, /event
            new ListCommand(),
            new TerrainCommand(),
            new SafeCommand(),
            new PatternCommand(),
            new DescriptionCommand(),
            new AchatCommand(),
            new AntiCheatCommand(),
            new AntiCheatAlertsCommand(),
            new BorderCommand(),
            new CallCommand(),
            new CaseCommand(),
            new CrowbarCommand(),
            new DamageCommand(),
            new DelwarpCommand(),
            new SetwarpCommand(),
            new DeviceCommand(),
            new FeedCommand(),
            new GarbageCollectorCommand(),
            new GodCommand(),
            new HealCommand(),
            new KickCommand(),
            new LockCommand(),
            new RenameCommand(),
            new RenderDistanceCommand(),
            new RtpCommand(),
            new SayCommand(),
            new ScaleCommand(),
            new SetSpawnCommand(),
            new StatusCommand(),
            new TimingsCommand(),
            new WhitelistCommand(),
            new InvSeeCommand(),
            //NEW
            new StatsCommand(),
            new APlayerCommand(),
            new ServerCommand(),
            new VanishCommand(),
            new WhoCommand(),
            new WingsCommand(),
            new VerificationCommand(),
            new TurboDropCommand(),
            new VillagerShopCommand(),
            //GRACZE
            new CraftingCommand(),
            new DelHomeCommand(),
            new SetHomeCommand(),
            new HomeCommand(),
            new DiscordCommand(),
            new EffectCommand(),
            new EnchantCommand(),
            new HelpCommand(),
            new IgnoreCommand(),
            //NEW
            new IncognitoCommand(),
            new DepositCommand(),
            new KitCommand(),
            new MarketCommand(),
            new MsgCommand(),
            new PlayerCommand(),
            new PluginsCommand(),
            new RCommand(),
            new ReportCommand(),
            new ResetRankCommand(),
            new ServicesCommand(),
            new SpawnCommand(),
            new SponsorCommand(),
            new SvipCommand(),
            new TopCommand(),
            new TpacceptCommand(),
            new TpaCommand(),
            new UnIgnoreCommand(),
            new VipCommand(),
            new WarpCommand(),
            new WebsiteCommand(),
            new YtCommand(),
            //GILDIE
            new CreateCommand(),
            new AcceptCommand(),
            new AfCommand(),
            new AllianceCommand(),
            new BaseTeleportCommand(),
            new BattleCommand(),
            new BreakAllianceCommand(),
            new DeleteCommand(),
            new FfCommand(),
            new GuildAdminCommand(),
            new GuildKickCommand(),
            new IncreaseCommand(),
            new InfoCommand(),
            new InviteCommand(),
            new ItemsCommand(),
            new LeaveCommand(),
            new PanelCommand(),
            new RegenerationCommand(),
            new RenewalCommand(),
            new ReportGuildCommand(),
            new SetBaseCommand(),
            new TreasuryCommand(),
            new WarCommand(),
            new WarsCommand(),
        ]);
    }

    private function initBlocks() : void {
        $blocks = [
            new Lava(),
            new Water(),
            new Farmland(),
            new Obsidian(),
            new StoneButton(),
            new TNT(),
            new Chest(),
            new Sand(),
            new Bamboo(),
        ];

        foreach($blocks as $block) {
            BlockFactory::getInstance()->register($block, true);
        }
    }

    private function initListeners() : void {
        $listeners = [
            new PlayerLoginListener(),
            new DataPacketReceiveListener(),
            new InventoryTransactionListener(),
            new PlayerChatListener(),
            new PlayerCommandPreprocessListener(),
            new PlayerExhaustListener(),
            new PlayerInteractListener(),
            new PlayerJoinListener(),
            new PlayerQuitListener(),
            new BlockBreakListener(),
            new BlockPlaceListener(),
            new ItemSpawnListener(),
            new ItemDeSpawnListener(),
            new EntityDeSpawnListener(),
            new BlockDecayListener(),
            new BlockFormListener(),
            new SignChangeListener(),
            new ItemHeldListener(),
            new PickupItemListener(),
            new PlayerChangeSkinListener(),
            new PlayerItemConsumeListener(),
            new CraftItemListener(),
            new PlayerDeathListener(),
            new PlayerDropItemListener(),
            new PlayerItemUseListener(),
            new EntityDamageListener(),
            new EntityExplodeListener(), //TODO: sprawdzic
            new ExplosionPrimeListener(),
            new DataPacketSendListener(),
            new PlayerCreationListener()
        ];

        foreach($listeners as $listener) {
            $this->getServer()->getPluginManager()->registerEvents($listener, $this);
        }
    }

    private function initTasks() : void {
        $this->getScheduler()->scheduleRepeatingTask(new ServerSettingsTask(), 20);
        $this->getScheduler()->scheduleRepeatingTask(new UpdateUserTask(), 20);
        $this->getScheduler()->scheduleRepeatingTask(new AbyssTask($this->abyssManager), 20);
        $this->getScheduler()->scheduleRepeatingTask(new BotTask(), 20*Settings::$BOT_MESSAGE_DELAY);
        $this->getScheduler()->scheduleRepeatingTask(new AbyssTask($this->abyssManager), 20);
        $this->getScheduler()->scheduleRepeatingTask(new WarCheckerTask(), 20);
        $this->getScheduler()->scheduleRepeatingTask(new GuildTask(), 20);
        $this->getScheduler()->scheduleRepeatingTask(new AntyLogoutTask(), 20);
        $this->getScheduler()->scheduleRepeatingTask(new NotifyTask(), 20);
        $this->getScheduler()->scheduleRepeatingTask(new BorderTask(), 20*3);
    }

    private function initEnchantments() : void {
        $stringToEnchantmentParser = StringToEnchantmentParser::getInstance();

        $enchantments = [
            EnchantmentIds::KNOCKBACK => new KnockbackEnchantment("KNOCKBACK", Rarity::UNCOMMON, ItemFlags::SWORD, ItemFlags::NONE, 2),
            EnchantmentIds::FORTUNE => new LootEnchantment("FORTUNE", Rarity::RARE, ItemFlags::TOOL, ItemFlags::NONE, 3),
            -1 => new Enchantment("Glow", Rarity::MYTHIC, ItemFlags::ALL, ItemFlags::NONE, 1),
        ];

        foreach($enchantments as $enchantId => $enchantment) {
            EnchantmentIdMap::getInstance()->register($enchantId, $enchantment);

            if($stringToEnchantmentParser->parse(strtolower($enchantment->getName()))) {
                $stringToEnchantmentParser->override(strtolower($enchantment->getName()), fn() => EnchantmentIdMap::getInstance()->fromId($enchantId));
                continue;
            }

            $stringToEnchantmentParser->register(strtolower($enchantment->getName()), fn() => EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::FORTUNE));
        }
    }

    private function initEntities() : void {
        $factory = EntityFactory::getInstance();

        $factory->register(FireworksRocket::class, function(World $world, CompoundTag $nbt) : FireworksRocket{
            return new FireworksRocket(EntityDataHelper::parseLocation($nbt, $world), ItemFactory::getInstance()->get(ItemIds::FIREWORKS));
        }, ['Firework', 'minecraft:firework_rocket'], EntityLegacyIds::FIREWORKS_ROCKET);

        $factory->register(GuildHeart::class, function(World $world, CompoundTag $nbt) : GuildHeart{
            return new GuildHeart(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ['GuildHeart']);

        $factory->register(VillagerShopEntity::class, function(World $world, CompoundTag $nbt) : VillagerShopEntity{
            return new VillagerShopEntity(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ['VillagerShop']);

        $factory->register(PrimedTNT::class, function(World $world, CompoundTag $nbt) : PrimedTNT{
            return new PrimedTNT(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['PrimedTnt', 'PrimedTNT', 'minecraft:tnt'], EntityLegacyIds::TNT);

        $factory->register(Arrow::class, function(World $world, CompoundTag $nbt) : Arrow{
            return new Arrow(EntityDataHelper::parseLocation($nbt, $world), null, $nbt->getByte(ArrowAlias::TAG_CRIT, 0) === 1, $nbt);
        }, ['Arrow', 'minecraft:arrow'], EntityLegacyIds::ARROW);

        $factory->register(Snowball::class, function(World $world, CompoundTag $nbt) : Snowball{
            return new Snowball(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['Snowball', 'minecraft:snowball'], EntityLegacyIds::SNOWBALL);
    }

    private function initItems() : void {
        $items = [
            new Armor(new ItemIdentifier(ItemIds::IRON_BOOTS, 0), "Iron Boots", new ArmorTypeInfo(2, 430, ArmorInventory::SLOT_FEET)),
            new Armor(new ItemIdentifier(ItemIds::IRON_CHESTPLATE, 0), "Iron Chestplate", new ArmorTypeInfo(6, 529, ArmorInventory::SLOT_CHEST)),
            new Armor(new ItemIdentifier(ItemIds::IRON_HELMET, 0), "Iron Helmet", new ArmorTypeInfo(2, 364, ArmorInventory::SLOT_HEAD)),
            new Armor(new ItemIdentifier(ItemIds::IRON_LEGGINGS, 0), "Iron Leggings", new ArmorTypeInfo(5, 496, ArmorInventory::SLOT_LEGS)),

            new FlintSteel(),
            new GoldenApple(),
            new Fireworks(),
            new ProjectileEnderPearl(),
            new Bucket(new ItemIdentifier(ItemIds::BUCKET, 0), "Bucket"),
            new LiquidBucket(new ItemIdentifier(ItemIds::BUCKET, 8), "Water Bucket", VanillaBlocks::WATER()),
            new EnchantedGoldenApple(new ItemIdentifier(ItemIds::ENCHANTED_GOLDEN_APPLE, 0), "Enchanted Golden Apple"),
        ];

        $creativeItems = [
            (new BoyFarmer())->__toItem(),
            (new CobbleX())->__toItem(),
            (new Crowbar())->__toItem(),
            (new FastPickaxe())->__toItem(),
            (new FosMiner())->__toItem(),
            (new PremiumCase())->__toItem(),
            (new StoneGenerator())->__toItem(),
            (new TerrainAxe())->__toItem(),
            (new ThrownTNT())->__toItem()
        ];

        foreach($items as $item) {
            ItemFactory::getInstance()->register($item, true);
        }

        foreach($creativeItems as $creativeItem) {
            CreativeInventory::getInstance()->add($creativeItem);
        }
    }

    private function initRecipes(): void {
        $this->getServer()->getCraftingManager()->registerShapedRecipe(new ShapedRecipe(
            ["aaa", "axa", "aaa"],
            [
                "a" => VanillaBlocks::GOLD()->asItem(),
                "x" => VanillaItems::APPLE()
            ],
            [VanillaItems::ENCHANTED_GOLDEN_APPLE()]
        ));
        $this->getServer()->getCraftingManager()->registerShapedRecipe(new ShapedRecipe(
            ["aaa", "axa", "aaa"],
            [
                "a" => VanillaBlocks::OBSIDIAN()->asItem(),
                "x" => VanillaItems::ENDER_PEARL()
            ],
            [VanillaBlocks::ENDER_CHEST()->asItem()]
        ));
    }

    private function initSnow() : void {
        $compressedBiomeData = @file_get_contents($path = Path::join(BEDROCK_DATA_PATH, self::LOCAL_DEFINITIONS_PATH));
        if(!$compressedBiomeData) {
            throw new RuntimeException("Failed to read a file $path");
        }

        $nbt = (new NetworkNbtSerializer())->read($compressedBiomeData)->mustGetCompoundTag();
        foreach ($nbt->getValue() as $biomeName => $biomeCompound) {
            if(!$biomeCompound instanceof CompoundTag) {
                throw new AssumptionFailedError("Received invalid or corrupted biome data. Try looking for a new plugin version at poggit");
            }

            foreach ($biomeCompound as $index => $value) {
                if($index === "temperature") {
                    $value = new FloatTag(0);
                }

                $biomeCompound->setTag($index, $value);
            }

            $nbt->setTag($biomeName, $biomeCompound);
        }

        try {
            StaticPacketCache::getInstance()->getBiomeDefs()->definitions = new CacheableNbt($nbt);
        } catch(Throwable) {
            throw new AssumptionFailedError("There were some changes in protocol library independent on protocol change. Try looking for a new plugin version at poggit");
        }
    }

    private function initWorlds() : void {
        $world = $this->getServer()->getWorldManager()->getDefaultWorld();
        $world->setTime(1000);
        $world->stopTime();
    }
}