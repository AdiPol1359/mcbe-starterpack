<?php

namespace core\anticheat\module\modules;

use core\anticheat\module\BaseModule;
use core\Main;
use pocketmine\entity\Effect;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Hoe;
use pocketmine\scheduler\ClosureTask;

class AntiSpeedMineModule extends BaseModule {

    public function __construct() {
        parent::__construct("FastBreak");
    }

    /**
     * @param PlayerInteractEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function AntiSpeedMineInteract(PlayerInteractEvent $e) : void{

        if(!$this->enabled)
            return;

        if($e->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK)
            $this->data[$e->getPlayer()->getName()]["breakTimes"] = floor(microtime(true) * 20);
    }

    /**
     * @param BlockBreakEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function AntiSpeedMineBlockBreak(BlockBreakEvent $e) : void{

        if($e->getInstaBreak())
            return;

        if(!$this->enabled)
            return;

        $player = $e->getPlayer();
        $name = $player->getName();

        if(!isset($this->data[$name]["counter"])) {
            $this->data[$name]["counter"] = 1;
            return;
        }

        if(!isset($this->data[$name]["breakTimes"])){

            $e->setCancelled(true);

            if($this->data[$name]["counter"] >= 5)
                Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void{
                    if(!$player)
                        return;
                    $player->close("", "§l§cWykryto cheaty!");
                }), 3);
            else
                $this->data[$name]["counter"]++;

            return;
        }

        $target = $e->getBlock();
        $item = $e->getItem();

        if($item instanceof Hoe)
            return;

        $expectedTime = ceil($target->getBreakTime($item) * 20);

        if($player->hasEffect(Effect::HASTE))
            $expectedTime *= 1 - (0.2 * $player->getEffect(Effect::HASTE)->getEffectLevel());

        if($player->hasEffect(Effect::MINING_FATIGUE))
            $expectedTime *= 1 + (0.3 * $player->getEffect(Effect::MINING_FATIGUE)->getEffectLevel());

        $expectedTime -= 1;

        $actualTime = ceil(microtime(true) * 20) - $this->data[$name]["breakTimes"];

        if($actualTime < $expectedTime){

            if(!isset($this->data["notify"][$player->getName()]) || $this->data["notify"][$player->getName()] <= time()) {
                $this->data["notify"][$player->getName()] = time() + 5;
                $this->notifyAdmin($player->getName());
            }

            $e->setCancelled(true);

            if($this->data[$name]["counter"] >= 5)
                Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void{
                    if(!$player)
                        return;
                    $player->close("", "§l§cWykryto cheaty!");
                }), 3);
            else
                $this->data[$name]["counter"]++;

            return;
        }

        $this->data[$name]["counter"]--;
        unset($this->data[$name]);
    }

    public function AntiCheatOnQuit(PlayerQuitEvent $e) : void{
        unset($this->data[$e->getPlayer()->getName()]);
    }

    public function changeSlot(PlayerItemHeldEvent $e) : void {

        if(!$this->enabled)
            return;

        $this->data[$e->getPlayer()->getName()]["breakTimes"] = 0;
    }
}