<?php
namespace EssentialsPE\BaseFiles;

use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\level\Location;
use pocketmine\Player;
use pocketmine\utils\Config;

class BaseSession {
    /** @var Loader */
    private $plugin;
    /** @var Player */
    private $player;
    /** @var Config */
    private $config;
    /** @var array */
    public static $defaults = [
        "isAFK" => false,
        "kickAFK" => null,
        "lastMovement" => null,
        "lastPosition" => null,
        "isGod" => false,
        "homes" => [],
        "quickReply" => false,
        "isMuted" => false,
        "mutedUntil" => null,
        "nick" => null,
        "ptCommands" => false,
        "ptChatMacros" => false,
        "isPvPEnabled" => true,
        "requestTo" => false,
        "requestToAction" => false,
        "requestToTask" => null,
        "latestRequestFrom" => null,
        "requestsFrom" => [],
        "isUnlimitedEnabled" => false,
        "isVanished" => false,
        "noPacket" => false
    ];
    /** @var array */
    public static $configDefaults = [
        "isAFK" => false,
        "isGod" => false,
        "homes" => [],
        "isMuted" => false,
        "mutedUntil" => null,
        "nick" => null,
        "ptCommands" => false,
        "ptChatMacros" => false,
        "isPvPEnabled" => true,
        "isUnlimitedEnabled" => false,
        "isVanished" => false
    ];

    /**
     * @param Loader $plugin
     * @param Player $player
     * @param Config $config
     * @param array $values
     */
    public function __construct(Loader $plugin, Player $player, Config $config, array $values){
        $this->plugin = $plugin;
        $this->player = $player;
        $this->config = $config;
        self::$defaults["lastMovement"] = !$player->hasPermission("essentals.afk.preventauto") ? time() : null;
        foreach($values as $k => $v){
            $this->{$k} = $v;
        }
        $this->loadHomes();
    }

    private function saveSession(){
        $values = [];
        foreach(self::$configDefaults as $k => $v){
            switch($k){
                case "mutedUntil":
                    $v = $this->{$k} instanceof \DateTime ? $this->{$k}->getTimestamp() : null;
                    break;
                case "homes":
                    $v = $this->encodeHomes();
                    break;
                default:
                    $v = $this->{$k};
                    break;
            }
            $values[$k] = $v;
        }
        $this->config->setAll($values);
        $this->config->save();
    }

    public function onClose(){
        $this->saveSession();

        // Let's revert some things to their original state...
        $this->setNick(null);
        $this->getPlugin()->removeTPRequest($this->getPlayer());
        if($this->isVanished()){
            $this->getPlugin()->setVanish($this->getPlayer(), false, $this->noPacket());
        }
    }

    /**
     * @return Loader
     */
    public final function getPlugin(){
        return $this->plugin;
    }

    /**
     * @return Player
     */
    public final function getPlayer(){
        return $this->player->getPlayer();
    }

    /**
     *            ______ _  __
     *      /\   |  ____| |/ /
     *     /  \  | |__  | ' /
     *    / /\ \ |  __| |  <
     *   / ____ \| |    | . \
     *  /_/    \_|_|    |_|\_\
     */

    /** @var bool */
    private $isAFK = false;
    /** @var int|null */
    private $kickAFK = null;
    /** @var int|null */
    private $lastMovement = null;

    /**
     * @return bool
     */
    public function isAFK(){
        return $this->isAFK;
    }

    /**
     * @param bool $mode
     * @return bool
     */
    public function setAFK($mode){
        if(!is_bool($mode)){
            return false;
        }
        $this->isAFK = $mode;
        return true;
    }

    /**
     * @return bool|int
     */
    public function getAFKKickTaskID(){
        if(!$this->isAFK()){
            return false;
        }
        return $this->kickAFK;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function setAFKKickTaskID($id){
        if(!is_int($id)){
            return false;
        }
        $this->kickAFK = $id;
        return true;
    }

    public function removeAFKKickTaskID(){
        $this->kickAFK = null;
    }

    /**
     * @return int|null
     */
    public function getLastMovement(){
        return $this->lastMovement;
    }

    /**
     * @param int $time
     */
    public function setLastMovement($time){
        $this->lastMovement = (int) $time;
    }

    /**  ____             _
     *  |  _ \           | |
     *  | |_) | __ _  ___| | __
     *  |  _ < / _` |/ __| |/ /
     *  | |_) | (_| | (__|   <
     *  |____/ \__,_|\___|_|\_\
     */

    /** @var null */
    private $lastLocation = null;

    /**
     * @return bool|Location
     */
    public function getLastPosition(){
        if(!$this->lastLocation instanceof Location){
            return false;
        }
        return $this->lastLocation;
    }

    /**
     * @param Location $pos
     */
    public function setLastPosition(Location $pos){
        $this->lastLocation = $pos;
    }

    public function removeLastPosition(){
        $this->lastLocation = null;
    }

    /**   _____           _
     *   / ____|         | |
     *  | |  __  ___   __| |
     *  | | |_ |/ _ \ / _` |
     *  | |__| | (_) | (_| |
     *   \_____|\___/ \__,_|
     */

    /** @var bool */
    private $isGod = false;

    /**
     * @return bool
     */
    public function isGod(){
        return $this->isGod;
    }

    /**
     * @param bool $mode
     * @return bool
     */
    public function setGod($mode){
        if(!is_bool($mode)){
            return false;
        }
        $this->isGod = $mode;
        return true;
    }

    /**  _    _
     *  | |  | |
     *  | |__| | ___  _ __ ___   ___ ___
     *  |  __  |/ _ \| '_ ` _ \ / _ / __|
     *  | |  | | (_) | | | | | |  __\__ \
     *  |_|  |_|\___/|_| |_| |_|\___|___/
     */

    /** @var array */
    private $homes = [];

    private function loadHomes(){
        $homes = [];
        foreach($this->homes as $name => $values){
            if(is_array($values)){
                if($this->getPlugin()->getServer()->isLevelGenerated($values[3])){
                    if(!$this->getPlugin()->getServer()->isLevelLoaded($values[3])){
                        $this->getPlugin()->getServer()->loadLevel($values[3]);
                    }
                    $homes[$name] = new BaseLocation($name, $values[0], $values[1], $values[2], $this->getPlugin()->getServer()->getLevelByName($values[3]), $values[4], $values[5]);
                }
            }
        }
        $this->homes = $homes;
    }

    private function encodeHomes(){
        $homes = [];
        foreach($this->homes as $name => $object){
            if($object instanceof BaseLocation){
                $homes[$name] = [$object->getX(), $object->getY(), $object->getZ(), $object->getLevel()->getName(), $object->getYaw(), $object->getPitch()];
            }
        }
        return $homes;
    }

    /**
     * @param $home
     * @return bool
     */
    public function homeExists($home){
        return $this->getPlugin()->validateName($home) && isset($this->homes[$home]) && $this->homes[$home] instanceof BaseLocation;
    }

    /**
     * @param $home
     * @return bool|BaseLocation
     */
    public function getHome($home){
        if(!$this->homeExists($home)){
            return false;
        }
        return $this->homes[$home];
    }

    /**
     * @param $home
     * @param Location $pos
     * @return bool
     */
    public function setHome($home, Location $pos){
        if(!$this->getPlugin()->validateName($home, false)){
            return false;
        }
        $this->homes[$home] = $pos instanceof BaseLocation ? $pos : BaseLocation::fromPosition($home, $pos);
        return true;
    }

    /**
     * @param $home
     * @return bool
     */
    public function removeHome($home){
        if(!$this->homeExists($home)){
            return false;
        }
        unset($this->homes[$home]);
        return true;
    }

    /**
     * @param bool $inArray
     * @return array|bool|string
     */
    public function homesList($inArray = false){
        $list = array_keys($this->homes);
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
     *  | \  / |___  __ _
     *  | |\/| / __|/ _` |
     *  | |  | \__ | (_| |
     *  |_|  |_|___/\__, |
     *               __/ |
     *              |___/
     */

    /** @var bool|string */
    private $quickReply = false;

    /**
     * @return bool|string
     */
    public function getQuickReply(){
        return $this->quickReply;
    }

    /**
     * @param CommandSender $sender
     */
    public function setQuickReply(CommandSender $sender){
        $this->quickReply = $sender->getName();
    }

    public function removeQuickReply(){
        $this->quickReply = false;
    }

    /**  __  __       _
     *  |  \/  |     | |
     *  | \  / |_   _| |_ ___
     *  | |\/| | | | | __/ _ \
     *  | |  | | |_| | ||  __/
     *  |_|  |_|\__,_|\__\___|
     */

    /** @var bool */
    private $isMuted = false;
    /** @var \DateTime|null */
    private $mutedUntil = null;

    /**
     * @return bool
     */
    public function isMuted(){
        return $this->isMuted;
    }

    /**
     * @return \DateTime|null
     */
    public function getMutedUntil(){
        return $this->mutedUntil;
    }

    /**
     * @param bool $state
     * @param \DateTime|null $expires
     */
    public function setMuted($state, \DateTime $expires = null){
        if(is_bool($state)){
            $this->isMuted = $state;
            $this->mutedUntil = $expires;
        }
    }

    /**  _   _ _      _
     *  | \ | (_)    | |
     *  |  \| |_  ___| | _____
     *  | . ` | |/ __| |/ / __|
     *  | |\  | | (__|   <\__ \
     *  |_| \_|_|\___|_|\_|___/
     */

    /** @var null|string */
    private $nick = null;

    /**
     * @return null|string
     */
    public function getNick(){
        return $this->nick;
    }

    /**
     * @param null|string $nick
     */
    public function setNick($nick){
        $this->nick = $nick;
        $this->getPlayer()->setDisplayName($nick === null ? $this->getPlayer()->getName() : $nick);
        $this->getPlayer()->setNameTag($nick === null ? $this->getPlayer()->getName() : $nick);
    }

    /**  _____                    _______          _
     *  |  __ \                  |__   __|        | |
     *  | |__) _____      _____ _ __| | ___   ___ | |
     *  |  ___/ _ \ \ /\ / / _ | '__| |/ _ \ / _ \| |
     *  | |  | (_) \ V  V |  __| |  | | (_) | (_) | |
     *  |_|   \___/ \_/\_/ \___|_|  |_|\___/ \___/|_|
     */

    /** @var bool|array */
    private $ptCommands = false;
    /** @var bool|array */
    private $ptChatMacro = false;

    /**
     * @return bool
     */
    public function isPowerToolEnabled(){
        if(!$this->ptCommands && !$this->ptChatMacro){
            return false;
        }
        return true;
    }

    /**
     * @param int $itemId
     * @param string $command
     * @return bool
     */
    public function setPowerToolItemCommand($itemId, $command){
        if(!is_int((int) $itemId) || (int) $itemId === 0){
            return false;
        }
        if(!is_array($this->ptCommands) || !isset($this->ptCommands[$itemId]) || !is_array($this->ptCommands[$itemId])){
            $this->ptCommands[$itemId] = $command;
        }else{
            $this->ptCommands[$itemId][] = $command;
        }
        return true;
    }

    /**
     * @param int $itemId
     * @return bool
     */
    public function getPowerToolItemCommand($itemId){
        if(!isset($this->ptCommands[$itemId]) || is_array($this->ptCommands[$itemId])){
            return false;
        }
        return $this->ptCommands[$itemId];
    }

    /**
     * @param int $itemId
     * @param array $commands
     * @return bool
     */
    public function setPowerToolItemCommands($itemId, array $commands){
        if(!is_int((int) $itemId) || (int) $itemId === 0 || count($commands) < 1){
            return false;
        }
        $this->ptCommands[$itemId] = $commands;
        return true;
    }

    /**
     * @param int $itemId
     * @return bool
     */
    public function getPowerToolItemCommands($itemId){
        if(!is_array($this->ptCommands) || !in_array($itemId, $this->ptCommands) || !is_array($this->ptCommands[$itemId])){
            return false;
        }
        return $this->ptCommands[$itemId];
    }

    /**
     * @param int $itemId
     * @param string $command
     */
    public function removePowerToolItemCommand($itemId, $command){
        $commands = $this->getPowerToolItemCommands($itemId);
        if(is_array($commands)){
            foreach($commands as $c){
                if(stripos(strtolower($c), strtolower($command)) !== false){
                    unset($c);
                }
            }
        }
    }

    /**
     * @param int $itemId
     * @param string $chat_message
     * @return bool
     */
    public function setPowerToolItemChatMacro($itemId, $chat_message){
        if(!is_int($itemId) || $itemId === 0){
            return false;
        }
        $chat_message = str_replace("\\n", "\n", $chat_message);
        $this->ptChatMacro[$itemId] = $chat_message;
        return true;
    }

    /**
     * @param int $itemId
     * @return bool
     */
    public function getPowerToolItemChatMacro($itemId){
        if(!is_int($itemId) || $itemId === 0 || !isset($this->ptChatMacro[$itemId])){
            return false;
        }
        return $this->ptChatMacro[$itemId];
    }

    /**
     * @param int $itemId
     */
    public function disablePowerToolItem($itemId){
        unset($this->ptCommands[$itemId]);
        unset($this->ptChatMacro[$itemId]);
    }

    public function disablePowerTool(){
        $this->ptCommands = false;
        $this->ptChatMacro = false;
    }

    /**  _____        _____
     *  |  __ \      |  __ \
     *  | |__) __   _| |__) |
     *  |  ___/\ \ / |  ___/
     *  | |     \ V /| |
     *  |_|      \_/ |_|
     */

    /** @var bool */
    private $isPvPEnabled = true;

    /**
     * @return bool
     */
    public function isPVPEnabled(){
        return $this->isPvPEnabled;
    }

    /**
     * @param bool $mode
     * @return bool
     */
    public function setPvP($mode){
        if(!is_bool($mode)){
            return false;
        }
        $this->isPvPEnabled = $mode;
        return true;
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

    //Request to:
    /** @var bool|string */
    private $requestTo = false;
    /** @var bool|string */
    private $requestToAction = false;
    /** @var null|int */
    private $requestToTask = null;

    /**
     * @return array|bool
     */
    public function madeARequest(){
        return ($this->requestTo !== false ? [$this->requestTo, $this->requestToAction] : false);
    }

    /**
     * @param string $target
     * @return bool
     */
    public function madeARequestTo($target){
        return $this->requestTo === $target;
    }

    /**
     * @param string $target
     * @param string $action
     */
    public function requestTP($target, $action){
        $this->requestTo = $target;
        $this->requestToAction = $action;
    }

    public function cancelTPRequest(){
        $this->requestTo = false;
        $this->requestToAction = false;
    }

    /**
     * @return bool|int
     */
    public function getRequestToTaskID(){
        return ($this->requestToTask !== null ? $this->requestToTask : false);
    }

    /**
     * @param int $taskId
     * @return bool
     */
    public function setRequestToTaskID($taskId){
        if(!is_int($taskId)){
            return false;
        }
        $this->requestToTask = $taskId;
        return true;
    }

    public function removeRequestToTaskID(){
        $this->requestToTask = null;
    }

    //Requests from:
    /** @var null|string */
    private $latestRequestFrom = null;
    /** @var array */
    private $requestsFrom = [];
    /** This is how it works per player:
    *
    * "iksaku" => "tpto"  <--- Type of request
    *    ^^^
    * Requester Name
    */

    /**
     * @return array|bool
     */
    public function hasARequest(){
        return (count($this->requestsFrom) > 0 ? $this->requestsFrom : false);
    }

    /**
     * @param string $requester
     * @return bool|string
     */
    public function hasARequestFrom($requester){
        return (isset($this->requestsFrom[$requester]) ? $this->requestsFrom[$requester] : false);
    }

    /**
     * @return bool|string
     */
    public function getLatestRequestFrom(){
        return ($this->latestRequestFrom !== null ? $this->latestRequestFrom : false);
    }

    /**
     * @param string $requester
     * @param string $action
     */
    public function receiveRequest($requester, $action){
        $this->latestRequestFrom = $requester;
        $this->requestsFrom[$requester] = $action;
    }

    /**
     * @param string $requester
     */
    public function removeRequestFrom($requester){
        unset($this->requestsFrom[$requester]);
        if($this->getLatestRequestFrom() === $requester){
            $this->latestRequestFrom = null;
        }
    }

    /**  _    _       _ _           _ _           _   _____ _
     *  | |  | |     | (_)         (_| |         | | |_   _| |
     *  | |  | |_ __ | |_ _ __ ___  _| |_ ___  __| |   | | | |_ ___ _ __ ___  ___
     *  | |  | | '_ \| | | '_ ` _ \| | __/ _ \/ _` |   | | | __/ _ | '_ ` _ \/ __|
     *  | |__| | | | | | | | | | | | | ||  __| (_| |  _| |_| ||  __| | | | | \__ \
     *   \____/|_| |_|_|_|_| |_| |_|_|\__\___|\__,_| |_____|\__\___|_| |_| |_|___/
     */

    /** @var bool */
    private $isUnlimitedEnabled = false;

    /**
     * @return bool
     */
    public function isUnlimitedEnabled(){
        return $this->isUnlimitedEnabled;
    }

    /**
     * @param bool $mode
     * @return bool
     */
    public function setUnlimited($mode){
        if(!is_bool($mode)){
            return false;
        }
        $this->isUnlimitedEnabled = $mode;
        return true;
    }

    /** __      __         _     _
     *  \ \    / /        (_)   | |
     *   \ \  / __ _ _ __  _ ___| |__
     *    \ \/ / _` | '_ \| / __| '_ \
     *     \  | (_| | | | | \__ | | | |
     *      \/ \__,_|_| |_|_|___|_| |_|
     */

    /** @var bool */
    private $isVanished = false;

    /**
     * If set to true, we will use Player packets instead of Effect ones
     *
     * @var bool
     */
    private $noPacket = false;

    /**
     * @return bool
     */
    public function isVanished(){
        return $this->isVanished;
    }

    /**
     * @param bool $mode
     * @param bool $noPacket
     * @return bool
     */
    public function setVanish($mode, $noPacket){
        if(!is_bool($mode)){
            return false;
        }
        $this->isVanished = $mode;
        if(!is_bool($noPacket)){
            return false;
        }
        $this->noPacket = $noPacket;
        return true;
    }

    /**
     * @return bool
     */
    public function noPacket(){
        return $this->noPacket;
    }
}