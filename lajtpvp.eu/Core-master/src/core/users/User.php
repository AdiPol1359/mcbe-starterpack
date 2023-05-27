<?php

declare(strict_types=1);

namespace core\users;

use core\entities\object\EnderPearl;
use core\Main;
use core\users\data\UserBackpack;
use core\users\data\UserBank;
use core\users\data\UserDrop;
use core\users\data\UserHome;
use core\users\data\UserIgnore;
use core\users\data\UserIncognito;
use core\users\data\UserKit;
use core\users\data\UserServices;
use core\users\data\UserStat;
use core\users\data\UserTerrain;
use core\utils\RandomUtil;
use core\utils\Settings;
use pocketmine\math\Vector3;
use pocketmine\Server;

class User {

    private array $lastData = [];
    private bool $isConnected = false;

    private ?string $lastPrivateMessage = null;

    private int $safe = 0;
    private int $lastChatMessage = 0;

    private bool $antiCheatAlerts = true;
    private bool $hasGod = false;
    private bool $isVanish = false;

    private array $rtpPlayers = [];
    private array $teleportRequests = [];

    private int $antyLogoutTime = 0;
    private ?string $lastDamager = null;
    private array $assists = [];

    private array $killedPlayers = [];

    private array $lastEnderPearls = [];
    /** @var EnderPearl[] */
    private array $enderPearls = [];

    private array $guildInvites = [];

    private UserDrop $dropManager;
    private UserBackpack $backpackManager;
    private UserStat $statManager;
    private UserTerrain $terrainManager;
    private UserHome $homeManager;
    private UserIgnore $ignoreManager;
    private UserIncognito $incognitoManager;
    private UserKit $kitManager;
    private UserBank $bankManager;
    private UserServices $servicesManager;

    public function __construct(private string $name, private string $uuid, private string $deviceId) {
        $this->dropManager = new UserDrop($this);
        $this->backpackManager = new UserBackpack($this);
        $this->statManager = new UserStat($this);
        $this->terrainManager = new UserTerrain($this);
        $this->homeManager = new UserHome($this);
        $this->ignoreManager = new UserIgnore($this);
        $this->incognitoManager = new UserIncognito($this);
        $this->kitManager = new UserKit($this);
        $this->bankManager = new UserBank($this);
        $this->servicesManager = new UserServices($this);
    }

    public function getName() : string {
        return $this->name;
    }

    public function getUUID() : string {
        return $this->uuid;
    }

    public function getDeviceId() : string {
        return $this->deviceId;
    }

    public function isConnected() : bool {
        return $this->isConnected;
    }

    public function onUpdate() : void {
        foreach($this->lastData as $name => $value) {
            if($value["type"] === Settings::$TIME_TYPE) {
                if($value["value"] <= time())
                    unset($this->lastData[$name]);
            }
        }
    }

    /* ANTI CHEAT ALERTS */

    public function hasAntiCheatAlerts() : bool {
        return $this->antiCheatAlerts;
    }

    public function setAntiCheatAlerts(bool $value) : void {
        $this->antiCheatAlerts = $value;
    }

    /* GOD */

    public function hasGod() : bool {
        return $this->hasGod;
    }

    public function setGodMode(bool $value = true) : void {
        $this->hasGod = $value;
    }

    /* RTP */

    public function isInRtpData(string $nick) : bool {
        $bool = false;

        if(isset($this->rtpPlayers[$nick])) {
            if($this->rtpPlayers[$nick] > time())
                $bool = true;
            else
                unset($this->rtpPlayers[$nick]);
        }

        return $bool;
    }

    public function getRtp(string $nick) : int {
        return $this->rtpPlayers[$nick];
    }

    public function addRtp(string $nick) : void {
        $this->rtpPlayers[$nick] = time() + Settings::$RTP_TIME;
    }

    /* LAST CHAT MESSAGE */

    public function getLastChatMessage() : int {
        return $this->lastChatMessage;
    }

    public function setLastChatMessage(int $time) : void {
        $this->lastChatMessage = $time;
    }

    public function canType() : bool {
        return $this->lastChatMessage <= time();
    }

    /* VANISH */

    public function isVanished() : bool {
        return $this->isVanish;
    }

    public function setVanish(bool $value) : void {
        $this->isVanish = $value;
    }

    /* INVITES */

    public function getInvites() : array {
        return $this->guildInvites;
    }

    public function addInvite(string $tag) : void {
        $this->guildInvites[$tag] = time() + Settings::$INVITE_EXPIRE_TIME;
    }

    public function hasInvite(string $tag) : bool {
        if(!isset($this->guildInvites[$tag]))
            return false;

        if($this->guildInvites[$tag] <= time())
            unset($this->guildInvites[$tag]);

        return isset($this->guildInvites[$tag]);
    }

    public function removeInvite(string $tag) : void {
        unset($this->guildInvites[$tag]);
    }

    /* TELEPORT */

    public function getTeleportRequests() : array {
        $requests = [];

        foreach($this->teleportRequests as $requestNick => $data) {
            if($data["time"] > time())
                $requests[$requestNick] = $data;
        }

        return $requests;
    }

    public function hasTeleportRequest(string $nick) : bool {
        foreach($this->teleportRequests as $requestNick => $data) {
            if($nick === $requestNick)
                if($data["time"] > time())
                    return true;
        }

        return false;
    }

    public function setTeleportRequest(string $nick) : void {
        $this->teleportRequests[$nick] = ["time" => time() + 30];
    }

    public function removeTeleportRequest(string $nick) : void {
        unset($this->teleportRequests[$nick]);
    }

    public function clearTeleportRequests() : void {
        $this->teleportRequests = [];
    }

    /* ANTY LOGOUT */

    public function setAntyLogout(string $attacker, float $attackerDamage = 0) : void {
        $this->antyLogoutTime = (time() + Settings::$ANTYLOGOUT_TIME);
        $this->lastDamager = $attacker;

        if(isset($this->assists[$attacker]))
            $this->assists[$attacker] += $attackerDamage;
        else
            $this->assists[$attacker] = $attackerDamage;
    }

    public function hasAntyLogout() : bool {
        return $this->antyLogoutTime > 0;
    }

    public function resetAntyLogout() : void {
        $this->antyLogoutTime = 0;
        $this->lastDamager = null;
        $this->assists = [];
    }

    public function getAntyLogoutTime() : int {
        return $this->antyLogoutTime;
    }

    public function getAntyLogoutLeftTime() : int {
        return ($this->antyLogoutTime < time() ? 0 : $this->antyLogoutTime);
    }

    public function getLastAttacker() : ?string {
        return $this->lastDamager;
    }

    public function getAssists() : array {
        return $this->assists;
    }

    /* KILLED PLAYERS */

    public function getKilledPlayers() : array {
        return $this->killedPlayers;
    }

    public function addKilledPlayer(string $nick, string $ip) : void {
        $this->killedPlayers[$nick] = ["ip" => $ip, "time" => (time() + Settings::$LAST_KILL_TIME)];
    }

    public function hasKilled(string $nick, ?string $ip = null) : bool {
        foreach($this->killedPlayers as $entityName => $data) {
            if($data["time"] <= time()) {
                unset($this->killedPlayers[$entityName]);
                continue;
            }

            if($entityName === $nick || $data["ip"] === $ip)
                return true;
        }

        return false;
    }

    /* SAFE */

    public function setSafeTime(int $time) : void {
        $this->safe = $time;
    }

    public function isSafe() : bool {
        return $this->safe > time();
    }

    public function getSafeTime() : int {
        return $this->safe;
    }

    public function startSafeGame() : void {
        $player = Server::getInstance()->getPlayerExact($this->name);

        if(!$player)
            return;

        $this->setSafeTime(time() + 60 * 2);

        RandomUtil::randomTeleport([$player]);

        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();

        foreach(Settings::$KITS["Gracz"]["items"] as $item)
            $player->getInventory()->addItem($item);

        Main::getInstance()->getSafeManager()->addSafe($player);
    }

    /* PRIVATE MESSAGE */

    public function getLastPrivateMessage() : ?string {
        return $this->lastPrivateMessage;
    }

    public function setLastPrivateMessage(string $nick) : void {
        $this->lastPrivateMessage = $nick;
    }

    /* LAST ENDER PEARLS */

    public function addLastEnderPearl(Vector3 $position) : void {
        if(count($this->lastEnderPearls) >= 3)
            unset($this->lastEnderPearls[array_key_first($this->lastEnderPearls)]);

        $this->lastEnderPearls[] = $position;
    }

    public function isLastEnderPearlPosition(Vector3 $position) : bool {
        foreach($this->lastEnderPearls as $key => $pos) {
            if($position->equals($pos) || $position->distance($pos) <= 1.5)
                return true;
        }

        return false;
    }

    /* ENDER PEARLS */

    public function getEnderPearls() : array {
        return $this->enderPearls;
    }

    public function addEnderPearl(EnderPearl $enderPearl) : void {
        $this->enderPearls[$enderPearl->getId()] = $enderPearl;
    }

    public function clearEnderPearls() : void {
        foreach($this->enderPearls as $id => $entity)
            $entity->setOwningEntity(null);

        $this->enderPearls = [];
    }

    public function removeEnderPearl(int $id) : void {
        if(!isset($this->enderPearls[$id]))
            return;

        unset($this->enderPearls[$id]);
    }

    /* LAST DATA */

    public function setLastData(string $name, $value, string $type) : void {
        $this->lastData[$name] = ["value" => $value, "type" => $type];
    }

    public function getLastData(string $name) {
        return $this->lastData[$name];
    }

    public function hasLastData(string $name) : bool {
        return isset($this->lastData[$name]);
    }

    public function removeLastData(string $name) : bool {
        if(!$this->hasLastData($name))
            return false;

        unset($this->lastData[$name]);
        return true;
    }

    public function connect() : void {
        $this->isConnected = true;
    }

    public function disconnect() : void {
        $this->isConnected = false;
    }

    public function save() : void {
        $this->dropManager->save();
        $this->backpackManager->save();
        $this->statManager->save();
        $this->homeManager->save();
        $this->ignoreManager->save();
        $this->incognitoManager->save();
        $this->kitManager->save();
        $this->bankManager->save();
        $this->servicesManager->save();
    }

    public function getDropManager() : UserDrop {
        return $this->dropManager;
    }

    public function getBackpackManager() : UserBackpack {
        return $this->backpackManager;
    }

    public function getStatManager() : UserStat {
        return $this->statManager;
    }

    public function getTerrainManager() : UserTerrain {
        return $this->terrainManager;
    }

    public function getHomeManager() : UserHome {
        return $this->homeManager;
    }

    public function getIgnoreManager() : UserIgnore {
        return $this->ignoreManager;
    }

    public function getIncognitoManager() : UserIncognito {
        return $this->incognitoManager;
    }

    public function getKitManager() : UserKit {
        return $this->kitManager;
    }

    public function getBankManager() : UserBank {
        return $this->bankManager;
    }

    public function getServicesManager() : UserServices {
        return $this->servicesManager;
    }
}