<?php

declare(strict_types=1);

namespace core\anticheat\modules;

use core\anticheat\BaseModule;
use core\Main;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Hoe;
use pocketmine\scheduler\ClosureTask;

class AntiSpeedMineModule extends BaseModule {

    #[Pure] public function __construct() {
        parent::__construct("FastBreak");
    }

    /**
     * @param PlayerInteractEvent $e
     * @priority LOWEST
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
     * @priority LOWEST
     * @ignoreCancelled true
     */

    public function AntiSpeedMineBlockBreak(BlockBreakEvent $e) : void{

        if(!$e->isCancelled()) {
            if($e->getInstaBreak())
                return;

            $player = $e->getPlayer();
            $name = $player->getName();

            if(!isset($this->data[$name]["counter"])) {
                $this->data[$name]["counter"] = 1;
                return;
            }

            if(!isset($this->data[$name]["breakTimes"])) {

                if($this->enabled)
                    $e->cancel();

                if($this->data[$name]["counter"] >= 5)
                    if($this->enabled) {
                        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void {
                            if(!$player)
                                return;
                            $player->kick("", "§l§cWykryto cheaty!");
                        }), 3);
                    } else
                        $this->data[$name]["counter"]++;

                return;
            }

            $target = $e->getBlock();
            $item = $e->getItem();

            if($item instanceof Hoe)
                return;

            $expectedTime = ceil($target->getBreakInfo()->getBreakTime($item) * 20);


            if($player->getEffects()->has(VanillaEffects::HASTE()))
                $expectedTime *= 1 - (0.25 * $player->getEffects()->get(VanillaEffects::HASTE())->getEffectLevel());

            if($player->getEffects()->has(VanillaEffects::MINING_FATIGUE()))
                $expectedTime *= 1 + (0.35 * $player->getEffects()->get(VanillaEffects::MINING_FATIGUE())->getEffectLevel());

            $expectedTime -= 1;

            $actualTime = ceil(microtime(true) * 20) - $this->data[$name]["breakTimes"];

            if($actualTime < $expectedTime) {

                if(!isset($this->data["notify"][$player->getName()]) || $this->data["notify"][$player->getName()] <= time()) {
                    $this->data["notify"][$player->getName()] = time() + 5;
                    $this->notifyAdmin($player->getName());
                }

                if($this->enabled)
                    $e->cancel();

                if($this->data[$name]["counter"] >= 5)
                    if($this->enabled) {
                        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void {
                            if(!$player)
                                return;
                            $player->kick("", "§l§cWykryto cheaty!");
                        }), 3);
                    } else
                        $this->data[$name]["counter"]++;

                return;
            }

            $this->data[$name]["counter"]--;
            unset($this->data[$name]);
        }
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