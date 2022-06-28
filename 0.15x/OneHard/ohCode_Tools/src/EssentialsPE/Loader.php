<?php
namespace EssentialsPE;

use EssentialsPE\BaseFiles\BaseKit;
use EssentialsPE\BaseFiles\BaseLocation;
use EssentialsPE\BaseFiles\BaseSession;
use EssentialsPE\BaseFiles\MessagesAPI;
use EssentialsPE\Commands\AFK;
use EssentialsPE\Commands\Antioch;
use EssentialsPE\Commands\Back;
use EssentialsPE\Commands\BreakCommand;
use EssentialsPE\Commands\Broadcast;
use EssentialsPE\Commands\Burn;
use EssentialsPE\Commands\ClearInventory;
use EssentialsPE\Commands\Compass;
use EssentialsPE\Commands\Depth;
#use EssentialsPE\Commands\Economy\Balance;
#use EssentialsPE\Commands\Economy\Eco;
#use EssentialsPE\Commands\Economy\Pay;
#use EssentialsPE\Commands\Economy\Sell;
#use EssentialsPE\Commands\Economy\SetWorth;
#use EssentialsPE\Commands\Economy\Worth;
use EssentialsPE\Commands\EssentialsPE;
use EssentialsPE\Commands\Extinguish;
use EssentialsPE\Commands\Fly;
use EssentialsPE\Commands\GetPos;
use EssentialsPE\Commands\God;
use EssentialsPE\Commands\Heal;
use EssentialsPE\Commands\Home\DelHome;
use EssentialsPE\Commands\Home\Home;
use EssentialsPE\Commands\Home\SetHome;
use EssentialsPE\Commands\ItemCommand;
use EssentialsPE\Commands\ItemDB;
use EssentialsPE\Commands\Jump;
use EssentialsPE\Commands\KickAll;
use EssentialsPE\Commands\Kit;
use EssentialsPE\Commands\More;
use EssentialsPE\Commands\Mute;
use EssentialsPE\Commands\Near;
use EssentialsPE\Commands\Nick;
use EssentialsPE\Commands\Nuke;
use EssentialsPE\Commands\Override\Gamemode;
use EssentialsPE\Commands\Override\Kill;
use EssentialsPE\Commands\Override\Msg;
use EssentialsPE\Commands\PowerTool\PowerTool;
use EssentialsPE\Commands\PowerTool\PowerToolToggle;
use EssentialsPE\Commands\PTime;
use EssentialsPE\Commands\PvP;
use EssentialsPE\Commands\RealName;
use EssentialsPE\Commands\Repair;
use EssentialsPE\Commands\Reply;
use EssentialsPE\Commands\Seen;
use EssentialsPE\Commands\SetSpawn;
use EssentialsPE\Commands\Spawn;
use EssentialsPE\Commands\Sudo;
use EssentialsPE\Commands\Suicide;
use EssentialsPE\Commands\Teleport\TPA;
use EssentialsPE\Commands\Teleport\TPAccept;
use EssentialsPE\Commands\Teleport\TPAHere;
use EssentialsPE\Commands\Teleport\TPAll;
use EssentialsPE\Commands\Teleport\TPDeny;
use EssentialsPE\Commands\Teleport\TPHere;
use EssentialsPE\Commands\TempBan;
use EssentialsPE\Commands\Top;
use EssentialsPE\Commands\Unlimited;
use EssentialsPE\Commands\Vanish;
use EssentialsPE\Commands\Warp\DelWarp;
use EssentialsPE\Commands\Warp\Setwarp;
use EssentialsPE\Commands\Warp\Warp;
use EssentialsPE\Commands\World;
use EssentialsPE\EventHandlers\OtherEvents;
use EssentialsPE\EventHandlers\PlayerEvents;
use EssentialsPE\EventHandlers\SignEvents;
use EssentialsPE\Events\PlayerAFKModeChangeEvent;
use EssentialsPE\Events\PlayerFlyModeChangeEvent;
use EssentialsPE\Events\PlayerGodModeChangeEvent;
use EssentialsPE\Events\PlayerMuteEvent;
use EssentialsPE\Events\PlayerNickChangeEvent;
use EssentialsPE\Events\PlayerPvPModeChangeEvent;
use EssentialsPE\Events\PlayerUnlimitedModeChangeEvent;
use EssentialsPE\Events\PlayerVanishEvent;
use EssentialsPE\Events\SessionCreateEvent;
use EssentialsPE\Tasks\AFK\AFKKickTask;
use EssentialsPE\Tasks\AFK\AFKSetterTask;
use EssentialsPE\Tasks\TPRequestTask;
use EssentialsPE\Tasks\TravisKillTask;
use EssentialsPE\Tasks\Updater\AutoFetchCallerTask;
use EssentialsPE\Tasks\Updater\UpdateFetchTask;
use EssentialsPE\Tasks\Updater\UpdateInstallTask;
use pocketmine\command\CommandSender;
use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;
use pocketmine\item\Tool;
use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\Byte;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Double;
use pocketmine\nbt\tag\Enum;
use pocketmine\network\protocol\MobEffectPacket;
use pocketmine\network\protocol\SetTimePacket;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\Random;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase{
    /** @var Config */
    private $economy;

    /** @var MessagesAPI
    private $messages;*/

    /** @var array */
    private $kits = [];

    /** @var array */
    private $warps = [];

    public function onEnable(){
        if(!is_dir($this->getDataFolder())){
            mkdir($this->getDataFolder());
        }
        $this->checkConfig();
        $this->saveConfigs();
	    $this->getLogger()->info(TextFormat::YELLOW . "Loading...");
        $this->registerEvents();
        $this->registerCommands();

        foreach($this->getServer()->getOnlinePlayers() as $p){
            //Sessions & Mute
            $this->createSession($p);
        }

        if($this->isUpdaterEnabled()){
            $this->fetchEssentialsPEUpdate(false);
        }
        $this->scheduleAutoAFKSetter();
        if(getenv("TRAVIS") !== false){
            $this->getLogger()->info("Scheduling TravisKillTask...");
            $this->getServer()->getScheduler()->scheduleDelayedTask(new TravisKillTask($this), 5);
        }
    }

    public function onDisable(){
        foreach($this->getServer()->getOnlinePlayers() as $p){
            //Sessions
            $this->removeSession($p);
        }

        $this->encodeWarps(true);
    }

    /**
     * Function to register all the Event Handlers that EssentialsPE provide
     */
    public function registerEvents(){
        $this->getServer()->getPluginManager()->registerEvents(new OtherEvents($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new PlayerEvents($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new SignEvents($this), $this);
    }

    /**
     * Function to easily disable commands
     *
     * @param array $commands
     */
    private function unregisterCommands(array $commands){
        $commandmap = $this->getServer()->getCommandMap();

        foreach($commands as $commandlabel){
            $command = $commandmap->getCommand($commandlabel);
            $command->setLabel($commandlabel . "_disabled");
            $command->unregister($commandmap);
        }
    }

    /**
     * Function to register all EssentialsPE's commands...
     * And to override some default ones
     */
    private function registerCommands(){
        //Unregister commands to override
        $this->unregisterCommands([
           "gamemode",
            "kill",
            "tell"
        ]);

        //Register the new commands
        $this->getServer()->getCommandMap()->registerAll("EssentialsPE", [
            new AFK($this),
            new Antioch($this),
            new Back($this),
            //new BigTreeCommand($this), //TODO
            new BreakCommand($this),
            new Broadcast($this),
            new Burn($this),
            new ClearInventory($this),
            new Compass($this),
            new Depth($this),
            new EssentialsPE($this),
            new Extinguish($this),
            new Fly($this),
            new GetPos($this),
            new God($this),
            new Heal($this),
            new ItemCommand($this),
            new ItemDB($this),
            new Jump($this),
            new KickAll($this),
            new Kit($this),
            new More($this),
            new Mute($this),
            new Near($this),
            new Nick($this),
            new Nuke($this),
            new PTime($this),
            new PvP($this),
            new RealName($this),
            new Repair($this),
            new Seen($this),
            new SetSpawn($this),
            new Spawn($this),
            new Sudo($this),
            new Suicide($this),
            new TempBan($this),
            new Top($this),
            //new TreeCommand($this), //TODO
            new Unlimited($this),
            new Vanish($this),
            new World($this),

            //Economy
            //new Balance($this),
            //new Eco($this),
            //new Pay($this),
            //new Sell($this),
            //new SetWorth($this),
            //new Worth($this),

            //Home
            new DelHome($this),
            new Home($this),
            new SetHome($this),

            // Messages
            new Msg($this),
            new Reply($this),

            //PowerTool
            new PowerTool($this),
            new PowerToolToggle($this),

            //Teleport
            new TPA($this),
            new TPAccept($this),
            new TPAHere($this),
            new TPAll($this),
            new TPDeny($this),
            new TPHere($this),

            //Warp
            new DelWarp($this),
            new Setwarp($this),
            new Warp($this),

            //Override
            new Gamemode($this),
            new Kill($this)
        ]);
    }

    public function checkConfig(){
        if(!file_exists($this->getDataFolder() . "config.yml")){
            $this->saveDefaultConfig();
        }
        //$this->saveResource("Economy.yml");
        $this->saveResource("Kits.yml");
        $cfg = $this->getConfig();

        if(!$cfg->exists("version") || $cfg->get("version") !== "0.0.1"){
            $this->getLogger()->debug(TextFormat::RED . "An invalid config file was found, generating a new one...");
            unlink($this->getDataFolder() . "config.yml");
            $this->saveDefaultConfig();
            $cfg = $this->getConfig();
        }

        $booleans = ["enable-custom-colors"];
        foreach($booleans as $key){
            $value = null;
            if(!$cfg->exists($key) || !is_bool($cfg->get($key))){
                switch($key){
                    // Properties to auto set true
                    case "safe-afk":
                        $value = true;
                        break;
                    // Properties to auto set false
                    case "enable-custom-colors":
                        $value = false;
                        break;
                }
            }
            if($value !== null){
                $cfg->set($key, $value);
            }
        }

        $integers = ["oversized-stacks", "near-radius-limit", "near-default-radius"];
        foreach($integers as $key){
            $value = null;
            if(!is_numeric($cfg->get($key))){
                switch($key){
                    case "auto-afk-kick":
                        $value = 300;
                        break;
                    case "oversized-stacks":
                        $value = 64;
                        break;
                    case "near-radius-limit":
                        $value = 200;
                        break;
                    case "near-default-radius":
                        $value = 100;
                        break;
                }
            }
            if($value !== null){
                $cfg->set($key, $value);
            }
        }

        $afk = ["safe", "auto-set", "auto-broadcast", "auto-kick", "broadcast"];
        foreach($afk as $key){
            $value = null;
            $k = $this->getConfig()->getNested("afk." . $key);
            switch($key){
                case "safe":
                case "auto-broadcast":
                case "broadcast":
                    if(!is_bool($k)){
                        $value = true;
                    }
                    break;
                case "auto-set":
                case "auto-kick":
                    if(!is_int($k)){
                        $value = 300;
                    }
                    break;
            }
            if($value !== null){
                $this->getConfig()->setNested("afk." . $key, $value);
            }
        }

        $updater = ["enabled", "time-interval", "warn-console", "warn-players", "stable"];
        foreach($updater as $key){
            $value = null;
            $k = $this->getConfig()->getNested("updater." . $key);
            switch($key){
                case "time-interval":
                    if(!is_int($k)){
                        $value = 1800;
                    }
                    break;
                case "enabled":
                case "warn-console":
                case "warn-players":
                case "stable":
                    if(!is_bool($k)){
                        $value = true;
                    }
                    break;
            }
            if($value !== null){
                $this->getConfig()->setNested("updater." . $key, $value);
            }
        }
    }

    private function saveConfigs(){
        /**$this->economy = new Config($this->getDataFolder() . "Economy.yml", Config::YAML);
        $keys = ["default-balance", "max-money", "min-money"];
        foreach($keys as $k){
            if(!is_int($k)){
                $value = 0;
                switch($k){
                    case "default-balance":
                        $value = 0;
                        break;
                    case "max-money":
                        $value = 10000000000000;
                        break;
                    case "min-money":
                        $value = -10000;
                        break;
                }
                $this->economy->set($k, $value);
            }
        }*/

        $this->loadKits();
        $this->loadWarps();
        $this->updateHomesAndNicks();
    }

    private function updateHomesAndNicks(){
        if(file_exists($f = $this->getDataFolder() . "Homes.yml")){
            $cfg = new Config($f, Config::YAML);
            foreach($cfg->getAll() as $player => $home){
                if(!is_array($home)){
                    continue;
                }
                $pCfg = $this->getSessionFile($player);
                foreach($home as $name => $values){
                    if(!$this->validateName($name, false) || !is_array($values)){
                        continue;
                    }
                    $pCfg->setNested("homes." . $name, $values);
                }
                $pCfg->save();
            }
            unlink($f);
        }
        if(file_exists($f = $this->getDataFolder() . "Nicks.yml")){
            $cfg = new Config($f, Config::YAML);
            foreach($cfg->getAll() as $player => $nick){
                $pCfg = $this->getSessionFile($player);
                $pCfg->set("nick", $nick);
                $pCfg->save();
            }
            unlink($f);
        }
    }

    private function loadKits(){
        $cfg = new Config($this->getDataFolder() . "Kits.yml", Config::YAML);
        $children = [];
        foreach($cfg->getAll() as $n => $i){
            $this->kits[$n] = new BaseKit($n, $i);
            $children[] = new Permission("essentials.kits." . $n);
        }
        $this->getServer()->getPluginManager()->addPermission(new Permission("essentials.kits", null, null, $children));
    }

    private function loadWarps(){
        $cfg = new Config($this->getDataFolder() . "Warps.yml", Config::YAML);
        $cfg->reload();
        $children = [];
        foreach($cfg->getAll() as $n => $v){
            if($this->getServer()->isLevelGenerated($v[3])){
                if(!$this->getServer()->isLevelLoaded($v[3])){
                    $this->getServer()->loadLevel($v[3]);
                }
                $this->warps[$n] = new BaseLocation($n, $v[0], $v[1], $v[2], $this->getServer()->getLevelByName($v[3]), $v[4], $v[5]);
                $children[] = new Permission("essentials.warps." . $n);
            }
        }
        $this->getServer()->getPluginManager()->addPermission(new Permission("essentials.warps", null, null, $children));
    }

    /**
     * @param bool $save
     */
    private function encodeWarps($save = false){
        $warps = [];
        foreach($this->warps as $name => $object){
            if($object instanceof BaseLocation){
                $warps[$name] = [$object->getX(), $object->getY(), $object->getZ(), $object->getLevel()->getName(), $object->getYaw(), $object->getPitch()];
            }
        }
        if($save){
            $cfg = new Config($this->getDataFolder() . "Warps.yml", Config::YAML);
            $cfg->setAll($warps);
            $cfg->save();
        }
        $this->warps = $warps;
    }

    public function reloadFiles(){
        $this->getConfig()->reload();
        //$this->economy->reload();
        $this->loadKits();
        $this->loadWarps();
        $this->updateHomesAndNicks();
    }

    /*
     *  .----------------.  .----------------.  .----------------.
     * | .--------------. || .--------------. || .--------------. |
     * | |      __      | || |   ______     | || |     _____    | |
     * | |     /  \     | || |  |_   __ \   | || |    |_   _|   | |
     * | |    / /\ \    | || |    | |__) |  | || |      | |     | |
     * | |   / ____ \   | || |    |  ___/   | || |      | |     | |
     * | | _/ /    \ \_ | || |   _| |_      | || |     _| |_    | |
     * | ||____|  |____|| || |  |_____|     | || |    |_____|   | |
     * | |              | || |              | || |              | |
     * | '--------------' || '--------------' || '--------------' |
     *  '----------------'  '----------------'  '----------------'
     *
     */

    /**
     * Let you search for a player using his Display name(Nick) or Real name
     *
     * @param string $player
     * @return bool|Player
     */
    public function getPlayer($player){
        if(!$this->validateName($player, false)){
            return false;
        }
        $player = strtolower($player);
        $found = false;
        foreach($this->getServer()->getOnlinePlayers() as $p){
            if(strtolower(TextFormat::clean($p->getDisplayName(), true)) === $player || strtolower($p->getName()) === $player){
                $found = $p;
                break;
            }
        }
        // If cannot get the exact player name/nick, try with portions of it
        if(!$found){
            $found = ($f = $this->getServer()->getPlayer($player)) === null ? false : $f; // PocketMine function to get from portions of name
        }
        /*
         * Copy from PocketMine's function (use above xD) but modified to work with Nicknames :P
         *
         * ALL THE RIGHTS FROM THE FOLLOWING CODE BELONGS TO POCKETMINE-MP
         */
        if(!$found){
            $delta = \PHP_INT_MAX;
            foreach($this->getServer()->getOnlinePlayers() as $p){
                // Clean the Display Name due to colored nicks :S
                if(\stripos(($n = TextFormat::clean($p->getDisplayName(), true)), $player) === 0){
                    $curDelta = \strlen($n) - \strlen($player);
                    if($curDelta < $delta){
                        $found = $p;
                        $delta = $curDelta;
                    }
                    if($curDelta === 0){
                        break;
                    }
                }
            }
        }
        return $found;
    }

    /**
     * Return a colored message replacing every
     * color code (&a = §a)
     *
     * @param string $message
     * @param Player|null $player
     * @return bool|string
     */
    public function colorMessage($message, Player $player = null){
        $message = preg_replace_callback(
            "/(\\\&|\&)[0-9a-fk-or]/",
            function(array $matches){
                return str_replace("\\§", "&", str_replace("&", "§", $matches[0]));
            },
            $message
        );
        if(strpos($message, "§") !== false && ($player instanceof Player) && !$player->hasPermission("essentials.colorchat")){
            $player->sendMessage(TextFormat::RED . "You can't chat using colors!");
            return false;
        }
        return $message;
    }

    /**
     * @param Item $item
     * @return bool
     * @deprecated
     */
    public function isReparable(Item $item){
        return $this->isRepairable($item);
    }
    /**
     * Let you know if the item is a Tool or Armor
     * (Items that can get "real damage")
     *
     * @param Item $item
     * @return bool
     */
    public function isRepairable(Item $item){
        return $item instanceof Tool || $item instanceof Armor;
    }


    /**
     * Let you see who is near a specific player
     *
     * @param Player $player
     * @param int $radius
     * @return bool|Player[]
     */
    public function getNearPlayers(Player $player, $radius = null){
        if($radius === null || !is_int((int) $radius)){
            $radius = $this->getConfig()->get("near-default-radius");
        }
        if(!is_numeric($radius)){
            return false;
        }
        /** @var Player[] $players */
        $players = [];
        foreach($player->getLevel()->getNearbyEntities(new AxisAlignedBB($player->getFloorX() - $radius, $player->getFloorY() - $radius, $player->getFloorZ() - $radius, $player->getFloorX() + $radius, $player->getFloorY() + $radius, $player->getFloorZ() + $radius), $player) as $e){
            if($e instanceof Player){
                $players[] = $e;
            }
        }
        return $players;
    }

    /**
     * Change the time of a player
     *
     * @param Player $player
     * @param $time
     * @param bool $static
     * @return bool
     */
    public function setPlayerTime(Player $player, $time, $static = false){
        if(!is_int((int) $time) || !is_bool($static)){
            return false;
        }
        $pk = new SetTimePacket();
        $pk->time = $time;
        $pk->started = ($static === false);
        $pk->encode();
        $pk->isEncoded = true;
        $player->dataPacket($pk);
        if(isset($pk->__encapsulatedPacket)){
            unset($pk->__encapsulatedPacket);
        }
        return true;
    }

    /**
     * Easy get an item by name and metadata.
     * The way this function understand the information about the item is:
     * 'ItemNameOrID:Metadata' - Example (Granite block item):
     *      '1:1' - or - 'stone:1'
     *
     * @param $item_name
     * @return Item|ItemBlock
     */
    public function getItem($item_name){
        if(strpos($item_name, ":") !== false){
            $v = explode(":", $item_name);
            $item_name = $v[0];
            $damage = $v[1];
        }else{
            $damage = 0;
        }

        if(!is_numeric($item_name)){
            $item = Item::fromString($item_name);
        }else{
            $item = Item::get($item_name);
        }
        $item->setDamage($damage);

        return $item;
    }

    /**
     * Return an array with the following values:
     * 0 => Timestamp integer
     * 1 => The rest of the string (removing any "space" between time codes)
     *
     * @param string $string
     * @return array|bool
     */
    public function stringToTimestamp($string){
        /**
         * Rules:
         * Integers without suffix are considered as seconds
         * "s" is for seconds
         * "m" is for minutes
         * "h" is for hours
         * "d" is for days
         * "w" is for weeks
         * "mo" is for months
         * "y" is for years
         */
        if(trim($string) === ""){
            return false;
        }
        $t = new \DateTime();
        preg_match_all("/[0-9]+(y|mo|w|d|h|m|s)|[0-9]+/", $string, $found);
        if(count($found[0]) < 1){
            return false;
        }
        $found[2] = preg_replace("/[^0-9]/", "", $found[0]);
        foreach($found[2] as $k => $i){
            switch($c = $found[1][$k]){
                case "y":
                case "w":
                case "d":
                    $t->add(new \DateInterval("P" . $i. strtoupper($c)));
                    break;
                case "mo":
                    $t->add(new \DateInterval("P" . $i. strtoupper(substr($c, 0, strlen($c) -1))));
                    break;
                case "h":
                case "m":
                case "s":
                    $t->add(new \DateInterval("PT" . $i . strtoupper($c)));
                    break;
                default:
                    $t->add(new \DateInterval("PT" . $i . "S"));
                    break;
            }
            $string = str_replace($found[0][$k], "", $string);
        }
        return [$t, ltrim(str_replace($found[0], "", $string))];
    }

    /**
     * Checks if a name is valid, it could be for a Nick, Home, Warp, etc...
     *
     * @param string $string
     * @param bool $allowColorCodes
     * @return bool
     */
    public function validateName($string, $allowColorCodes = false){
        if(trim($string) === ""){
            return false;
        }
        $format = [];
        if($allowColorCodes){
            $format[] = "/(\&|\§)[0-9a-fk-or]/";
        }
        $format[] = "/[a-zA-Z0-9_]/"; // Due to color codes can be allowed, then check for them first, so after, make a normal lookup
        $s = preg_replace($format, "", $string);
        if(strlen($s) !== 0){
            return false;
        }
        return true;
    }

    /**   _____              _
     *   / ____|            (_)
     *  | (___   ___ ___ ___ _  ___  _ __  ___
     *   \___ \ / _ / __/ __| |/ _ \| '_ \/ __|
     *   ____) |  __\__ \__ | | (_) | | | \__ \
     *  |_____/ \___|___|___|_|\___/|_| |_|___/
     */

    /** @var array  */
    private $sessions = [];

    /**
     * Tell if a session exists for a specific player
     *
     * @param Player $player
     * @return bool
     */
    public function sessionExists(Player $player){
        return isset($this->sessions[spl_object_hash($player)]);
    }

    /**
     * Creates a new Sessions for the specified player
     *
     * @param Player $player
     * @return BaseSession
     */
    public function createSession(Player $player){
        $spl = spl_object_hash($player);
        if(!isset($this->sessions[$spl])){
            $this->getLogger()->debug("Creating player session file...");
            $cfg = $this->getSessionFile($player->getName());
            $tValues = $cfg->getAll();
            $values = BaseSession::$defaults;
            foreach($tValues as $k => $v){
                $values[$k] = $v;
            }
            $this->getLogger()->debug("Creating virtual session...");
            $this->getServer()->getPluginManager()->callEvent($ev = new SessionCreateEvent($this, $player, $values));
            $this->getLogger()->debug("Setting up new values...");
            $values = $ev->getValues();
            $m = BaseSession::$defaults["isMuted"];
            $mU = BaseSession::$defaults["mutedUntil"];
            if(isset($values["isMuted"])){
                if(!isset($values["mutedUntil"])){
                    $values["mutedUntil"] = null;
                }
                $m = $values["isMuted"];
                if(is_int($t = $values["mutedUntil"])){
                    $date = new \DateTime();
                    $mU = date_timestamp_set($date, $values["mutedUntil"]);
                }else{
                    $mU = $values["mutedUntil"];
                }
                unset($values["isMuted"]);
                unset($values["mutedUntil"]);
            }
            $n = $player->getName();
            if(isset($values["nick"])){
                $n = $values["nick"];
                $this->getLogger()->info($player->getName() . " is also known as " . $n);
                unset($values["nick"]);
            }
            $v = BaseSession::$defaults["isVanished"];
            $vNP = BaseSession::$defaults["noPacket"];
            if(isset($values["isVanished"])){
                if(!isset($values["noPacket"])){
                    $values["noPacket"] = false;
                }
                $v = $values["isVanished"];
                $vNP = $values["noPacket"];
                unset($values["isVanished"]);
                unset($values["noPacket"]);
            }
            $this->getLogger()->debug("Setting up final values...");
            $this->sessions[$spl] = new BaseSession($this, $player, $cfg, $values);
            $this->setMute($player, $m, $mU);
            $this->setNick($player, $n);
            $this->setVanish($player, $v, $vNP);
        }
        return $this->sessions[$spl];
    }

    /**
     * @param $player
     * @return bool|Config
     */
    private function getSessionFile($player){
        if(!is_dir($dir = $this->getDataFolder() . "Sessions" . DIRECTORY_SEPARATOR)){
            mkdir($dir);
        }
        if(!is_dir($dir = $dir . strtolower(substr($player, 0, 1)) . DIRECTORY_SEPARATOR)){
            mkdir($dir);
        }
        return new Config($dir . strtolower($player) . ".session", Config::JSON, BaseSession::$configDefaults);
    }

    /**
     * Remove player's session (if active and available)
     *
     * @param Player $player
     */
    public function removeSession(Player $player){
        $spl = spl_object_hash($player);
        if(isset($this->sessions[$spl])){
            $this->getSession($player)->onClose();
            unset($this->sessions[$spl]);
        }
    }

    /**
     * @param Player $player
     * @return BaseSession
     */
    private function getSession(Player $player){
        if(!$this->sessionExists($player)){
            $this->createSession($player);
        }
        return $this->sessions[spl_object_hash($player)];
    }

    /**
     *            ______ _  __
     *      /\   |  ____| |/ /
     *     /  \  | |__  | ' /
     *    / /\ \ |  __| |  <
     *   / ____ \| |    | . \
     *  /_/    \_|_|    |_|\_\
     */

    /**
     * Tell if the player is AFK or not
     *
     * @param Player $player
     * @return bool
     */
    public function isAFK(Player $player){
        return $this->getSession($player)->isAFK();
    }

    /**
     * Change the AFK mode of a player
     *
     * @param Player $player
     * @param bool $state
     * @param bool $broadcast
     * @return bool
     */
    public function setAFKMode(Player $player, $state, $broadcast = true){
        if(!is_bool($state)){
            return false;
        }
        $this->getServer()->getPluginManager()->callEvent($ev = new PlayerAFKModeChangeEvent($this, $player, $state, $broadcast));
        if($ev->isCancelled()){
            return false;
        }
        $state = $ev->getAFKMode();
        $this->getSession($player)->setAFK($state);
        $time = $this->getConfig()->getNested("afk.auto-kick");
        if($state === false && ($id = $this->getSession($player)->getAFKKickTaskID()) !== false){
            $this->getServer()->getScheduler()->cancelTask($id);
            $this->getSession($player)->removeAFKKickTaskID();
        }elseif($state === true and (is_int($time) and $time  > 0) and !$player->hasPermission("essentials.afk.kickexempt")){
            $task = $this->getServer()->getScheduler()->scheduleDelayedTask(new AFKKickTask($this, $player), ($time * 20));
            $this->getSession($player)->setAFKKickTaskID($task->getTaskId());
        }
        $player->sendMessage(TextFormat::YELLOW . "You're " . ($this->isAFK($player) ? "now" : "no longer") . " AFK");
        if($ev->getBroadcast()){
            $this->broadcastAFKStatus($player);
        }
        return true;
    }

    /**
     * Automatically switch the AFK mode on/off
     *
     * @param Player $player
     * @param bool $broadcast
     */
    public function switchAFKMode(Player $player, $broadcast = true){
        $this->setAFKMode($player, !$this->isAFK($player), $broadcast);
    }

    /**
     * For internal use ONLY
     *
     * This function schedules the global Auto-AFK setter
     */
    public function scheduleAutoAFKSetter(){
        if(is_int($v = $this->getConfig()->getNested("afk.auto-set")) && $v > 0){
            $this->getServer()->getScheduler()->scheduleDelayedTask(new AFKSetterTask($this), (600)); // Check every 30 seconds...
        }
    }

    /**
     * Get the last time that a player moved
     *
     * @param Player $player
     * @return int|null
     */
    public function getLastPlayerMovement(Player $player){
        return $this->getSession($player)->getLastMovement();
    }

    /**
     * Change the last time that a player moved
     *
     * @param Player $player
     * @param int $time
     */
    public function setLastPlayerMovement(Player $player, $time){
        if(!$player->hasPermission("essentials.afk.preventauto")){
            $this->getSession($player)->setLastMovement($time);
        }
    }

    /**
     * Broadcast the AFK status of a player
     *
     * @param Player $player
     */
    public function broadcastAFKStatus(Player $player){
        if(!$this->getConfig()->getNested("afk.broadcast")){
            return;
        }
        $message = TextFormat::YELLOW . $player->getDisplayName() . " is " . ($this->isAFK($player) ? "now" : "no longer") . " AFK";
        $this->getServer()->getLogger()->info($message);
        foreach($this->getServer()->getOnlinePlayers() as $p){
            if($p !== $player){
                $p->sendMessage($message);
            }
        }
    }

    /**  ____             _
     *  |  _ \           | |
     *  | |_) | __ _  ___| | __
     *  |  _ < / _` |/ __| |/ /
     *  | |_) | (_| | (__|   <
     *  |____/ \__,_|\___|_|\_\
     */

    /**
     * Return the last known spot of a player before teleporting
     *
     * @param Player $player
     * @return bool|Location
     */
    public function getLastPlayerPosition(Player $player){
        return $this->getSession($player)->getLastPosition();
    }

    /**
     * Updates the last position of a player.
     *
     * @param Player $player
     * @param Location $pos
     */
    public function setPlayerLastPosition(Player $player, Location $pos){
        $this->getSession($player)->setLastPosition($pos);
    }

    /**
     * @param Player $player
     */
    public function removePlayerLastPosition(Player $player){
        $this->getSession($player)->removeLastPosition();
    }

    /**  ______
     *  |  ____|
     *  | |__   ___ ___  _ __   ___  _ __ ___  _   _
     *  |  __| / __/ _ \| '_ \ / _ \| '_ ` _ \| | | |
     *  | |___| (_| (_) | | | | (_) | | | | | | |_| |
     *  |______\___\___/|_| |_|\___/|_| |_| |_|\__, |
     *                                          __/ |
     *                                         |___/
     */

    /**
     * Get the default balance for new players
     *
     * @return int
     */
    public function getDefaultBalance(){
        return $this->getConfig()->get("default-balance");
    }

    /**
     * Get the max balance that a player can own
     *
     * @return bool|mixed
     */
    public function getMaxBalance(){
        return $this->economy->get("max-money");
    }

    /**
     * Gets the minium balance that a player can own
     *
     * @return bool|mixed
     */
    public function getMinBalance(){
        return $this->economy->get("min-money");
    }

    /**
     * Returns the currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol(){
        return $this->economy->get("currency-symbol");
    }

    /**
     * Return the current balance of a player.
     *
     * @param Player $player
     * @return int
     */
    public function getPlayerBalance(Player $player){
        $balance = $this->economy->getNested("player-balances." . $player->getName());
        if(!$balance){
            $this->setPlayerBalance($player, $b = $this->getDefaultBalance());
            return $b;
        }
        return $balance;
    }

    /**
     * Sets the balance of a player
     *
     * @param Player $player
     * @param $balance
     */
    public function setPlayerBalance(Player $player, $balance){
        if((int) $balance > (int) $this->getMaxBalance()){
            $balance = (int) $this->getMaxBalance();
        }elseif((int) $balance < (int) $this->getMinBalance()){
            $balance = (int) $this->getMinBalance();
        }elseif((int) $balance < 0 && !$player->hasPermission("essentials.eco.load")){
            $balance = 0;
        }
        $this->economy->setNested("player-balances." . $player->getName(), (int) $balance);
        $this->economy->save();
    }

    /**
     * Sums a quantity to player's balance
     * NOTE: You can also specify negative quantities!
     *
     * @param Player $player
     * @param $quantity
     */
    public function addToPlayerBalance(Player $player, $quantity){
        $balance = $this->getPlayerBalance($player) + (int) $quantity;
        if($balance > $this->getMaxBalance()){
            $balance = $this->getMaxBalance();
        }elseif($balance < $this->getMinBalance()){
            $balance = $this->getMinBalance();
        }elseif($balance < 0 && !$player->hasPermission("essentials.eco.loan")){
            $balance = 0;
        }
        $this->setPlayerBalance($player, $balance);
    }

    /**
     * Get the worth of an item
     *
     * @param $itemId
     * @return bool|int
     */
    public function getItemWorth($itemId){
        return $this->economy->getNested("worth." . (int) $itemId, false);
    }

    /**
     * Sets the worth of an item
     *
     * @param $itemId
     * @param $worth
     */
    public function setItemWorth($itemId, $worth){
        $this->economy->setNested("worth." . (int) $itemId, (int) $worth);
        $this->economy->save();
    }

    /**
     * @param Player $player
     * @param Item $item
     * @param int|null $amount
     * @return array|bool|int
     */
    public function sellPlayerItem(Player $player, Item $item, $amount = null){
        if(!$this->getItemWorth($item->getId())){
            return false;
        }
        /** @var Item[] $contents */
        $contents = [];
        $quantity = 0;
        foreach($player->getInventory()->getContents() as $s => $i){
            if($i->getId() === $item->getId() && $i->getDamage() === $item->getDamage()){
                $contents[$s] = clone $i;
                $quantity += $i->getCount();
            }
        }
        $worth = $this->getItemWorth($item->getId());
        if($amount === null){
            $worth = $worth * $quantity;
            $player->getInventory()->remove($item);
            $this->addToPlayerBalance($player, $worth);
            return $worth;
        }
        $amount = (int) $amount;
        if($amount < 0){
            $amount = $quantity - $amount;
        }elseif($amount > $quantity){
            return -1;
        }

        $count = $amount;
        foreach($contents as $s => $i){
            if(($count - $i->getCount()) >= 0){
                $count = $count - $i->getCount();
                $i->setCount(0);
            }else{
                $c = $i->getCount() - $count;
                $i->setCount($c);
                $count = 0;
            }
            if($count <= 0){
                break;
            }
        }
        return [$amount, $worth];
    }

    /**  ______       _   _ _   _
     *  |  ____|     | | (_| | (_)
     *  | |__   _ __ | |_ _| |_ _  ___ ___
     *  |  __| | '_ \| __| | __| |/ _ / __|
     *  | |____| | | | |_| | |_| |  __\__ \
     *  |______|_| |_|\__|_|\__|_|\___|___/
     */

    /**
     * @param Player $player
     * @return bool
     */
    public function antioch(Player $player){
        $block = $player->getTargetBlock(100, [0, 8, 9, 10, 11]);
        if($block === null){
            return false;
        }
        $this->createTNT($block->add(0, 1), $player->getLevel());
        return true;
    }

    /**
     * Spawn a carpet of bomb!
     *
     * @param Player $player
     */
    public function nuke(Player $player){
        for($x = -10; $x <= 10; $x += 5){
            for($z = -10; $z <= 10; $z += 5){
                $this->createTNT($player->add($x, 0, $z), $player->getLevel());
            }
        }
    }

    /**
     * @param Vector3|Position $pos
     * @param null|Level $level
     */
    public function createTNT(Vector3 $pos, Level $level = null){
        if($level === null){
            if($pos instanceof Position){
                $level = $pos->getLevel();
            }else{
                return;
            }
        }
        $mot = (new Random())->nextSignedFloat() * M_PI * 2;
        $entity = Entity::createEntity("PrimedTNT", $level->getChunk($pos->x >> 4, $pos->z >> 4), new Compound("EssNuke", [
            "Pos" => new Enum("Pos", [
                new Double("", $pos->getFloorX() + 0.5),
                new Double("", $pos->getFloorY()),
                new Double("", $pos->getFloorZ() + 0.5)
            ]),
            "Motion" => new Enum("Motion", [
                new Double("", -sin($mot) * 0.02),
                new Double("", 0.2),
                new Double("", -cos($mot) * 0.02)
            ]),
            "Rotation" => new Enum("Rotation", [
                new Float("", 0),
                new Float("", 0)
            ]),
            "Fuse" => new Byte("Fuse", 80),
        ]));
        $entity->spawnToAll();
    }

    /**  ______ _
     *  |  ____| |
     *  | |__  | |_   _
     *  |  __| | | | | |
     *  | |    | | |_| |
     *  |_|    |_|\__, |
     *             __/ |
     *            |___/
     */

    /**
     * Get the "Can fly" status of a player
     *
     * @param Player $player
     * @return bool
     */
    public function canFly(Player $player){
        return $player->getAllowFlight();
    }

    /**
     * Set the "flying" allowed status to a player
     *
     * @param Player $player
     * @param bool $mode
     * @return bool
     */
    public function setFlying(Player $player, $mode){
        $this->getServer()->getPluginManager()->callEvent($ev = new PlayerFlyModeChangeEvent($this, $player, $mode));
        if($ev->isCancelled()){
            return false;
        }
        $player->setAllowFlight($ev->willFly());
        return true;
    }

    /**
     * Automatically switch a player between "Can" and "Can't" fly
     *
     * @param Player $player
     */
    public function switchCanFly(Player $player){
        $this->setFlying($player, !$this->canFly($player));
    }

    /**   _____           _
     *   / ____|         | |
     *  | |  __  ___   __| |
     *  | | |_ |/ _ \ / _` |
     *  | |__| | (_) | (_| |
     *   \_____|\___/ \__,_|
     */

    /**
     * Tell if a player is in God Mode
     *
     * @param Player $player
     * @return bool
     */
    public function isGod(Player $player){
        return $this->getSession($player)->isGod();
    }

    /**
     * Set the God Mode on or off
     *
     * @param Player $player
     * @param bool $state
     * @return bool
     */
    public function setGodMode(Player $player, $state){
        if(!is_bool($state)){
            return false;
        }
        $this->getServer()->getPluginManager()->callEvent($ev = new PlayerGodModeChangeEvent($this, $player, $state));
        if($ev->isCancelled()){
            return false;
        }
        $this->getSession($player)->setGod($ev->getGodMode());
        return true;
    }

    /**
     * Switch God Mode on/off automatically
     *
     * @param Player $player
     */
    public function switchGodMode(Player $player){
        $this->setGodMode($player, !$this->isGod($player));
    }

    /**  _    _
     *  | |  | |
     *  | |__| | ___  _ __ ___   ___ ___
     *  |  __  |/ _ \| '_ ` _ \ / _ / __|
     *  | |  | | (_) | | | | | |  __\__ \
     *  |_|  |_|\___/|_| |_| |_|\___|___/
     */

    /**
     * Tell is a player have a specific home by its name
     *
     * @param Player $player
     * @param string $home
     * @return bool
     */
    public function homeExists(Player $player, $home){
        return $this->sessionExists($player) && $this->getSession($player)->homeExists($home);
    }

    /**
     * Return the home information (Position and Rotation)
     *
     * @param Player $player
     * @param string $home
     * @return bool|BaseLocation
     */
    public function getHome(Player $player, $home){
        return $this->getSession($player)->getHome($home);
    }

    /**
     * Create or update a home
     *
     * @param Player $player
     * @param string $home
     * @param Position $pos
     * @param int $yaw
     * @param int $pitch
     * @return bool
     */
    public function setHome(Player $player, $home, Position $pos, $yaw = 0, $pitch = 0){
        return $this->getSession($player)->setHome($home, ($pos instanceof Location ? $pos : Location::fromObject($pos, $pos->getLevel(), $yaw, $pitch)));
    }

    /**
     * Removes a home
     *
     * @param Player $player
     * @param string $home
     * @return bool
     */
    public function removeHome(Player $player, $home){
        return $this->getSession($player)->removeHome($home);
    }

    /**
     * Return a list of all the available homes of a certain player
     *
     * @param Player $player
     * @param bool $inArray
     * @return array|bool|string
     */
    public function homesList(Player $player, $inArray = false){
        return $this->getSession($player)->homesList($inArray);
    }

    /**  _  ___ _
     *  | |/ (_| |
     *  | ' / _| |_ ___
     *  |  < | | __/ __|
     *  | . \| | |_\__ \
     *  |_|\_|_|\__|___/
     */

    /**
     * Check if a kit exists
     *
     * @param $kit
     * @return bool
     */
    public function kitExists($kit){
        return $this->validateName($kit, false) && isset($this->kits[$kit]);
    }

    /**
     * Return the contents of a kit, if existent
     *
     * @param $kit
     * @return bool|BaseKit
     */
    public function getKit($kit){
        if(!$this->kitExists($kit)){
            return false;
        }
        return $this->kits[$kit];
    }

    /**
     * Get a list of all available kits
     *
     * @param bool $inArray
     * @return array|bool|string
     */
    public function kitList($inArray = false){
        $list = array_keys($this->kits);
        if(count($list) < 1){
            return false;
        }
        if(!$inArray){
            return implode(", ", $list);
        }
        return $list;
    }

    /**  __  __
     *  |  \/  |
     *  | \  / | ___ ___ ___  __ _  __ _  ___ ___
     *  | |\/| |/ _ / __/ __|/ _` |/ _` |/ _ / __|
     *  | |  | |  __\__ \__ | (_| | (_| |  __\__ \
     *  |_|  |_|\___|___|___/\__,_|\__, |\___|___/
     *                              __/ |
     *                             |___/
     */

    // TODO: Multi-Language API

    /**  __  __
     *  |  \/  |
     *  | \  / |___  __ _
     *  | |\/| / __|/ _` |
     *  | |  | \__ | (_| |
     *  |_|  |_|___/\__, |
     *               __/ |
     *              |___/
     */

    private $quickReply = [
        "console" => false,
        "rcon" => false
    ];

    /**
     * Get the target for QuickReply, in string...
     *
     * @param CommandSender $sender
     * @return bool|string
     */
    public function getQuickReply(CommandSender $sender){
        if($sender instanceof Player){
            $q = $this->getSession($sender)->getQuickReply();
        }else{
            $q = $this->quickReply[strtolower($sender->getName())];
        }
        return $q;
    }

    /**
     * Assign a player to use with QuickReply
     *
     * @param CommandSender $messaged, The player that got the message
     * @param CommandSender $messenger, The player that sent the message
     */
    public function setQuickReply(CommandSender $messaged, CommandSender $messenger){
        if($messaged instanceof Player){
            $this->getSession($messaged)->setQuickReply($messenger);
        }else{
            $this->quickReply[strtolower($messaged->getName())] = $messenger->getName();
        }
    }

    /**
     * Removes QuickReply
     *
     * @param CommandSender $sender
     */
    public function removeQuickReply(CommandSender $sender){
        if($sender instanceof Player){
            $this->getSession($sender)->removeQuickReply();
        }else{
            $this->quickReply[strtolower($sender)] = false;
        }
    }

    /**  __  __       _
     *  |  \/  |     | |
     *  | \  / |_   _| |_ ___
     *  | |\/| | | | | __/ _ \
     *  | |  | | |_| | ||  __/
     *  |_|  |_|\__,_|\__\___|
     */

    /**
     * Tell if the is Muted or not
     *
     * @param Player $player
     * @return bool
     */
    public function isMuted(Player $player){
        return $this->getSession($player)->isMuted();
    }

    /**
     * Tell the time until a player will be muted
     * false = If player is not muted
     * \DateTime = DateTime Object with corresponding time
     * null = Will keep muted forever
     *
     * @param Player $player
     * @return bool|\DateTime|null
     */
    public function getMutedUntil(Player $player){
        if(!$this->isMuted($player)){
            return false;
        }
        return $this->getSession($player)->getMutedUntil();
    }

    /**
     * Set the Mute mode on or off
     *
     * @param Player $player
     * @param bool $state
     * @param \DateTime|null $expires
     * @param bool $notify
     * @return bool
     */
    public function setMute(Player $player, $state, \DateTime $expires = null, $notify = true){
        if(!is_bool($state)){
            return false;
        }
        $this->getServer()->getPluginManager()->callEvent($ev = new PlayerMuteEvent($this, $player, $state, $expires));
        if($ev->isCancelled()){
            return false;
        }
        $this->getSession($player)->setMuted($ev->willMute(), $ev->getMutedUntil());
        if($notify && $player->hasPermission("essentials.mute.notify")){
            $player->sendMessage(TextFormat::YELLOW . "You where " . ($this->isMuted($player) ? "muted " . ($ev->getMutedUntil() !== null ? "until: " . TextFormat::AQUA . $ev->getMutedUntil()->format("l, F j, Y") . TextFormat::RED . " at " . TextFormat::AQUA . $ev->getMutedUntil()->format("h:ia") : TextFormat::AQUA . "Forever" . TextFormat::YELLOW . "!") : "unmuted!"));
        }
        return true;
    }

    /**
     * Switch the Mute mode on/off automatically
     *
     * @param Player $player
     * @param \DateTime|null $expires
     * @param bool $notify
     */
    public function switchMute(Player $player, \DateTime $expires = null, $notify = true){
        $this->setMute($player, !$this->isMuted($player), $expires, $notify);
    }

    /**  _   _ _      _
     *  | \ | (_)    | |
     *  |  \| |_  ___| | _____
     *  | . ` | |/ __| |/ / __|
     *  | |\  | | (__|   <\__ \
     *  |_| \_|_|\___|_|\_|___/
     */

    /**
     * Get players' saved Nicks
     *
     * @param Player $player
     * @return null|string
     */
    public function getNick(Player $player){
        return $this->getSession($player)->getNick();
    }

    /**
     * Change the player name for chat and even on his NameTag (aka Nick)
     *
     * @param Player $player
     * @param string $nick
     * @return bool
     */
    public function setNick(Player $player, $nick){
        if(!$this->validateName($nick, true) || !$this->colorMessage($nick, $player)){
            return false;
        }
        $this->getServer()->getPluginManager()->callEvent($ev = new PlayerNickChangeEvent($this, $player, $this->colorMessage($nick)));
        if($ev->isCancelled()){
            return false;
        }
        if(strtolower($ev->getNewNick()) === strtolower($player->getName()) || $ev->getNewNick() === "off" || trim($ev->getNewNick()) === "" || $ev->getNewNick() === null){
            $ev->setNick(null);
        }
        $this->getSession($player)->setNick($ev->getNewNick());
        return true;
    }

    /**
     * Restore the original player name for chat and on his NameTag
     *
     * @param Player $player
     * @return bool
     */
    public function removeNick(Player $player){
        $this->getServer()->getPluginManager()->callEvent($event = new PlayerNickChangeEvent($this, $player, $player->getName()));
        if($event->isCancelled()){
            return false;
        }
        $this->getSession($player)->setNick(null);
        return true;
    }

    /**  _____                    _______          _
     *  |  __ \                  |__   __|        | |
     *  | |__) _____      _____ _ __| | ___   ___ | |
     *  |  ___/ _ \ \ /\ / / _ | '__| |/ _ \ / _ \| |
     *  | |  | (_) \ V  V |  __| |  | | (_) | (_) | |
     *  |_|   \___/ \_/\_/ \___|_|  |_|\___/ \___/|_|
     */

    /**
     * Tell is PowerTool is enabled for a player, doesn't matter on what item
     *
     * @param Player $player
     * @return bool
     */
    public function isPowerToolEnabled(Player $player){
        return $this->getSession($player)->isPowerToolEnabled();
    }

    /**
     * Run all the commands and send all the chat messages assigned to an item
     *
     * @param Player $player
     * @param Item $item
     * @return bool
     */
    public function executePowerTool(Player $player, Item $item){
        $command = false;
        if($this->getPowerToolItemCommand($player, $item) !== false){
            $command = $this->getPowerToolItemCommand($player, $item);
        }elseif($this->getPowerToolItemCommands($player, $item) !== false){
            $command = $this->getPowerToolItemCommands($player, $item);
        }
        if($command !== false){
            if(!is_array($command)){
                $this->getServer()->dispatchCommand($player, $command);
            }else{
                foreach($command as $c){
                    $this->getServer()->dispatchCommand($player, $c);
                }
            }
        }
        if($chat = $this->getPowerToolItemChatMacro($player, $item) !== false){
            $this->getServer()->broadcast("<" . $player->getDisplayName() . "> " . TextFormat::RESET . $this->getPowerToolItemChatMacro($player, $item), Server::BROADCAST_CHANNEL_USERS);
        }
        if($command === false && $chat === false){
            return false;
        }
        return true;
    }

    /**
     * Sets a command for the item you have in hand
     * NOTE: If the hand is empty, it will be cancelled
     *
     * @param Player $player
     * @param Item $item
     * @param string $command
     * @return bool
     */
    public function setPowerToolItemCommand(Player $player, Item $item, $command){
        return $this->getSession($player)->setPowerToolItemCommand($item->getId(), $command);
    }

    /**
     * Return the command attached to the specified item if it's available
     * NOTE: Only return the command if there're no more commands, for that use "getPowerToolItemCommands" (note the "s" at the final :P)
     *
     * @param Player $player
     * @param Item $item
     * @return bool|string
     */
    public function getPowerToolItemCommand(Player $player, Item $item){
        return $this->getSession($player)->getPowerToolItemCommand($item->getId());
    }

    /**
     * Let you assign multiple commands to an item
     *
     * @param Player $player
     * @param Item $item
     * @param array $commands
     * @return bool
     */
    public function setPowerToolItemCommands(Player $player, Item $item, array $commands){
        return $this->getSession($player)->setPowerToolItemCommands($item->getId(), $commands);
    }

    /**
     * Return a the list of commands assigned to an item
     * (if they're more than 1)
     *
     * @param Player $player
     * @param Item $item
     * @return bool|array
     */
    public function getPowerToolItemCommands(Player $player, Item $item){
        return $this->getSession($player)->getPowerToolItemCommands($item->getId());
    }

    /**
     * Let you remove 1 command of the item command list
     * [ONLY if there're more than 1)
     *
     * @param Player $player
     * @param Item $item
     * @param string $command
     */
    public function removePowerToolItemCommand(Player $player, Item $item, $command){
        $this->getSession($player)->removePowerToolItemCommand($item->getId(), $command);
    }

    /**
     * Set a chat message to broadcast has the player
     *
     * @param Player $player
     * @param Item $item
     * @param string $chat_message
     * @return bool
     */
    public function setPowerToolItemChatMacro(Player $player, Item $item, $chat_message){
        return $this->getSession($player)->setPowerToolItemChatMacro($item->getId(), $chat_message);
    }

    /**
     * Get the message to broadcast has the player
     *
     * @param Player $player
     * @param Item $item
     * @return bool|string
     */
    public function getPowerToolItemChatMacro(Player $player, Item $item){
        return $this->getSession($player)->getPowerToolItemChatMacro($item->getId());
    }

    /**
     * Remove the command only for the item in hand
     *
     * @param Player $player
     * @param Item $item
     */
    public function disablePowerToolItem(Player $player, Item $item){
        $this->getSession($player)->disablePowerToolItem($item->getId());
    }

    /**
     * Remove the commands for all the items of a player
     *
     * @param Player $player
     */
    public function disablePowerTool(Player $player){
        $this->getSession($player)->disablePowerTool();
    }

    /**  _____        _____
     *  |  __ \      |  __ \
     *  | |__) __   _| |__) |
     *  |  ___/\ \ / |  ___/
     *  | |     \ V /| |
     *  |_|      \_/ |_|
     */

    /**
     * Tell if the PvP mode is enabled for the specified player, or not
     *
     * @param Player $player
     * @return bool
     */
    public function isPvPEnabled(Player $player){
        return $this->getSession($player)->isPVPEnabled();
    }

    /**
     * Set the PvP mode on or off
     *
     * @param Player $player
     * @param bool $state
     * @return bool
     */
    public function setPvP(Player $player, $state){
        if(!is_bool($state)){
            return false;
        }
        $this->getServer()->getPluginManager()->callEvent($ev = new PlayerPvPModeChangeEvent($this, $player, $state));
        if($ev->isCancelled()){
            return false;
        }
        $this->getSession($player)->setPvP($ev->getPvPMode());
        return true;
    }

    /**
     * Switch the PvP mode on/off automatically
     *
     * @param Player $player
     */
    public function switchPvP(Player $player){
        $this->setPvP($player, !$this->isPvPEnabled($player));
    }

    /**  _______ _____  _____                           _
     *  |__   __|  __ \|  __ \                         | |
     *     | |  | |__) | |__) |___  __ _ _   _  ___ ___| |_ ___
     *     | |  |  ___/|  _  // _ \/ _` | | | |/ _ / __| __/ __|
     *     | |  | |    | | \ |  __| (_| | |_| |  __\__ | |_\__ \
     *     |_|  |_|    |_|  \_\___|\__, |\__,_|\___|___/\__|___/
     *                                | |
     *                                |_|
     */

    /**
     * Tell if a player has a pending request
     * Return false if not
     * Return array with all the names of the requesters and the actions to perform of each:
     *      "tpto" means that the requester wants to tp to the target position
     *      "tphere" means that the requester wants to tp the target to its position
     *
     * @param Player $player
     * @return bool|array
     */
    public function hasARequest(Player $player){
        return $this->getSession($player)->hasARequest();
    }

    /**
     * Tell if a player ($target) as a request from a specific player ($requester)
     * Return false if not
     * Return the type of request made:
     *      "tpto" means that the requester wants to tp to the target position
     *      "tphere" means that the requester wants to tp the target to its position
     *
     * @param Player $target
     * @param Player $requester
     * @return bool|string
     */
    public function hasARequestFrom(Player $target, Player $requester){
        return $this->getSession($target)->hasARequestFrom($requester->getName());
    }

    /**
     * Return the name of the latest teleport requester for a specific player
     *
     * @param Player $player
     * @return bool|string
     */
    public function getLatestRequest(Player $player){
        return $this->getSession($player)->getLatestRequestFrom();
    }

    /**
     * Tell if a player made a request to another player
     * Return false if not
     * Return array with the name of the target and the action to perform:
     *      "tpto" means that the requester wants to tp to the target position
     *      "tphere" means that the requester wants to tp the target to its position
     *
     * @param Player $player
     * @return array|bool
     */
    public function madeARequest(Player $player){
        return $this->getSession($player)->madeARequest();
    }

    /**
     * Schedule a Request to move $requester to $target's position
     *
     * @param Player $requester
     * @param Player $target
     */
    public function requestTPTo(Player $requester, Player $target){
        $this->getSession($requester)->requestTP($target->getName(), "tpto");

        $this->getSession($target)->receiveRequest($requester->getName(), "tpto");

        $this->scheduleTPRequestTask($requester);
    }

    /**
     * Schedule a Request to mode $target to $requester's position
     *
     * @param Player $requester
     * @param Player $target
     */
    public function requestTPHere(Player $requester, Player $target){
        $this->getSession($requester)->requestTP($target->getName(), "tphere");

        $this->getSession($target)->receiveRequest($requester->getName(), "tphere");

        $this->scheduleTPRequestTask($requester);
    }

    /**
     * Cancel the Request made by a player
     *
     * @param Player $requester
     * @param Player $target
     * @return bool
     */
    public function removeTPRequest(Player $requester, Player $target = null){
        if(!$this->getSession($requester)->madeARequest() && $target === null){
            return false;
        }

        if($target !== null && $this->getSession($requester)->madeARequestTo($target->getName())){
            $this->getSession($requester)->cancelTPRequest();
            $this->getSession($target)->removeRequestFrom($requester->getName());
        }elseif($target === null){
            $target = $this->getPlayer($this->getSession($requester)->madeARequest()[0]);
            $this->getSession($requester)->cancelTPRequest();
            if($target !== false){
                $this->getSession($target)->removeRequestFrom($requester->getName());
            }
        }

        $this->cancelTPRequestTask($requester);
        return true;
    }

    /**
     * Schedule the Request auto-remover task (Internal use ONLY!)
     *
     * @param Player $player
     */
    private function scheduleTPRequestTask(Player $player){
        $task = $this->getServer()->getScheduler()->scheduleDelayedTask(new TPRequestTask($this, $player), 20 * 60 * 5);
        $this->getSession($player)->setRequestToTaskID($task->getTaskId());
    }

    /**
     * Cancel the Task (Internal use ONLY!)
     *
     * @param Player $player
     */
    private function cancelTPRequestTask(Player $player){
        $this->getServer()->getScheduler()->cancelTask($this->getSession($player)->getRequestToTaskID());
        $this->getSession($player)->removeRequestToTaskID();
    }


    /**  _    _       _ _           _ _           _   _____ _
     *  | |  | |     | (_)         (_| |         | | |_   _| |
     *  | |  | |_ __ | |_ _ __ ___  _| |_ ___  __| |   | | | |_ ___ _ __ ___  ___
     *  | |  | | '_ \| | | '_ ` _ \| | __/ _ \/ _` |   | | | __/ _ | '_ ` _ \/ __|
     *  | |__| | | | | | | | | | | | | ||  __| (_| |  _| |_| ||  __| | | | | \__ \
     *   \____/|_| |_|_|_|_| |_| |_|_|\__\___|\__,_| |_____|\__\___|_| |_| |_|___/
     */

    /**
     * Tells if the unlimited mode is enabled
     *
     * @param Player $player
     * @return bool
     */
    public function isUnlimitedEnabled(Player $player){
        return $this->getSession($player)->isUnlimitedEnabled();
    }

    /**
     * Set the unlimited place of items on/off to a player
     *
     * @param Player $player
     * @param bool $mode
     * @return bool
     */
    public function setUnlimited(Player $player, $mode){
        if(!is_bool($mode)){
            return false;
        }
        $this->getServer()->getPluginManager()->callEvent($ev = new PlayerUnlimitedModeChangeEvent($this, $player, $mode));
        if($ev->isCancelled()){
            return false;
        }
        $this->getSession($player)->setUnlimited($ev->getUnlimitedMode());
        return true;
    }

    /**
     * Automatically switch the state of the Unlimited mode
     *
     * @param Player $player
     */
    public function switchUnlimited(Player $player){
        $this->setUnlimited($player, !$this->isUnlimitedEnabled($player));
    }

    /**  _    _           _       _
     *  | |  | |         | |     | |
     *  | |  | |_ __   __| | __ _| |_ ___ _ __
     *  | |  | | '_ \ / _` |/ _` | __/ _ | '__|
     *  | |__| | |_) | (_| | (_| | ||  __| |
     *   \____/| .__/ \__,_|\__,_|\__\___|_|
     *         | |
     *         |_|
     */

    /** @var UpdateFetchTask */
    private $updaterTask = null;

    /** @var UpdateInstallTask */
    public $updaterDownloadTask = null; // Used to prevent Async Task conflicts with Server's limit :P

    /**
     * Tell if the updater is enabled or not
     *
     * @return bool
     */
    public function isUpdaterEnabled(){
        return $this->getConfig()->getNested("updater.enabled");
    }

    /**
     * Tell the build of the updater for EssentialsPE
     *
     * @return string
     */
    public function getUpdateBuild(){
        return ($this->getConfig()->getNested("updater.stable") ? "stable" : "beta");
    }

    /**
     * Get the interval for the updater to get in action
     *
     * @return int
     */
    public function getUpdaterInterval(){
        return $this->getConfig()->getNested("updater.time-interval");
    }

    /**
     * Get the latest version, and install it if you want
     *
     * @param bool $install
     * @return bool
     */
    public function fetchEssentialsPEUpdate($install = false){
        if(($this->updaterTask !== null && $this->updaterTask->isRunning()) && ($this->updaterDownloadTask !== null && $this->updaterDownloadTask->isRunning())){
            return false;
        }
        $this->getServer()->getLogger()->debug(TextFormat::YELLOW . "Running EssentialsPE's UpdateFetchTask");
        $this->getServer()->getScheduler()->scheduleAsyncTask($task = new UpdateFetchTask($this->getUpdateBuild(), $install));
        $this->updaterTask = $task;
        return true;
    }

    /**
     * Schedules the updater task :3
     */
    public function scheduleUpdaterTask(){
        if($this->isUpdaterEnabled()){
            $this->getServer()->getScheduler()->scheduleDelayedTask(new AutoFetchCallerTask($this), $this->getUpdaterInterval() * 20);
        }
    }

    /**
     * Warn about a new update of EssentialsPE
     *
     * @param string $message
     */
    public function broadcastUpdateAvailability($message){
        if($this->getConfig()->getNested("updater.warn-console")){
            $this->getServer()->getLogger()->info($message);
        }
        if($this->getConfig()->getNested("updater.warn-players")){
            foreach($this->getServer()->getOnlinePlayers() as $p){
                if($p->hasPermission("essentials.update.notify")){
                    $p->sendMessage($message);
                }
            }
        }
    }

    /** __      __         _     _
     *  \ \    / /        (_)   | |
     *   \ \  / __ _ _ __  _ ___| |__
     *    \ \/ / _` | '_ \| / __| '_ \
     *     \  | (_| | | | | \__ | | | |
     *      \/ \__,_|_| |_|_|___|_| |_|
     */

    /** @var null|Effect */
    private $invisibilityEffect = null;

    /**
     * Tell if a player is Vanished, or not
     *
     * @param Player $player
     * @return bool
     */
    public function isVanished(Player $player){
        return $this->getSession($player)->isVanished();
    }

    /**
     * Tells if the specified player has "noPacket" enabled for vanish
     *
     * @param Player $player
     * @return bool
     */
    public function hasNoPacket(Player $player){
        return $this->getSession($player)->noPacket();
    }

    /**
     * Set the Vanish mode on or off
     *
     * @param Player $player
     * @param bool $state
     * @param bool $noPacket
     * @return bool
     */
    public function setVanish(Player $player, $state, $noPacket = false){
        if(!is_bool($state)){
            return false;
        }
        if($this->invisibilityEffect === null){
            $effect = new Effect(Effect::INVISIBILITY, "Vanish", 127, 131, 146);
            $effect->setDuration(1728000); // 24 hours... Well... No one will play more than this, so I think its OK xD
            $this->invisibilityEffect = $effect;
        }
        $this->getServer()->getPluginManager()->callEvent($ev = new PlayerVanishEvent($this, $player, $state, $noPacket));
        if($ev->isCancelled()){
            return false;
        }
        $state = $ev->willVanish();
        $player->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, $state);
        $player->setDataProperty(Entity::DATA_SHOW_NAMETAG, Entity::DATA_TYPE_BYTE, ($state ? 0 : 1));
        /** @var Player[] $pl */
        $pl = [];
        foreach($player->getLevel()->getPlayers() as $p){
            if($state || (!$state && !in_array($p->getName(), $ev->getHiddenFor()))){
                $pl[] = $p;
            }
        }
        $noPacket = $ev->noPacket();
        if($this->isVanished($player) && $ev->noPacket() !== ($priority = $this->hasNoPacket($player))){
            $noPacket = $priority;
        }
        if(!$noPacket){
            if(!$state){
                $pk = new MobEffectPacket();
                $pk->eid = $player->getId();
                $pk->eventId = MobEffectPacket::EVENT_REMOVE;
                $pk->effectId = $this->invisibilityEffect->getId();
            }else{
                $pk = new MobEffectPacket();
                $pk->eid = $player->getId();
                $pk->effectId = $this->invisibilityEffect->getId();
                $pk->amplifier = $this->invisibilityEffect->getAmplifier();
                $pk->particles = $this->invisibilityEffect->isVisible();
                $pk->duration = $this->invisibilityEffect->getDuration();
                $pk->eventId = MobEffectPacket::EVENT_ADD;
            }
            $this->getServer()->broadcastPacket($pl, $pk);
        }else{
            if(!$state){
                foreach($pl as $p){
                    $p->showPlayer($player);
                }
            }else{
                foreach($pl as $p){
                    $p->hidePlayer($player);
                }
            }
        }
        $this->getSession($player)->setVanish($state, !$state ? $ev->noPacket() : $noPacket);
        return true;
    }

    /**
     * Switch the Vanish mode on/off automatically
     *
     * @param Player $player
     * @return bool
     */
    public function switchVanish(Player $player){
        $this->setVanish($player, !$this->isVanished($player));
    }

    /**
     * Allow to switch between levels Vanished!
     * You need to teleport the player to a different level in order to call this event
     *
     * @param Player $player
     * @param Level $origin
     * @param Level $target
     */
    public function switchLevelVanish(Player $player, Level $origin, Level $target){
        if($origin->getName() !== $target->getName() && $this->isVanished($player)){

            // This will be used if the specified player has "noPacket" enabled.
            // A temporal check will be used for "the other players".
            $noPacket = $this->hasNoPacket($player);

            // Just as prevention if any player has "noPacket" disabled...
            $pk = new MobEffectPacket();
            $pk->effectId = $this->invisibilityEffect->getId();
            $pk->amplifier = $this->invisibilityEffect->getAmplifier();
            $pk->particles = $this->invisibilityEffect->isVisible();
            $pk->duration = $this->invisibilityEffect->getDuration();

            // Show to origin's players
            $pk->eventId = MobEffectPacket::EVENT_REMOVE;
            foreach($origin->getPlayers() as $p){
                if($p !== $player){
                    if($this->isVanished($player)){
                        if(!$noPacket){
                            $pk->eid = $player->getId();
                            $p->dataPacket($pk);
                        }else{
                            $p->showPlayer($player);
                        }
                    }
                    if($this->isVanished($p)){
                        if(!$this->hasNoPacket($p)){
                            $pk->eid = $p->getId();
                            $player->dataPacket($pk);
                        }else{
                            $player->showPlayer($p);
                        }
                    }
                }
            }
            // Hide to target's players
            $pk->eventId = MobEffectPacket::EVENT_ADD;
            foreach($target->getPlayers() as $p){
                if($p !== $player){
                    if($this->isVanished($player)){
                        if(!$noPacket){
                            $pk->eid = $player->getId();
                            $p->dataPacket($pk);
                        }else{
                            $p->hidePlayer($player);
                        }
                    }
                    if($this->isVanished($p)){
                        if(!$this->hasNoPacket($p)){
                            $pk->eid = $p->getId();
                            $player->dataPacket($pk);
                        }else{
                            $player->hidePlayer($p);
                        }
                    }
                }
            }
        }
    }

    /** __          __
     *  \ \        / /
     *   \ \  /\  / __ _ _ __ _ __
     *    \ \/  \/ / _` | '__| '_ \
     *     \  /\  | (_| | |  | |_) |
     *      \/  \/ \__,_|_|  | .__/
     *                       | |
     *                       |_|
     */

    /**
     * Tell if a warp exists
     *
     * @param string $warp
     * @return bool
     */
    public function warpExists($warp){
        return $this->validateName($warp, false) && isset($this->warps[$warp]);
    }

    /**
     * Get a Location object of the warp
     * If the function returns "false", it means that the warp doesn't exists
     *
     * @param string $warp
     * @return bool|BaseLocation
     */
    public function getWarp($warp){
        if(!$this->warpExists($warp)){
            return false;
        }
        return $this->warps[$warp];
    }

    /**
     * Create a warp or override its position
     *
     * @param string $warp
     * @param Position $pos
     * @param int $yaw
     * @param int $pitch
     * @return bool
     */
    public function setWarp($warp, Position $pos, $yaw = 0, $pitch = 0){
        if(!$this->validateName($warp, false)){
            return false;
        }
        $this->warps[$warp] = $pos instanceof BaseLocation ? $pos : BaseLocation::fromPosition($warp, ($pos instanceof Location ? $pos : Location::fromObject($pos, $pos->getLevel(), $yaw, $pitch)));
        return true;
    }

    /**
     * Removes a warp!
     * If the function return "false", it means that the warp doesn't exists
     *
     * @param string $warp
     * @return bool
     */
    public function removeWarp($warp){
        if(!$this->warpExists($warp)){
            return false;
        }
        unset($this->warps[$warp]);
        return true;
    }

    /**
     * Return a list of all the available warps
     *
     * @param bool $inArray
     * @return array|bool|string
     */
    public function warpList($inArray = false){
        $list = array_keys($this->warps);
        if(count($list) < 1){
            return false;
        }
        if(!$inArray){
            return implode(", ", $list);
        }
        return $list;
    }
}
