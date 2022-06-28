<?php

namespace Gildie\guild;

use Core\api\NameTagsAPI;
use pocketmine\level\Level;
use pocketmine\Player;

use pocketmine\block\Block;

use pocketmine\math\Vector3;

use pocketmine\entity\Entity;

use pocketmine\level\Position;

use pocketmine\item\{
	Item, enchantment\Enchantment, enchantment\EnchantmentInstance
};

use Gildie\Main;
use pocketmine\Server;

class GuildManager {

    private $db;

    private $guilds = [];
    private $playersGuilds = [];

    public const PERMISSION_STONIARKI = "stoniarki";
    public const PERMISSION_STONIARKI_DESTROY = "stoniarki_destroy";
    public const PERMISSION_BLOCKS_PLACE = "blocks_place";
    public const PERMISSION_BLOCKS_BREAK = "blocks_break";
    public const PERMISSION_TNT_PLACE = "tnt_place";
    public const PERMISSION_CHEST_PLACE_BREAK = "chest_pb";
    public const PERMISSION_CHEST_OPEN = "chest_o";
    public const PERMISSION_FURNACE_PLACE_BREAK = "furnace_pb";
    public const PERMISSION_FURNACE_OPEN = "furnace_o";
    public const PERMISSION_BEACON_PLACE_BREAK = "beacon_pb";
    public const PERMISSION_BEACON_OPEN = "beacon_o";
    public const PERMISSION_SKARBIEC_OPEN = "skarbiec_o";
    public const PERMISSION_TPACCEPT = "tpaccept";
    public const PERMISSION_LAVA = "lava";
    public const PERMISSION_WATER = "water";
    public const PERMISSION_INTERACT = "interact";
    public const PERMISSION_PVP = "pvp";
    public const PERMISSION_INVITE_MEMBERS = "invite_members";
    public const PERMISSION_KICK_MEMBERS = "kick_members";
    public const PERMISSION_SET_PERMISSIONS = "set_permissions";

    public function __construct(Main $main) {
        $this->db = $main->getDb();
        $this->init();
    }

    private function init() : void {
        $db = $this->db;

        $db->query("CREATE TABLE IF NOT EXISTS permissions (nick TEXT, ".self::PERMISSION_STONIARKI." INT, ".self::PERMISSION_STONIARKI_DESTROY." INT, ".self::PERMISSION_BLOCKS_PLACE." INT, ".self::PERMISSION_BLOCKS_BREAK." INT, ".self::PERMISSION_TNT_PLACE." INT, ".self::PERMISSION_CHEST_PLACE_BREAK." INT, ".self::PERMISSION_CHEST_OPEN." INT, ".self::PERMISSION_FURNACE_PLACE_BREAK." INT, ".self::PERMISSION_FURNACE_OPEN." INT, ".self::PERMISSION_BEACON_PLACE_BREAK." INT, ".self::PERMISSION_BEACON_OPEN." INT, ".self::PERMISSION_SKARBIEC_OPEN." INT, ".self::PERMISSION_TPACCEPT." INT, ".self::PERMISSION_LAVA." INT, ".self::PERMISSION_WATER." INT, ".self::PERMISSION_INTERACT." INT, ".self::PERMISSION_PVP." INT, ".self::PERMISSION_INVITE_MEMBERS." INT, ".self::PERMISSION_KICK_MEMBERS." INT, ".self::PERMISSION_SET_PERMISSIONS." INT)");
    }

    public function isInGuild(string $player) : bool {
        $player = strtolower($player);

        $array = $this->db->query("SELECT * FROM players WHERE LOWER(player) = '$player'")->fetchArray();

        return !empty($array);
    }

    public function isGuildExists(string $guild) : bool {
        $guild = strtolower($guild);

        $array = $this->db->query("SELECT * FROM guilds WHERE LOWER(guild) = '$guild'")->fetchArray();

        return !empty($array);
    }

    public function isPlot(int $x, int $z) : bool {
        $array = $this->db->query("SELECT * FROM plots WHERE '$x' <= x1 AND '$x' >= x2 AND '$z' <= z1 AND '$z' >= z2")->fetchArray();

        return !empty($array);
    }

    public function isMaxPlot(int $x, int $z) : bool {
        $array = $this->db->query("SELECT * FROM plots WHERE '$x' <= max_x1 AND '$x' >= max_x2 AND '$z' <= max_z1 AND '$z' >= max_z2")->fetchArray();

        return !empty($array);
    }

    public function isInOwnPlot(Player $player, Vector3 $pos) : bool {
        return $this->getPlayerGuild($player->getName()) === $this->getGuildFromPos($pos->getFloorX(), $pos->getFloorZ());
    }

    public function getGuildFromPos(int $x, int $z) : ?Guild {

        $array = $this->db->query("SELECT * FROM plots WHERE '$x' <= x1 AND '$x' >= x2 AND '$z' <= z1 AND '$z' >= z2")->fetchArray();

        return $this->getGuildByTag($array['guild']);
    }

    public function getPlayerGuild(string $player) : ?Guild {
        $player = strtolower($player);

        if(isset($this->playersGuilds[$player]))
            return $this->playersGuilds[$player];

        $array = $this->db->query("SELECT * FROM players WHERE LOWER(player) = '$player'")->fetchArray(SQLITE3_ASSOC);
        $guild = $this->getGuildByTag($array['guild']);

        if($guild == null)
            return null;

        $this->playersGuilds[$player] = $guild;
        return $guild;
    }

    public function getGuildByTag(?string $tag) : ?Guild {
        if($tag == null || !$this->isGuildExists($tag)) return null;

        if(!isset($this->guilds[strtolower($tag)]))
            $this->guilds[strtolower($tag)] = new Guild($tag);

        return $this->guilds[strtolower($tag)];
    }

    public function isInSameGuild(Player $player1, Player $player2) : bool {
        return $this->getPlayerGuild($player1->getName()) === $this->getPlayerGuild($player2->getName());
    }

    public function unsetGuild(string $tag) : void {
        unset($this->guilds[strtolower($tag)]);
    }

    public function unsetPlayerGuild(string $player) : void {
        unset($this->playersGuilds[strtolower($player)]);
    }

    public function getGuildFromHeart(Vector3 $pos) : ?Guild {
        $x = (int) $pos->getFloorX();
        $y = (int) $pos->getFloorY();
        $z = (int) $pos->getFloorZ();

        $array = $this->db->query("SELECT * FROM guilds WHERE heart_x = '$x' AND heart_y = '$y' AND heart_z = '$z'")->fetchArray(SQLITE3_ASSOC);

        return $this->getGuildByTag($array['guild']);
    }

    public function isHeart(Block $block) : bool {
        return $this->getGuildFromHeart($block) !== null && $block->getId() == Block::END_PORTAL_FRAME;
    }

    public function getGuildsTop() : array {
        $top = [];
        $guilds = [];
        $b_guilds = [];

        $result = $this->db->query("SELECT * FROM guilds");

        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            $guild = $this->getGuildByTag($row['guild']);

            $membersCount = $guild->getMembersCount();
            $points = $guild->getPoints();

            if($membersCount >= 3)
                $guilds[strtolower($guild->getTag())] = $points;
            else
                $b_guilds[strtolower($guild->getTag())] = $points;
            }

        arsort($guilds);
        arsort($b_guilds);

        $i = 1;

        foreach($guilds as $tag => $pkt){
            $top[$i] = $this->getGuildByTag($tag);
            $i += 1;
        }

        $b = count($guilds) + 11;
        foreach($b_guilds as $tag => $pkt){
            $top[$b] = $this->getGuildByTag($tag);
            $b += 1;
        }

        return $top;
    }
    
  public static function getItems(Player $player) : array {
  	/*$cobblex = Item::get(48, 0, 64);
	 	$cobblex->setCustomName("§r§l§4CobbleX");
 		$cobblex->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));*/
 		
 		$items = [
 		 Item::get(Item::EMERALD_BLOCK, 0, 32),
 		 Item::get(Item::DIAMOND_BLOCK, 0, 32),
 		 Item::get(Item::IRON_BLOCK, 0, 32),
 		 Item::get(Item::GOLD_BLOCK, 0, 32),
 		 Item::get(Item::BOOKSHELF, 0, 32)
 		];
 		
 		if($player->hasPermission("nicecraft.gildie.sponsor")) {
 			
 		$items = [
 		 Item::get(Item::EMERALD_BLOCK, 0, 32),
 		 Item::get(Item::DIAMOND_BLOCK, 0, 32),
 		 Item::get(Item::IRON_BLOCK, 0, 32),
 		 Item::get(Item::GOLD_BLOCK, 0, 32),
 		 Item::get(Item::BOOKSHELF, 0, 32)
 		];
 		
 		} 
 		
     return $items;
  }

  public function getAllPermissions() : array {
        return [
            self::PERMISSION_STONIARKI,
            self::PERMISSION_STONIARKI_DESTROY,
            self::PERMISSION_BLOCKS_PLACE,
            self::PERMISSION_BLOCKS_BREAK,
            self::PERMISSION_TNT_PLACE,
            self::PERMISSION_CHEST_PLACE_BREAK,
            self::PERMISSION_CHEST_OPEN,
            self::PERMISSION_FURNACE_PLACE_BREAK,
            self:: PERMISSION_FURNACE_OPEN,
            self::PERMISSION_BEACON_PLACE_BREAK,
            self::PERMISSION_BEACON_OPEN,
            self::PERMISSION_SKARBIEC_OPEN,
            self::PERMISSION_TPACCEPT,
            self::PERMISSION_LAVA,
            self::PERMISSION_WATER,
            self::PERMISSION_INTERACT,
            self::PERMISSION_PVP,
            self::PERMISSION_INVITE_MEMBERS,
            self::PERMISSION_KICK_MEMBERS,
            self::PERMISSION_SET_PERMISSIONS
        ];
  }

    public function getDefaultPermissions() : array {
        return [
            self::PERMISSION_STONIARKI,
            self::PERMISSION_STONIARKI_DESTROY,
            self::PERMISSION_BLOCKS_PLACE,
            self::PERMISSION_BLOCKS_BREAK,
            self::PERMISSION_TNT_PLACE,
            self::PERMISSION_CHEST_PLACE_BREAK,
            self::PERMISSION_CHEST_OPEN,
            self::PERMISSION_FURNACE_PLACE_BREAK,
            self:: PERMISSION_FURNACE_OPEN,
            self::PERMISSION_BEACON_PLACE_BREAK,
            self::PERMISSION_BEACON_OPEN,
            self::PERMISSION_SKARBIEC_OPEN,
            self::PERMISSION_TPACCEPT,
            self::PERMISSION_LAVA,
            self::PERMISSION_WATER,
            self::PERMISSION_INTERACT,
        ];
    }

  public function setPermission(string $nick, string $permission) : void {
        $nick = strtolower($nick);
        $this->db->query("UPDATE permissions SET '$permission' = '1' WHERE nick = '$nick'");
  }

  public function removePermission(string $nick, string $permission) : void {
      $nick = strtolower($nick);
        $this->db->query("UPDATE permissions SET '$permission' = '0' WHERE nick = '$nick'");
  }

    public function hasPermission(string $nick, string $permisison) : int {
        $nick = strtolower($nick);
        return $this->db->query("SELECT * FROM permissions WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC)[$permisison];
    }

  public function switchPermission(string $nick, string $permission) : void {
        if(self::hasPermission($nick, $permission))
            self::removePermission($nick, $permission);
        else
            self::setPermission($nick, $permission);
  }

  public function setAllPermissions(string $nick) : void {
        $nick = strtolower($nick);

        $array = $this->db->query("SELECT * FROM permissions WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC);

        if(empty($array))
            $this->db->query("INSERT INTO permissions VALUES ('$nick', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1')");

        foreach(self::getAllPermissions() as $permission)
            self::setPermission($nick, $permission);
  }

  public function removeAllPermissions(string $nick) : void {
        foreach(self::getAllPermissions() as $permission)
            self::removePermission($nick, $permission);
  }

    public function setDefaultPermissions(string $nick) : void {
        $nick = strtolower($nick);

        $array = $this->db->query("SELECT * FROM permissions WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC);

        if(empty($array))
            $this->db->query("INSERT INTO permissions VALUES ('$nick', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1')");

        self::removeAllPermissions($nick);

        foreach(self::getDefaultPermissions() as $permission)
            self::setPermission($nick, $permission);
    }

    public function updateNameTags() : void {
        $guildManager = Main::getInstance()->getGuildManager();

        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
            foreach($player->getViewers() as $vierwer) {
                if($vierwer->hasPermission("nicecraft.nametag.ignore"))
                    continue;

                if($guildManager->isInGuild($vierwer->getName())) {
                    $guild = $guildManager->getPlayerGuild($vierwer->getName());
                    $tag = $guild->getTag();

                    if($guildManager->isInSameGuild($player, $vierwer))
                        $vierwer->sendData($player, [Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, "§4[{$tag}] ".NameTagsAPI::getGuildNameTag($vierwer)]]);
                    else {
                        if($guildManager->isInGuild($player->getName())) {
                            $p_guild = $guildManager->getPlayerGuild($player->getName());

                            if($guild->hasAllianceWith($p_guild)) {
                                $vierwer->sendData($player, [Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, "§6[{$tag}] ".NameTagsAPI::getGuildNameTag($vierwer)]]);
                                continue;
                            }
                        }
                        $vierwer->sendData($player, [Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, "§c[{$tag}] ".NameTagsAPI::getGuildNameTag($vierwer)]]);
                    }
                }
            }
        }
    }
}