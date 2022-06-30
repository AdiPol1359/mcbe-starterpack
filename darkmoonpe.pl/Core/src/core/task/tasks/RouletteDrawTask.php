<?php

namespace core\task\tasks;

use core\fakeinventory\FakeInventoryAPI;
use core\fakeinventory\inventory\hazard\roulette\RouletteDraw;
use core\Main;
use core\manager\managers\hazard\types\roulette\RouletteGame;
use core\manager\managers\hazard\types\roulette\RouletteManager;
use core\manager\managers\ParticlesManager;
use core\manager\managers\SoundManager;
use core\user\UserManager;
use core\util\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\level\sound\ClickSound;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class RouletteDrawTask extends Task {

    private static self $instance;

    private RouletteDraw $inventory;

    private array $players = [];

    private int $time = 0;
    private int $stop = 0;

    public function __construct() {
        self::$instance = $this;

        $this->inventory = new RouletteDraw();
    }

    public function onRun(int $currentTick) {
        $this->update();

        foreach(FakeInventoryAPI::getInventories() as $player => $inventory) {
            if($inventory instanceof RouletteDraw)
                $inventory->setContents($this->inventory->getContents());
        }
    }

    public function start() : void {
        Main::getInstance()->getScheduler()->scheduleRepeatingTask($this, 5);
    }

    public static function getInstance() : self {
        return self::$instance;
    }

    public function addPlayer(Player $player) : void {
        if(in_array($player->getName(), $this->players))
            return;

        $this->players[] = $player->getName();
    }

    public function removePlayer(Player $player) : void {
        if(($key = array_search($player->getName(), $this->players)) !== false)
            unset($this->players[$key]);
    }

    public function getInventory() : RouletteDraw {
        return $this->inventory;
    }

    public function update() : void {

        $this->time++;

        if($this->time >= 15 && $this->time < 25) {
            if($this->stop == 0)
                $this->stop = 1;
            else {
                $this->stop--;
                return;
            }
        } elseif($this->time >= 25) {
            if($this->stop == 0)
                $this->stop = 2;
            else {
                $this->stop--;
                return;
            }

            if($this->time >= 35) {
                for($s = 1; $s <= 9; $s++) {
                    if($s === 5) {
                        continue;
                    }

                    $slot = 9 + $s - 1;

                    $this->inventory->setItem($slot, Item::get(Item::IRON_BARS)->setCustomName(" "));
                }

                for($i = 0; $i < $this->inventory->getSize(); $i++) {
                    if($this->inventory->getItem($i)->getId() === Item::AIR)
                        $this->inventory->setItem($i, Item::get(Item::IRON_BARS)->setCustomName(" "));
                }

                if($this->time >= 42) {

                    for($s = 1; $s <= 9; $s++) {
                        if($s === 5) {
                            $slot = 9 + $s - 1;
                            $item = $this->inventory->getItem($slot);

                            $winColor = "";

                            if($item->getId() === Item::CONCRETE) {

                                switch($item->getDamage()){

                                    case 14:
                                        $winColor = "Czerwony";
                                        break;

                                    case 15:
                                        $winColor = "Czarny";
                                        break;

                                    case 5:
                                        $winColor = "Zielony";
                                        break;
                                }

                                foreach(RouletteGame::getPlayers() as $nick => $roulettePlayer) {
                                    $user = UserManager::getUser($nick);

                                    $lose = 0;
                                    $profit = 0;

                                    foreach($roulettePlayer->getBetAmounts() as $color => $amount) {

                                        $lose += $amount;

                                        switch($item->getDamage()) {

                                            case 14:

                                                if($color === "Czerwony") {
                                                    $user->addPlayerMoney($amount * 2);
                                                    $profit += $amount * 2;
                                                }

                                                break;

                                            case 15:

                                                if($color === "Czarny") {
                                                    $user->addPlayerMoney($amount * 2);
                                                    $profit += $amount * 2;
                                                }

                                                break;

                                            case 5:

                                                if($color === "Zielony") {
                                                    $user->addPlayerMoney($amount * 7);
                                                    $profit += $amount * 7;
                                                }

                                                break;
                                        }
                                    }

                                    $userPlayer = Server::getInstance()->getPlayerExact($user->getName());

                                    if($userPlayer) {
                                        ParticlesManager::spawnFirework($userPlayer, $userPlayer->getLevel(), [[ParticlesManager::TYPE_SMALL_SPHERE, ParticlesManager::COLOR_DARK_PURPLE], [ParticlesManager::TYPE_SMALL_SPHERE, ParticlesManager::COLOR_BLUE]]);
                                        SoundManager::addSound($userPlayer, $userPlayer->asPosition(), "mob.cat.meow");
                                        $userPlayer->sendMessage(MessageUtil::formatLines(["Wygrany kolor: §l§9" . $winColor, "Obstawiles: §l§9" . $lose . "§r§7zl", "Zyskales: §l§9" . $profit . "§r§7zl"]));
                                    }

                                    RouletteGame::removePlayer($nick);
                                }
                            }
                        }
                    }

                    $this->stop();
                }
                return;
            }
        }

        for($s = 1; $s <= 9; $s++) {
            if($s === 9) {
                $this->inventory->setItem(17, RouletteManager::getRandomItem());
                continue;
            }

            $slot = 9 + $s - 1;

            $this->inventory->setItem($slot, $this->inventory->getItem($slot+1));
        }

        foreach($this->players as $nick) {
            $player = Server::getInstance()->getPlayerExact($nick);

            if(!$player)
                continue;

            $player->getLevel()->addSound(new ClickSound($player), [$player]);
        }
    }

    public function stop() : void {
        $this->getHandler()->cancel();

        for($i = 9; $i <= 17; $i++)
            $this->inventory->setItem($i, Item::get(Item::AIR));

        $this->time = 0;
        $this->stop = 0;

        foreach($this->players as $nick){

            $player = Server::getInstance()->getPlayerExact($nick);

            if(!$player)
                continue;

            $inv = FakeInventoryAPI::getInventory($player->getName());

            if(!$inv)
                continue;

            $inv->closeFor($player);
        }

        RouletteGame::setLock(false);
        RouletteGame::setStartGame(false);

        $this->players = [];
    }
}