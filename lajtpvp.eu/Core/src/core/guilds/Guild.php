<?php

namespace core\guilds;

use core\entities\custom\GuildGolem;
use core\entities\custom\GuildHeart;
use core\Main;
use core\tasks\async\DeleteRegenerationAsyncTask;
use core\tasks\sync\RegenerationTask;
use core\utils\MessageUtil;
use core\utils\Settings;
use core\utils\ShapeUtil;
use core\utils\SkinUtil;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\world\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\scheduler\TaskHandler;
use pocketmine\Server;

class Guild {

    private bool $friendlyFire;
    private bool $regeneration;

    private array $regenerationBlocks;

    private ?GuildHeart $guildHeart;

    private ?GuildGolem $guildGolem;

    private array $allianceRequests;
    private bool $alliancePvp;

    private ?TaskHandler $regenerationTask;
    private string $regenerationFile;

    private int $lastGoldTick = 0;
    private int $lastBattleMessage = 0;
    private int $tntPlaceBlock;

    public function __construct(private string $tag, private string $name, private int $size, private int $hearts, private int $health, private int $golemHealth, private array $players, private Vector3 $baseSpawn, private Vector3 $heartSpawn, private int $conquerTime, private int $expireTime, private int $tnt, private array $alliances, private array $treasury, private int $points, private int $slots, private int $regenerationGold){
        $this->friendlyFire = false;

        $this->guildHeart = null;
        $this->guildGolem = null;
        $this->regeneration = false;
        $this->regenerationBlocks = [];
        $this->allianceRequests = [];
        $this->regenerationTask = null;
        $this->tntPlaceBlock = time();
        $this->alliancePvp = false;

        $this->regenerationFile = Settings::$GUILD_DATA_FOLDER."/".$this->tag."/"."regeneration.txt";
        $this->setDefaultFiles();
    }

    public function getTag() : string{
        return $this->tag;
    }

    public function getName() : string{
        return $this->name;
    }

    public function getBaseSpawn() : Vector3{
        return $this->baseSpawn;
    }

    public function getHeartSpawn() : Vector3{
        return $this->heartSpawn;
    }

    public function getConquerTime() : int{
        return $this->conquerTime;
    }

    public function getExpireTime() : int{
        return $this->expireTime;
    }

    public function setConquerTime(int $time) : void{
        $this->conquerTime = $time;
    }

    public function setExpireTime(int $time) : void{
        $this->expireTime = $time;
    }

    public function getSize() : int {
        return $this->size;
    }

    public function setSize(int $size) : void {
        $this->size = $size;
    }

    public function getHearts() : int {
        return $this->hearts;
    }

    public function setHearts(int $hearts) : void {
        $this->hearts = $hearts;

        $this->getGuildHeart()->updateTag();
    }

    public function reduceHearts(int $hearts = 1) : void {
        $this->setHearts($this->hearts - $hearts);
    }

    public function addHearts(int $hearts = 1) : void {
        $this->setHearts($this->hearts + $hearts);
    }

    public function getGolemHealth() : int {
        return $this->golemHealth;
    }

    public function setGolemHealth(int $health) : void {
        $this->golemHealth = $health;
    }

    public function reduceGolemHealth(int $health = 1) : void {
        $this->setGolemHealth($this->getGolemHealth() - $health);
    }

    public function addGolemHealth(int $health = 1) : void {
        $this->setGolemHealth($this->getGolemHealth() + $health);
    }

    public function isFriendlyFireEnabled() : bool {
        return $this->friendlyFire;
    }

    public function setFriendlyFire(bool $friendlyFire) : void {
        $this->friendlyFire = $friendlyFire;
    }

    #[Pure] public function getPlayer(string $nick) : ?GuildPlayer {

        foreach($this->players as $guildPlayer){
            if($guildPlayer->getName() === $nick)
                return $guildPlayer;
        }

        return null;
    }

    #[Pure] public function existsPlayer(string $nick) : bool {

        foreach($this->players as $guildPlayer){
            if($guildPlayer->getName() === $nick)
                return true;
        }

        return false;
    }

    public function getPlayers() : array {
        return $this->players;
    }

    public function addPlayer(string $nick, string $rank = GuildPlayer::MEMBER, array $settings = []) : void {
        $this->players[$nick] = new GuildPlayer($nick, $rank, $this->tag, $settings);
    }

    public function kickPlayer(string $nick) : void {
        unset($this->players[$nick]);
    }

    public function delete() : void {

        Server::getInstance()->getAsyncPool()->submitTask(new DeleteRegenerationAsyncTask($this->tag, Settings::$GUILD_DATA_FOLDER."/"));

        foreach($this->getAlliances() as $alliance) {
            $allianceGuild = Main::getInstance()->getGuildManager()->getGuild($alliance);

            if(!$allianceGuild)
                continue;

            $allianceGuild->removeAlliance($this->getTag());
        }

        $this->guildHeart?->close();
    }

    public function isInPlot(Position $pos, bool $max = false) : bool {

        $heart = clone $this->heartSpawn;

        $sizePos1 = $heart->add($this->size, 0, $this->size);

        $sizePos2 = $heart->subtract($this->size, 0, $this->size);

        if($max) {
            $pos1 = $sizePos1;
            $pos2 = $sizePos2;
        } else {
            $pos1 = $sizePos1;
            $pos2 = $sizePos2;
        }

        return $pos->getFloorX() <= max($pos1->getFloorX(), $pos2->getFloorX()) && $pos->getFloorX() >= min($pos1->getFloorX(), $pos2->getFloorX()) && $pos->getFloorZ() <= max($pos1->getFloorZ(), $pos2->getFloorZ()) && $pos->getFloorZ() >= min($pos1->getFloorZ(), $pos2->getFloorZ());
    }

    public function isInHeart(Position $pos) : bool {

        $heart = clone $this->heartSpawn;

        $sizePos1 = $heart->add(3, 4, 3);
        $sizePos2 = $heart->subtract(3, 1, 3);

        return $pos->getFloorX() <= max($sizePos1->getFloorX(), $sizePos2->getFloorX()) && $pos->getFloorX() >= min($sizePos1->getFloorX(), $sizePos2->getFloorX()) && $pos->getFloorY() <= max($sizePos1->getFloorY(), $sizePos2->getFloorY()) && $pos->getFloorY() >= min($sizePos1->getFloorY(), $sizePos2->getFloorY()) && $pos->getFloorZ() <= max($sizePos1->getFloorZ(), $sizePos2->getFloorZ()) && $pos->getFloorZ() >= min($sizePos1->getFloorZ(), $sizePos2->getFloorZ());
    }

    public function setBase(Vector3 $base) : void {
        $this->baseSpawn = $base;
    }

    public function setRegeneration(bool $regeneration) : void {

        usort($this->regenerationBlocks, function($a, $b) {
            $diff = $a->y - $b->y;

            return ($diff !== 0) ? $diff : $a->z - $b->z;
        });

        $this->regeneration = $regeneration;

        if($regeneration && !$this->regenerationTask && count($this->regenerationBlocks) > 0)
            $this->regenerationTask = Main::getInstance()->getScheduler()->scheduleRepeatingTask(new RegenerationTask($this), 3);

        if(!$regeneration && $this->regenerationTask) {
            $this->regenerationTask->getTask()->getHandler()->cancel();
            $this->regenerationTask = null;
        }
    }

    public function addRegenerationBlock(Block $block) : void {
        $this->regenerationBlocks[] = $block;
    }

    public function resetRegenerationBlocks() : void {
        $this->regenerationBlocks = [];
    }

    public function getRegenerationBlocks() : array {
        return $this->regenerationBlocks;
    }

    public function isRegenerationEnabled() : bool {
        return $this->regeneration;
    }

    public function getRegenerationGold() : int {
        return $this->regenerationGold;
    }

    public function addRegenerationGold(int $gold) : void {
        $this->regenerationGold += $gold;
    }

    public function resetRegeneration() : void {
        $this->regenerationBlocks = [];
        $this->regeneration = false;
        $this->regenerationTask = null;
    }

    public function removeRegenerationBlock(int $key = -1) : void {
        if($key === -1)
            unset($this->regenerationBlocks[array_key_first($this->regenerationBlocks)]);
        else
            unset($this->regenerationBlocks[$key]);

        $this->lastGoldTick++;

        if($this->lastGoldTick >= Settings::$GUILD_REGENERATION_COST) {
            $this->regenerationGold -= 1;
            $this->lastGoldTick = 0;
        }
    }

    public function getLeader() : ?GuildPlayer {

        foreach($this->players as $guildPlayer) {
            if($guildPlayer->getRank() === GuildPlayer::LEADER)
                return $guildPlayer;
        }

        foreach($this->players as $guildPlayer) {
            if($guildPlayer->getRank() !== GuildPlayer::LEADER) {
                $guildPlayer->setAllSettings(true);
                $guildPlayer->setRank(GuildPlayer::LEADER);
                return $guildPlayer;
            }
        }

        return null;
    }

    #[Pure] public function getColorForPlayer(string $nick) : string {
        $color = "§c";

        if($this->existsPlayer($nick))
            $color = "§a";

        if($this->isAlliancePlayer($nick))
            $color = "§6";

        return $color;
    }

    public function getHealth() : int {
        return $this->health;
    }

    public function setHealth(int $health) : void {
        $this->health = $health;

        $this->guildHeart?->updateTag();
    }

    public function reduceHealth(int $health = 1) : void {
        $this->setHealth($this->getHealth() - $health);
    }

    public function addHeath(int $health = 1) : void {
        $this->setHealth($this->getHealth() + $health);
    }

    public function attack(Player $player, int $health) : void {

        if(($war = Main::getInstance()->getWarManager()->getWar($this->tag)) === null)
            return;

        if(($this->health - $health) <= 0) {
            if(($guild = Main::getInstance()->getGuildManager()->getPlayerGuild($player->getName())))
                $war->endWar($guild->getTag());
        }

        if($this->conquerTime <= time() && !$war->hasEnded()) {
            $this->reduceHealth($health);

            foreach($this->getOnlinePlayers() as $nick => $rank) {
                $onlinePlayer = Server::getInstance()->getPlayerExact($nick);

                if(!$onlinePlayer)
                    continue;

                $onlinePlayer->sendPopup("§cSERCE TWOJEJ GILDII JEST ATAKOWANE §8(§c".$this->getHealth()."§7/§c".Settings::$MAX_GUILD_HEALTH."§8)");
            }
        }
    }

    public function getGuildHeart() : ?GuildHeart {
        if(!$this->guildHeart) {
            ShapeUtil::createGuildShape(($heartPosition = Position::fromObject(clone $this->heartSpawn, Server::getInstance()->getWorldManager()->getWorldByName(Settings::$DEFAULT_WORLD))));
            $this->spawnGuildHeart();
        }

        return $this->guildHeart;
    }

    public function spawnGuildHeart() : void {
        is_file(Main::getInstance()->getDataFolder()."/playersSkins/".$this->getLeader()->getName().".png") ? $skin = SkinUtil::getSkinFromPath(Main::getInstance()->getDataFolder()."/playersSkins/".$this->getLeader()->getName().".png") : $skin = SkinUtil::getSkinFromPath(Main::getInstance()->getDataFolder()."/default/defaultSkin.png");

        $position = clone ($this->getHeartSpawn());
        $position->add(0.5, 1.25, 0.5);

        $nbt = CompoundTag::create()
            ->setString("guilds", $this->tag);

        $location = new Location($position->x, $position->y, $position->z, Server::getInstance()->getWorldManager()->getDefaultWorld(), 180, 0);
        $skin = new Skin("Standard_Custom", $skin, "");

        $nbtSpawn = new GuildHeart($location, $skin, $nbt);
        $nbtSpawn->spawnToAll();
    }

    public function setGuildHeart(GuildHeart $guildHeart) : void {
        $this->guildHeart = $guildHeart;
    }

    public function getOnlinePlayers() : array {

        $players = [];

        foreach($this->players as $guildPlayer) {
            if(Server::getInstance()->getPlayerExact($guildPlayer->getName()))
                $players[$guildPlayer->getName()] = $guildPlayer->getRank();
        }

        return $players;
    }

    public function setGuildGolem(?GuildGolem $golem) : void {
        $this->guildGolem = $golem;
    }

    public function getGuildGolem() : ?GuildGolem {
        return $this->guildGolem;
    }

    public function isTntEnabled() : bool {
        return $this->tnt >= time();
    }

    public function getTntTime() : bool {
        return $this->tnt;
    }

    public function setTnt(int $tnt) : void {
        $this->tnt = $tnt;
    }

    public function setTntPlaceBlock(int $time = 60) : void {
        $this->tntPlaceBlock = (time() + $time);
    }

    public function getTntPlaceBlock() : int {
        return $this->tntPlaceBlock;
    }

    public function explodeTnt() : void {

        foreach($this->getOnlinePlayers() as $onlinePlayer => $rank) {

            $user = Main::getInstance()->getUserManager()->getUser($onlinePlayer);

            if(!$user)
                continue;

            if(!$user->hasLastData(Settings::$TNT_ON_TERRAIN)) {
                if(($player = Server::getInstance()->getPlayerExact($onlinePlayer)) !== null) {
                    $player->sendMessage(MessageUtil::format("§eNa terenie twojej gildii wybuchlo tnt, nie mozesz stawiac blokow przez 60 sekund!"));
                }

                $user->setLFastData(Settings::$TNT_ON_TERRAIN, (time() + Settings::$TNT_ON_TERRAIN_TIME), Settings::$TIME_TYPE);
            }

            $this->setTntPlaceBlock();
        }
    }

    public function getAlliances() : array {
        return $this->alliances;
    }

    #[Pure] public function isAlliance(string $tag) : bool {
        return in_array($tag, $this->alliances);
    }

    #[Pure] public function isAlliancePlayer(string $nick) : bool {
        foreach($this->alliances as $alliance) {
            $guild = Main::getInstance()->getGuildManager()->getGuild($alliance);

            if(!$guild)
                continue;

            if($guild->existsPlayer($nick))
                return true;
        }

        return false;
    }

    public function addAlliance(string $tag) : void {
        $this->alliances[] = $tag;
    }

    public function removeAlliance(string $tag) : void {

        foreach($this->alliances as $key => $allianceTag) {
            if($allianceTag === $tag)
                unset($this->alliances[$key]);
        }
    }

    public function addAllianceRequest(string $tag) : void {

        $guild = Main::getInstance()->getGuildManager()->getGuild($tag);

        if(!$guild)
            return;

        $guild->setAlliancePvp($this->alliancePvp);

        $this->allianceRequests[$tag] = (time() + Settings::$GUILD_ALLIANCES_REQUEST_TIME);
    }

    public function hasAllianceRequest(string $tag) : bool {

        if(isset($this->allianceRequests[$tag])) {
            if($this->allianceRequests[$tag] >= time())
                return true;
        }

        return false;
    }

    public function removeAllianceRequest(string $tag) : void {
        unset($this->allianceRequests[$tag]);
    }

    public function getAllianceRequestTime(string $tag) : int {
        return $this->allianceRequests[$tag];
    }

    public function isAlliancePvpEnabled() : bool {
        return $this->alliancePvp;
    }

    public function setAlliancePvp(bool $pvp) : void {
        $this->alliancePvp = $pvp;

        foreach($this->alliances as $alliance) {
            if(($guild = Main::getInstance()->getGuildManager()->getGuild($alliance)) === null)
                continue;

            $guild->alliancePvp = $pvp;
        }
    }

    public function getTreasuryItemsToString() : string {

        $string = "";

        foreach($this->treasury as $slot => $item) {
            $namedTag = $item->getNamedTag();
            $namedTag->setInt("treasurySlot", $slot);

            $string .= json_encode($item->jsonSerialize()).";";
        }

        return $string;
    }

    public function getTreasury() : array {
        return $this->treasury;
    }

    public function addItemToTreasury(int $slot, Item $item) : void {
        $this->treasury[$slot] = $item;
    }

    public function getItemFromTreasury(int $slot) : Item {
        return $this->treasury[$slot] ?? ItemFactory::air();
    }

    public function removeItemFromTreasury(int $slot) : void {
        unset($this->treasury[$slot]);
    }

    public function setDefaultFiles() : void {

        $dataFolder = Settings::$GUILD_DATA_FOLDER;

        if(!is_dir($dataFolder."/".$this->tag))
            mkdir($dataFolder."/".$this->tag);

        if(!is_file(Settings::$GUILD_DATA_FOLDER."/".$this->tag."/"."regeneration.txt"))
            fopen($this->regenerationFile, 'wb');
    }

    public function getRegenerationFile() : string {
        if(!is_file(Settings::$GUILD_DATA_FOLDER."/".$this->tag."/"."regeneration.txt"))
            $this->setDefaultFiles();

        return $this->regenerationFile ?? "";
    }

    public function loadRegenerationBlocks() : void {

        $contents = file_get_contents($this->getRegenerationFile());
        $explodeData = explode(";", $contents);

        foreach($explodeData as $fullData) {
            $data = explode(":", $fullData);

            if(!isset($data[4]))
                continue;

            $block = BlockFactory::getInstance()->get($data[0], $data[1]);
            $block->position($block->getPosition()->getWorld(), $data[2], $data[3], $data[4]);

            $this->addRegenerationBlock($block);
        }
    }

    public function saveRegenerationBlocks() : void {

        $data = "";

        foreach($this->regenerationBlocks as $block) {
            if(!is_file($this->getRegenerationFile()))
                return;

            $data .= $block->getId().":".$block->getMeta().":".$block->getX().":".$block->getY().":".$block->getZ().";";
        }

        file_put_contents($this->getRegenerationFile(), $data);
    }

    public function getPoints() : int {
        return $this->points;
    }

    public function addPoints(int $value) : void {
        $this->points += $value;
    }

    public function reducePoints(int $value) : void {
        $this->points -= $value;
    }

    public function setPoints(int $value) : void {
        $this->points = $value;
    }

    public function getSlots() : int {
        return $this->slots;
    }

    public function addSlot(int $count = 1) : void {
        $this->slots += $count;
    }

    public function getLastBattleMessage() : int {
        return $this->lastBattleMessage;
    }

    public function setLastBattleMessage(int $time) : void {
        $this->lastBattleMessage = $time;
    }

    public function canSendBattleMessage() : bool {
        return $this->lastBattleMessage <= time();
    }
}