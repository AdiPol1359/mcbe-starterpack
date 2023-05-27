<?php

declare(strict_types=1);

namespace core\managers\villager;

use core\entities\custom\VillagerShopEntity;
use core\Main;
use core\utils\SkinUtil;
use core\utils\VectorUtil;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\world\Position;
use pocketmine\nbt\tag\CompoundTag;

class VillagerShopManager {

    /** @var VillagerShop[] */
    private array $villagers = [];

    public function __construct(private Main $plugin) {
        $this->loadVillagers();
    }

    public function createShop(int $id, string $name, Position $position) : void {
        $this->villagers[] = new VillagerShop($id, $name, [], $position);
    }

    public function loadVillagers() : void {
        $provider = $this->plugin->getProvider();

        foreach($provider->getQueryResult("SELECT * FROM villagers", true) as $row) {
            $items = [];

            $explodeItems = explode(";", $row["items"]);

            foreach($explodeItems as $explodeItem) {
                $jsonData = json_decode($explodeItem, true);
                if($jsonData === null)
                    continue;

                $itemData = Item::jsonDeserialize($jsonData);

                if(!$itemData instanceof Item)
                    continue;

                $namedTag = $itemData->getNamedTag();

                if(!$namedTag->getTag("shopSlot") || !$namedTag->getTag("costItem"))
                    continue;

                $slot = $namedTag->getInt("shopSlot");

                $items[$slot] = $itemData;
            }

            $this->villagers[] = new VillagerShop((int)$row["id"], $row["name"], $items, VectorUtil::getPositionFromData($row["position"]));

            $this->spawnVillager((int)$row["id"], VectorUtil::getPositionFromData($row["position"]));
        }
    }

    public function spawnVillager(int $id, Position $position) : void {
        foreach($this->plugin->getServer()->getWorldManager()->getDefaultWorld()->getEntities() as $entity) {
            if($entity instanceof VillagerShopEntity) {
                if($entity->getId() === $id)
                    $entity->close();
            }
        }

        $entityNbt = CompoundTag::create()
            ->setInt("villagerId", $id);

        $skin = new Skin("Standard_Custom", SkinUtil::getSkinFromPath($this->plugin->getDataFolder()."/default/villager.png"), "");

        (new VillagerShopEntity(Location::fromObject($position->asVector3(), $position->getWorld(), 0, 0), $skin, $entityNbt))->spawnToAll();
    }

    public function save() : void {
        $provider = $this->plugin->getProvider();

        foreach($this->villagers as $villagerShop) {

            $items = "";

            foreach($villagerShop->getItems() as $slot => $item)
                $items .= json_encode($item->jsonSerialize()).";";

            if(empty($provider->getQueryResult("SELECT * FROM villagers WHERE id = '".$villagerShop->getId()."'", true)))
                $provider->executeQuery("INSERT INTO villagers (id, name, items, position) VALUES ('".$villagerShop->getId()."', '".$villagerShop->getName()."', '".$items."', '".$villagerShop->getPosition()->__toString()."')");
            else
                $provider->executeQuery("UPDATE villagers SET name = '".$villagerShop->getName()."', items = '".$items."', position = '".$villagerShop->getPosition()->__toString()."' WHERE id = '".$villagerShop->getId()."'");
        }

        foreach($provider->getQueryResult("SELECT * FROM villagers", true) as $row) {
            if($this->getVillager((int)$row["id"]) === null)
                $provider->executeQuery("DELETE FROM villagers WHERE id = '".$row["id"]."'");
        }

        foreach($this->plugin->getServer()->getWorldManager()->getDefaultWorld()->getEntities() as $entity) {
            if($entity instanceof VillagerShopEntity)
                $entity->close();
        }
    }

    #[Pure] public function getVillager(int $id) : ?VillagerShop {
        foreach($this->villagers as $villagerShop) {
            if($villagerShop->getId() === $id)
                return $villagerShop;
        }

        return null;
    }

    public function getHighestId() : int {
        $id = 0;

        foreach($this->villagers as $villagerShop) {
            if($villagerShop->getId() >= $id)
                $id = $villagerShop->getId() + 1;
        }

        $highestDbId = $this->plugin->getProvider()->getQueryResult("SELECT * FROM villagers ORDER BY id DESC LIMIT 0, 1", true)["id"] ?? 1;
        if($highestDbId >= $id)
            $id = $highestDbId + 1;

        return $id;
    }

    public function removeVillager(int $id) : void {
        foreach($this->villagers as $key => $villagerShop) {
            if($villagerShop->getId() === $id)
                unset($this->villagers[$key]);
        }
    }

    public function getVillagers() : array {
        return $this->villagers;
    }
}