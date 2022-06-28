<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 19/11/2016
 * Time: 13:08
 */

namespace RWCORE\ObsidianBreaker;


use pocketmine\event\Listener;

use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;

use pocketmine\event\player\PlayerInteractEvent;

use pocketmine\block\Block;
use pocketmine\item\Item;

use pocketmine\utils\TextFormat as TF;

use RWCORE\Loader;

class OBListener implements Listener
{

    public $plugin;
    private $blockList = [];
    private $toExplode = [];

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
        $this->blockList = OBConfig::parseBlockList($plugin->getConfig()->get("blocks", []));

    }

    public function onExplode(ExplosionPrimeEvent $e)
    {
        if (!$e->isCancelled()) {
            $this->toExplode[$e->getEntity()->getId()] = $e->getForce();
        }
    }

    public function onInteract(PlayerInteractEvent $event){

        $i = $event->getItem();
        $id = $event->getBlock()->getId();
        $player = $event->getPlayer();
        $worth = $i->getDamage();
        $b = $event->getBlock();

        if ($i->getId() === 392 or $i->getId() === 280) {
            $b = $event->getBlock();
            if (isset($this->blockList[$b->getId()])) {
                if ($this->plugin->getData()->exists(OBConfig::getBlockString($b))) {
                    $data = $this->plugin->getData()->get(OBConfig::getBlockString($b));
                    $player->sendMessage("§7 Ten obsydian ma wytrzymalosc: " . TF::YELLOW . $data["health"] . "/".$data["maxHealth"]);
                } else $player->sendMessage("§7 Ten obsydian ma wytrzymalosc: " . TF::YELLOW . $this->blockList[$b->getId()] . "/3");
            }
        }
    }


    public function onExplosion(EntityExplodeEvent $event)
    {
        $p = $event->getEntity();
        $id = $event->getEntity()->getId();
        if (isset($this->toExplode[$id])) {
            $affectedBlocks = OBConfig::getExplosionAffectedBlocks($event->getPosition(), $this->toExplode[$id]);
            foreach ($affectedBlocks as $key => $block) {
                if (isset($this->blockList[$block->getId()])) {
                    $maxHealth = $this->blockList[$block->getId()];
                    if ($this->plugin->getData()->exists(OBConfig::getBlockString($block))) {
                        $existing = $this->plugin->getData()->get(OBConfig::getBlockString($block));
                        if (is_array($existing)) {
                            $health = $existing["health"] - 1;
                            $this->plugin->getData()->set(OBConfig::getBlockString($block), ["health" => $health, "maxHealth" => $existing["maxHealth"]]);
                            $this->plugin->getData()->save(true);
                        }
                    } else {
                        $health = $maxHealth - 1;
                        $this->plugin->getData()->set(OBConfig::getBlockString($block), ["health" => $health, "maxHealth" => $maxHealth]);
                        $this->plugin->getData()->save(true);
                    }
                    if (isset($health) and $health <= 0) {
                        if ($this->plugin->getData()->exists(OBConfig::getBlockString($block))) {
                            $this->plugin->getData()->remove(OBConfig::getBlockString($block));
                            $this->plugin->getData()->save(true);
                        }
                        $event->getPosition()->getLevel()->setBlock($block, Block::get(Block::AIR));
                        foreach ($block->getDrops(Item::get(Item::DIAMOND_PICKAXE)) as $item) $event->getPosition()->getLevel()->dropItem($block, Item::get($item[0], $item[1]));
                    }
                }
            }
        }
    }


}
