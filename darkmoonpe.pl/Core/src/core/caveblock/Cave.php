<?php

namespace core\caveblock;

use core\entity\entities\custom\CaveSpawn;
use core\entity\entities\mobs\Villager;
use core\Main;
use core\util\utils\FileUtil;
use core\util\utils\SkinUtil;
use pocketmine\entity\Entity;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\Server;

class Cave{

    private ?string $tag;
    private ?string $owner;
    private ?array $players;
    private ?Vector3 $spawn;
    private ?Vector3 $villagerSpawn;
    private ?array $settings;
    private ?array $timeSettings;
    private ?string $level;
    private Server $server;

    public function __construct(string $tag, string $owner, array $players, Vector3 $spawn, Vector3 $villagerSpawn, array $settings, array $timeSettings, string $level){
        $this->tag = $tag;
        $this->owner = $owner;
        $this->players = $players;
        $this->timeSettings = $timeSettings;
        $this->settings = $settings;
        $this->villagerSpawn = $villagerSpawn;
        $this->spawn = $spawn;
        $this->level = $level;
        $this->server = Server::getInstance();
    }

    public function getLevel() : string{
        return $this->level;
    }

    public function getName() : ?string{
        return $this->tag;
    }

    public function getOwner() : ?string{
        return $this->owner;
    }

    public function getPlayers() : ?array{
        return $this->players;
    }

    public function isLocked() : bool{
        return $this->settings["locked"] ? true : false;
    }

    public function getSpawn() : ?Vector3{
        return $this->spawn;
    }

    public function getCaveSetting(string $setting) : ?int{
        return (bool) $this->settings[$setting] ? 1 : 0;
    }

    public function getTimeSetting(string $setting) : ?int{
        return (int) $this->timeSettings[$setting];
    }

    public function unsetVillager() : void{
        $level = Server::getInstance()->getLevelByName($this->level);
        foreach($level->getEntities() as $entity) {
            if($entity instanceof Villager)
                $entity->close();
        }

        $this->villagerSpawn = new Vector3(0, 0, 0);
    }

    public function getVillagerSpawn() : ?Vector3{
        return $this->villagerSpawn;
    }

    public function setVillager(Vector3 $pos, $yaw = 180) : void{
        $level = Server::getInstance()->getLevelByName($this->level);
        $nbt = Entity::createBaseNBT(new Position($pos->x, $pos->y, $pos->z, $level), null, $yaw);

        $this->villagerSpawn = new Vector3($pos->x, $pos->y, $pos->z);

        $villager = Entity::createEntity("Villager", $level, $nbt);
        $villager->setNameTag("§l§9QUEST MASTER");
        $villager->spawnToAll();
    }

    public function setSpawn(Vector3 $pos) : void{
        $this->spawn = new Vector3($pos->x, $pos->y, $pos->z);

        $level = Server::getInstance()->getLevelByName($this->level);

        if($level === null)
            return;

        foreach($level->getEntities() as $entity)
            if($entity instanceof CaveSpawn)
                $entity->close();

        $nbtSpawn = Entity::createBaseNBT((new Position($this->spawn->x, $this->spawn->y, $this->spawn->z, $level))->add(0, 1), null, 180, 0);

        is_file(Main::getInstance()->getDataFolder()."/playersSkins/".$this->owner.".png") ? $skin = SkinUtil::getSkinFromPath(Main::getInstance()->getDataFolder()."/playersSkins/".$this->owner.".png") : $skin = SkinUtil::getSkinFromPath(Main::getInstance()->getDataFolder()."/default/Steve.png");


        $nbtSpawn->setTag(new CompoundTag('Skin', [
            new StringTag('Name', "CaveSpawn"),
            new ByteArrayTag('Data', $skin)
        ]));

        (new CaveSpawn($level, $nbtSpawn))->spawnToAll();
    }

    public function addPlayer(string $nick) : void {
        $this->players[$nick] = ["i_beacon" => 0, "o_chest" => 1, "p_block" => 1, "b_block" => 1, "p_item" => 1, "d_item" => 1, "z_perm" => 0];
    }

    public function isOnlineOwner() : bool {
        return !is_null(Server::getInstance()->getPlayerExact($this->owner));
    }

    public function switchSetting(string $setting, $status) : void {
        if($this->getCaveSetting($setting) == $status || is_null($status))
            return;
        $this->getCaveSetting($setting) == 1 ? $this->settings[$setting] = 0 : $this->settings[$setting] = 1;
    }

    public function switchTimeSetting(string $setting, $status) : void {
        if(is_null($status))
            return;

        $this->timeSettings[$setting] = $status;
    }

    public function getOnlinePlayers() : int {

        $count = 0;

        foreach($this->players as $row => $settings){
            if(Server::getInstance()->getPlayerExact($row) !== null)
                $count++;
        }

        return $count;
    }

    /*public function getPlayers() : array {

        $players = [];

        foreach($this->players as $row => $settings){
            if(Server::getInstance()->getPlayerExact($row) !== null)
                array_push($players, $row);
        }

        return $players;
    }*/

    public function isMember(string $nick) : bool{
        return array_key_exists($nick, $this->players);
    }

    public function getPlayerSetting(string $nick, string $setting) : int {
        if(!isset($this->players[$nick]))
            return 0;

        return (bool) $this->players[$nick][$setting] ? 1 : 0;
    }

    public function switchPlayerSetting(string $nick, string $setting, int $status) : void {
        $this->players[$nick][$setting] = $status;
    }

    public function kickPlayer(string $nick) : void {
        if(isset($this->players[$nick]))
            unset($this->players[$nick]);
    }

    public function switchOwner(string $newOwner) : void {
        $oldOwner = $this->owner;

        $this->switchPlayerSetting($oldOwner, "z_perm", 0);

        $this->switchPlayerSetting($newOwner, "z_perm", 1);
        $this->switchPlayerSetting($newOwner, "i_beacon", 1);
        $this->switchPlayerSetting($newOwner, "o_chest", 1);
        $this->switchPlayerSetting($newOwner, "p_block", 1);
        $this->switchPlayerSetting($newOwner, "b_block", 1);
        $this->switchPlayerSetting($newOwner, "p_item", 1);
        $this->switchPlayerSetting($newOwner, "d_item", 1);

        $this->owner = $newOwner;
    }

    public function isOwner(string $nick) : bool {
        return $this->owner === $nick;
    }

    public function remove() : void{

        foreach($this->getPlayers() as $pnick => $row) {
            $pl = Server::getInstance()->getPlayerExact($pnick);

            if(!$pl == null)
                if($pl->getLevel()->getName() === $this->level)
                    $pl->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
        }

        FileUtil::removeLevel($this->level);
        CaveManager::unsetCave($this->tag);
    }

    public function teleport(Player $player) : void{
        if(!$this->server->isLevelLoaded($this->level))
            $this->server->loadLevel($this->level);

        $x = -0.5;
        $y = 47;
        $z = -0.5;

        if(!$this->spawn)
            $this->setSpawn(new Position($x, $y, $z, $this->getServer()->getLevelByName($this->level)));

        $player->teleport(new Position($this->spawn->x, $this->spawn->y, $this->spawn->z, Server::getInstance()->getLevelByName($this->level)));
    }

    public function getServer() : Server{
        return $this->server;
    }
}