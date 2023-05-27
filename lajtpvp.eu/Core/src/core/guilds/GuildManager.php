<?php

declare(strict_types=1);

namespace core\guilds;

use core\Main;
use core\managers\nameTag\NameTagPlayerManager;
use core\utils\Settings;
use core\utils\VectorUtil;
use JetBrains\PhpStorm\Pure;
use pocketmine\item\Item;
use pocketmine\world\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;

class GuildManager {

    /** @var Guild[] */
    private array $guilds = [];
    
    public function __construct(private Main $plugin) {}

    public function createGuild(string $tag, string $name, Vector3 $base, array $leader) : void {
        $this->guilds[] = new Guild($tag, $name, Settings::$DEFAULT_GUILD_SIZE, Settings::$DEFAULT_GUILD_HEARTS, Settings::$DEFAULT_GUILD_HEALTH, Settings::$GOLEM_DEFAULT_HEALTH, $leader, $base, $base, (time() + (Settings::$CONQUER_TIME * 3600)), (time() + (Settings::$EXPIRE_TIME * 3600)), 0, [], [], Settings::$DEFAULT_GUILD_POINTS, Settings::$GUILD_DEFAULT_SLOTS, 0);
    }

    public function loadGuilds() : void {
        $provider = $this->plugin->getProvider();

        foreach ($provider->getQueryResult("SELECT * FROM guilds", true) as $row) {
            $players = [];
            
            foreach ($provider->getQueryResult("SELECT * FROM guilds_permission WHERE guilds = '".$row["tag"]."'", true) as $rowPlayer) {
                $settings = [
                    "block_break" => $rowPlayer["block_break"],
                    "beacon_break" => $rowPlayer["beacon_break"],
                    "block_place" => $rowPlayer["block_place"],
                    "tnt_place" => $rowPlayer["tnt_place"],
                    "interact_chest" => $rowPlayer["interact_chest"],
                    "interact_furnace" => $rowPlayer["interact_furnace"],
                    "interact_beacon" => $rowPlayer["interact_beacon"],
                    "use_custom_blocks" => $rowPlayer["use_custom_blocks"],
                    "add_player" => $rowPlayer["add_player"],
                    "kick_player" => $rowPlayer["kick_player"],
                    "friendly_fire" => $rowPlayer["friendly_fire"],
                    "treasury" => $rowPlayer["treasury"],
                    "panel" => $rowPlayer["panel"],
                    "regeneration" => $rowPlayer["regeneration"],
                    "teleport" => $rowPlayer["teleport"],
                    "battle" => $rowPlayer["battle"],
                    "alliance" => $rowPlayer["alliance"],
                    "alliance_pvp" => $rowPlayer["alliance_pvp"],
                    "chest_locker" => $rowPlayer["chest_locker"]
                ];

                $players[$rowPlayer["nick"]] = new GuildPlayer($rowPlayer["nick"], $rowPlayer["rank"], $rowPlayer["guilds"], $settings);
            }

            $basePosition = VectorUtil::getPositionFromData($row["base_position"]);
            $heartPosition = VectorUtil::getPositionFromData($row["heart_position"]);

            $treasuryItems = [];
            $explodeItems = explode(";", $row["treasury"]);

            foreach($explodeItems as $itemData) {

                if($itemData === "")
                    continue;

                $item = Item::jsonDeserialize(json_decode($itemData, true));

                $namedTag = $item->getNamedTag();

                if(!$namedTag->getTag("treasurySlot"))
                    continue;

                $slot = $namedTag->getInt("treasurySlot");
                $namedTag->removeTag("treasurySlot");

                $treasuryItems[$slot] = $item;
            }

            if(empty($players))
                continue;

            $guild = new Guild($row["tag"], $row["name"], $row["size"], $row["hearts"], $row["health"], $row["golemHealth"], $players, $basePosition, $heartPosition, $row["conquer_time"], $row["expire_time"], (int)$row["tnt"], json_decode($row["alliances"], true), $treasuryItems, $row["points"], $row["slots"], $row["regenerationGold"]);
            $guild->loadRegenerationBlocks();

            $this->guilds[] = $guild;
            $guild->spawnGuildHeart();
        }
    }

    public function save() : void {
        $provider = $this->plugin->getProvider();
        
        foreach($this->guilds as $key => $guild) {
            $guild->saveRegenerationBlocks();
            $guildTag = $guild->getTag();
            $baseSpawn = $guild->getBaseSpawn();
            $heartSpawn = $guild->getHeartSpawn();

            foreach($guild->getPlayers() as $guildPlayer) {

                if($this->existsGuildPlayerInDataBase($guildPlayer->getName())) {

                    $provider->executeQuery("UPDATE guilds_permission SET guilds = '{$guildPlayer->getGuildName()}', rank = '{$guildPlayer->getRank()}' WHERE nick = '{$guildPlayer->getName()}'");

                    foreach($guildPlayer->getSettings() as $setting => $status)
                        $provider->executeQuery("UPDATE guilds_permission SET '$setting' = '$status' WHERE nick = '{$guildPlayer->getName()}'");
                } else {
                    $query = "";

                    foreach($guildPlayer->getSettings() as $setting => $status) {

                        if($query === "")
                            $query = ", ";

                        $query .= $status !== null;

                        if(array_key_last($guildPlayer->getSettings()) !== $setting)
                            $query .= ', ';
                    }

                    $provider->executeQuery("INSERT INTO guilds_permission (nick, guild, rank, beacon_break, block_break, tnt_place, block_place, interact_chest, interact_furnace, interact_beacon, use_custom_blocks, add_player, kick_player, friendly_fire, treasury, panel, regeneration, teleport, battle, alliance, alliance_pvp, chest_locker) VALUES 
                    ('{$guildPlayer->getName()}', '{$guildPlayer->getGuildName()}', '{$guildPlayer->getRank()}' $query)");
                }
            }

            if($this->existsGuildInDatabase($guildTag))
                $provider->executeQuery("UPDATE guilds SET size = '{$guild->getSize()}', hearts = '{$guild->getHearts()}', health = '{$guild->getHealth()}', golemHealth = '{$guild->getGolemHealth()}', base_position = '{$baseSpawn->__toString()}', heart_position = '{$heartSpawn->__toString()}', conquer_time = '{$guild->getConquerTime()}', expire_time = '{$guild->getExpireTime()}', tnt = '". $guild->getTntTime() ."', alliances = '".json_encode($guild->getAlliances())."', treasury = '".$guild->getTreasuryItemsToString()."', points = '".$guild->getPoints()."', slots = '".$guild->getSlots()."', regenerationGold = '".$guild->getRegenerationGold()."' WHERE tag = '{$guildTag}'");
            else
                $provider->executeQuery("INSERT INTO guilds (tag, name, size, hearts, health, golemHealth, base_position, heart_position, conquer_time, expire_time, tnt, alliances, treasury, points, slots, regenerationGold) VALUES 
                ('{$guildTag}', '{$guild->getName()}', '{$guild->getSize()}', '{$guild->getHearts()}', '{$guild->getHealth()}', '{$guild->getGolemHealth()}','{$baseSpawn->__toString()}', '{$heartSpawn->__toString()}', '{$guild->getConquerTime()}', '{$guild->getExpireTime()}', '". $guild->getTntTime() ."', '".json_encode($guild->getAlliances())."', '".$guild->getTreasuryItemsToString()."', '".$guild->getPoints()."', '".$guild->getSlots()."', '".$guild->getRegenerationGold()."')");

            foreach ($provider->getQueryResult("SELECT * FROM guilds_permission", true) as $row) {
                if(!$this->existsGuildPlayer($row["nick"]))
                    $provider->executeQuery("DELETE FROM guilds_permission WHERE nick = '{$row['nick']}'");
            }

            if($guild->getGuildHeart())
                $guild->getGuildHeart()->close();
        }

        foreach ($provider->getQueryResult("SELECT * FROM guilds", true) as $row) {
            if(!$this->existsGuild($row["tag"]))
                $provider->executeQuery("DELETE FROM guilds WHERE tag = '{$row['tag']}'");
        }
    }

    #[Pure] public function existsGuild(string $tag) : bool {
        foreach($this->guilds as $guild) {
            if($guild->getTag() === $tag)
                return true;
        }

        return false;
    }

    public function existsGuildInDatabase(string $tag) : bool {
        return !empty($this->plugin->getProvider()->getQueryResult("SELECT * FROM guilds WHERE tag = '$tag'", true));
    }

    public function deleteGuild(string $tag) : void {
        $guild = $this->getGuild($tag);

        if(!$guild)
            return;

        foreach($guild->getPlayers() as $player => $rank) {
            if(($onlinePlayer = Server::getInstance()->getPlayerExact($player)))
                NameTagPlayerManager::updatePlayersAround($onlinePlayer);
        }

        $guild->delete();

        foreach($this->guilds as $key => $guild) {
            if($guild->getTag() === $tag)
                unset($this->guilds[$key]);
        }
    }

    #[Pure] public function getGuild(string $tag) : ?Guild {
        foreach($this->guilds as $key => $guild) {
            if($guild->getTag() === $tag)
                return $this->guilds[$key];
        }

        return null;
    }

    public function getGuildFromPos(Position $position) : ?Guild {

        foreach($this->guilds as $key => $guild) {
            if($guild->isInPlot($position))
                return $guild;
        }

        return null;
    }

    #[Pure] public function existsGuildPlayer(string $nick) : bool {

        foreach($this->guilds as $key => $guild) {
            if($guild->existsPlayer($nick))
                return true;
        }

        return false;
    }

    #[Pure] public function getPlayerGuild(string $nick) : ?Guild {

        foreach($this->guilds as $key => $guild) {
            if($guild->existsPlayer($nick))
                return $guild;
        }

        return null;
    }

    public function existsGuildPlayerInDataBase(string $nick) : bool {
        return !empty($this->plugin->getProvider()->getQueryResult("SELECT * FROM guilds_permission WHERE nick = '$nick'", true));
    }

    #[Pure] public function getClosestGuild(Position $position) : ?Guild {
        $closestGuild = null;

        foreach($this->guilds as $guild) {

            if($closestGuild === null) {
                $closestGuild = $guild;
                continue;
            }

            $playerPos = clone $position;
            $pos = clone $guild->getHeartSpawn();
            $close = clone $closestGuild->getHeartSpawn();

            if(sqrt(pow($pos->x - $playerPos->x, 2) + pow($pos->z - $playerPos->z, 2)) < sqrt(pow($playerPos->x - $close->x, 2) + pow($playerPos->z - $close->z, 2)))
                $closestGuild = $guild;
        }

        return $closestGuild;
    }

    #[Pure] public function getGuildsCount() : int {
        return count($this->guilds);
    }

    #[Pure] public function getGuildsTags(bool $strToLower = false) : array {
        $array = [];

        foreach($this->guilds as $key => $guild)
            $array[] = $strToLower ? strtolower($guild->getTag()) : $guild->getTag();

        return $array;
    }

    public function getGuilds() : array {
        return $this->guilds;
    }
}