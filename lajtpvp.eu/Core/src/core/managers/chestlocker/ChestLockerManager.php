<?php

declare(strict_types=1);

namespace core\managers\chestlocker;

use core\Main;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\VectorUtil;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Chest;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\world\World;
use pocketmine\world\Position;
use pocketmine\player\Player;

class ChestLockerManager {

    /** @var ChestLocker[] */
    private array $chests = [];

    public function __construct(private Main $plugin) {
        $this->load();
    }

    public function load() : void {
        $provider = $this->plugin->getProvider();

        foreach($provider->getQueryResult("SELECT * FROM chestlocker", true) as $row) {
            $this->chests[] = new ChestLocker($row["id"], $row["player"], $row["face"], VectorUtil::getPositionFromData($row["position"]), true);
        }
    }

    public function save() : void {
        $provider = $this->plugin->getProvider();

        foreach($this->chests as $key => $chestLocker) {
            if($chestLocker->isRemoved()) {
                 $provider->executeQuery("DELETE FROM chestlocker WHERE id = '".$chestLocker->getId()."'");
                 continue;
            }

            if(!$chestLocker->isFromDataBase())
                $provider->executeQuery("INSERT INTO chestlocker (id, player, face, position) VALUES ('".$chestLocker->getId()."', '".$chestLocker->getPlayer()."', '".$chestLocker->getFace()."', '".$chestLocker->getPosition()->__toString()."')");
        }
    }

    public function createChestLocker(string $playerName, int $face, Position $position) : void {
        $this->chests[] = new ChestLocker($this->getHighestId() + 1, $playerName, $face, $position, false);
    }

    public function removeChestLocker(int $id) : void {
        foreach($this->chests as $key => $chestLocker) {
            if($chestLocker->getId() === $id)
                $chestLocker->remove();

            $position = $chestLocker->getPosition();
            $level = $position->getWorld();

            if($level->getBlock($position)->getId() === BlockLegacyIds::WALL_SIGN) {
                $position->getWorld()->setBlock($position, VanillaBlocks::AIR());
                $position->getWorld()->dropItem($position, ItemFactory::getInstance()->get(ItemIds::SIGN));
            }
        }
    }

    #[Pure] public function getHighestId() : int {
        $id = 0;

        foreach($this->chests as $key => $chestLocker) {
            if($chestLocker->getId() > $id)
                $id = $chestLocker->getId();
        }

        return $id;
    }

    public function getLocker(Position $position) : ?ChestLocker {
        foreach($this->chests as $key => $chestLocker) {
            if($chestLocker->isRemoved())
                continue;

            if($chestLocker->getChestPosition()->equals($position) || $chestLocker->getPosition()->equals($position))
                return $chestLocker;

            $tile = $this->plugin->getServer()->getWorldManager()->getDefaultWorld()->getTile($chestLocker->getChestPosition());

            if($tile instanceof Chest && ($pair = $tile->getPair())) {
                if($tile->isPaired() && $pair->getPosition()->equals($position))
                    return $chestLocker;
            }
        }

        return null;
    }

    public function getLockLimit(Player $player) : int {
        $limit = Settings::$PLAYER_LOCK_LIMIT;

        if(PermissionUtil::has($player, Settings::$PERMISSION_TAG."chestlocker.vip"))
            $limit = Settings::$VIP_LOCK_LIMIT;

        if(PermissionUtil::has($player, Settings::$PERMISSION_TAG."chestlocker.svip"))
            $limit = Settings::$SVIP_LOCK_LIMIT;

        if(PermissionUtil::has($player, Settings::$PERMISSION_TAG."chestlocker.sponsor"))
            $limit = Settings::$SPONSOR_LOCK_LIMIT;

        return $limit;
    }

    #[Pure] public function getPlayerLockedChests(string $playerName) : array {
        $chests = [];

        foreach($this->chests as $key => $lockedChest) {
            if($lockedChest->getPlayer() === $playerName)
                $chests[] = $lockedChest;
        }

        return $chests;
    }
}