<?php

namespace Gildie;

use Core\commands\MottoCommand;
use Gildie\guild\GuildManager;
use Gildie\task\NameTagsTask;
use Gildie\utils\ShapesUtils;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Gildie\commands\{GaCommand,
    GCommand,
    PermisjeCommand,
    SkarbiecCommand,
    ZalozCommand,
    InfoCommand,
    ZaprosCommand,
    DolaczCommand,
    OpuscCommand,
    UsunCommand,
    UstawbazeCommand,
    BazaCommand,
    LiderCommand,
    ZastepcaCommand,
    WyrzucCommand,
    PrzedluzCommand,
    PowiekszCommand,
    SojuszCommand,
    AkceptujCommand,
    RozwiazCommand,
    PvpCommand,
    WalkaCommand,
    ItemyCommand};
use pocketmine\block\Block;
use pocketmine\tile\{
    Tile, Chest as TileChest, Sign as TileSign
};

class Main extends PluginBase {

    private $db;
    private $guildManager;
    private $skarbiecConfig;

    public static $invite = [];
    public static $alliance = [];
    public static $bazaTask = [];

    private static $instance;

    public function onEnable() : void {

        date_default_timezone_set('Europe/Warsaw');

        self::$instance = $this;

        $this->registerCommands();

        $this->db = new \SQLite3($this->getDataFolder(). 'DataBase.db');
        $this->db->exec("CREATE TABLE IF NOT EXISTS players (player TEXT PRIMARY KEY, guild TEXT, rank TEXT)");
        $this->db->exec("CREATE TABLE IF NOT EXISTS guilds (guild TEXT PRIMARY KEY, name TEXT, lifes INT, base_x DOUBLE, base_y DOUBLE, base_z DOUBLE, heart_x INT, heart_y INT, heart_z INT, conquer_date TEXT, expiry_date TEXT, pvp_guild TEXT, pvp_alliances TEXT)");
        $this->db->exec("CREATE TABLE IF NOT EXISTS alliances (guild TEXT, alliance TEXT)");
        $this->db->exec("CREATE TABLE IF NOT EXISTS plots (guild TEXT PRIMARY KEY, size INT, x1 INT, z1 INT, x2 INT, z2 INT, max_x1 INT, max_z1 INT, max_x2 INT, max_z2 INT)");
        $this->guildManager = new GuildManager($this);

        $this->skarbiecConfig = new Config($this->getDataFolder().'Skarbce.yml', Config::YAML);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getLogger()->info("Plugin włączono");
    }

    public function onDisable() : void {
        $this->getLogger()->info("Plugin wyłączono");
    }

    private function registerCommands() : void {
        $cmds = [
            new GCommand(),
            new ZalozCommand(),
            new InfoCommand(),
            new ZaprosCommand(),
            new DolaczCommand(),
            new OpuscCommand(),
            new UsunCommand(),
            new UstawbazeCommand(),
            new BazaCommand(),
            new LiderCommand(),
            new ZastepcaCommand(),
            new WyrzucCommand(),
            new PrzedluzCommand(),
            new PowiekszCommand(),
            new SojuszCommand(),
            new AkceptujCommand(),
            new RozwiazCommand(),
            new PvpCommand(),
            new WalkaCommand(),
            new ItemyCommand(),
            new PermisjeCommand(),
            new SkarbiecCommand(),
            new GaCommand()
        ];

        $this->getServer()->getCommandMap()->registerAll("guild", $cmds);
    }

    public function getDb() : \SQLite3 {
        return $this->db;
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

    public function getGuildManager() : GuildManager {
        return $this->guildManager;
    }

    public function getSkarbiecConfig() : Config {
        return $this->skarbiecConfig;
    }
}