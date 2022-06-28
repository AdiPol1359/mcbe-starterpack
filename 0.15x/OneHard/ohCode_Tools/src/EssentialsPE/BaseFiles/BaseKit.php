<?php
namespace EssentialsPE\BaseFiles;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\Player;

class BaseKit{
    /** @var string */
    protected $name;
    /** @var Item[] */
    protected $items;

    /**
     * @param string $name
     * @param array|Item[] $items
     */
    public function __construct($name, array $items){
        $this->name = $name;
        foreach($items as $i){
            if(!$i instanceof Item){
                $i = explode(" ", $i);
                if(count($i) > 1){
                    $amount = $i[1];
                    unset($i[1]);
                }else{
                    $amount = 1;
                }
                $i = explode(":", $i[0]);
                if(count($i) > 1){
                    $id = $i[0];
                    $meta = $i[1];
                }else{
                    $id = $i[0];
                    $meta = 0;
                }
                $i = new Item($id, $meta, $amount);
            }
            $this->items[$i->getId()] = $i;
        }
    }

    /**
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @return Item[]
     */
    public function getItems(){
        return $this->items;
    }

    /**
     * @param int $id
     * @param int|null $meta
     * @return bool|Item
     */
    public function hasItem($id, $meta = null){
        if(!isset($this->items[$id]) || ($meta !== null && $this->items[$id]->getDamage() !== $meta)){
            return false;
        }
        return $this->items[$id];
    }

    /**
     * @param Inventory $inventory
     */
    public function addToInventory(Inventory $inventory){
        foreach($this->getItems() as $i){
            $inventory->setItem($inventory->firstEmpty(), clone $i);
        }
        // call_user_func_array($inventory->addItem(), $this->getItems());
    }

    /**
     * @param Player $player
     */
    public function giveToPlayer(Player $player){
        $this->addToInventory($player->getInventory());
    }
}