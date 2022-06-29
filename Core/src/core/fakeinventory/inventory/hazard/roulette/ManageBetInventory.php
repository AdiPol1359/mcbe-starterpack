<?php

namespace core\fakeinventory\inventory\hazard\roulette;

use core\fakeinventory\FakeInventory;
use core\manager\managers\hazard\types\roulette\RouletteGame;
use core\manager\managers\PacketManager;
use core\manager\managers\ServerManager;
use core\manager\managers\SoundManager;
use core\user\UserManager;
use core\util\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\Player;

class ManageBetInventory extends FakeInventory {

    private float $amount;
    private float $newAmout;
    private string $color;
    private ?Item $clickedItem;

    public function __construct(Player $player, Item $item) {
        parent::__construct($player, "§9§lHAZARD", self::SMALL);
        $this->init($item);
        $this->setItems();
    }

    private function init(Item $item) : void {
        $namedtag = $item->getNamedTag();

        $this->amount = $namedtag->getFloat("BetAmount");
        $this->newAmout = 0;
        $this->color = $namedtag->getString("BetColor");
        $this->clickedItem = $item;
    }

    public function setItems() : void {

        $this->fillBars();

        $red = Item::get(Item::STAINED_GLASS, 14)->setCustomName("§l§8(§c-§c5.0§7zl§8)");
        $orange = Item::get(Item::STAINED_GLASS, 1)->setCustomName("§l§8(§c-§61.0§7zl§8)");
        $yellow = Item::get(Item::STAINED_GLASS, 4)->setCustomName("§l§8(§c-§e0.1§7zl§8)");

        $purple = Item::get(Item::STAINED_GLASS, 3)->setCustomName("§l§8(§a+§b0.1§7zl§8)");
        $blue = Item::get(Item::STAINED_GLASS, 11)->setCustomName("§l§8(§a+§91.0§7zl§8)");
        $light_blue = Item::get(Item::STAINED_GLASS, 10)->setCustomName("§l§8(§a+§d5.0§7zl§8)");

        $limeDye = Item::get(Item::DYE, 10)->setCustomName("§l§aZATWIERDZ BET");
        $redDye = Item::get(Item::DYE, 1)->setCustomName("§l§cRESETUJ BET");

        $netherStar = Item::get(Item::NETHER_STAR)->setCustomName("§l§cPOWORT");

        $this->setItemAt(1, 2, $red);
        $this->setItemAt(2, 2, $orange);
        $this->setItemAt(3, 2, $yellow);

        $this->setItemAt(7, 2, $purple);
        $this->setItemAt(8, 2, $blue);
        $this->setItemAt(9, 2, $light_blue);

        $this->setItemAt(4, 3, $limeDye);
        $this->setItemAt(6, 3, $redDye);

        $this->setItemAt(5, 3, $netherStar);

        if($this->clickedItem) {
            $amount = (float) number_format(($this->amount + $this->newAmout), 2, '.', '');
            $this->clickedItem->setCustomName("§7".$this->color . ": §l§9" . $amount."§r§7zl");
            $this->setItemAt(5, 2, $this->clickedItem);
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        if(!ServerManager::isSettingEnabled(ServerManager::HAZARD)) {
            $this->closeFor($player);
            $player->sendMessage(MessageUtil::format("Hazard jest aktualnie wylaczony!"));
            return true;
        }

        if(RouletteGame::isLocked()){
            $this->close($player);
            return true;
        }

        if($sourceItem->getId() === Item::STAINED_GLASS) {

            switch($sourceItem->getDamage()) {
                case 14:

                    if(($this->newAmout + $this->amount) >= 5.0)
                        $this->newAmout -= 5.0;
                    else
                        $this->newAmout = 0;
                    break;

                case 1:
                    if(($this->newAmout + $this->amount) >= 1.0)
                        $this->newAmout -= 1.0;
                    else
                        $this->newAmout = 0;
                    break;

                case 4:
                    if(($this->newAmout + $this->amount) >= 0.1)
                        $this->newAmout -= 0.1;
                    else
                        $this->newAmout = 0;
                    break;

                //dodanie

                case 10:
                    $this->newAmout += 5.0;
                    break;

                case 11:
                    $this->newAmout += 1.0;
                    break;

                case 3:
                    $this->newAmout += 0.1;
                    break;
            }

            $this->setItems();
        }

        if($sourceItem->getId() === Item::DYE){

            if(($this->amount + $this->newAmout) < 1) {
                $this->close($player);
                $player->sendMessage(MessageUtil::format("Kwota jaka chcesz obstawic musi byc wieksza jak §l§91.00§r§8zl§7!"));
                return true;
            }

            $user = UserManager::getUser($this->player->getName());

            if(!RouletteGame::getPlayer($player->getName()))
                RouletteGame::createRoulettePlayer($player->getName());

            $roulettePlayer = RouletteGame::getPlayer($player->getName());

            switch($sourceItem->getDamage()) {

                case "1":

                    if($this->clickedItem)
                        $this->init($this->clickedItem);

                    $betAmount = $roulettePlayer->getBetAmount($this->color);

                    $user->addPlayerMoney($betAmount);

                    $amountMinimum = 0;
                    $this->amount = 0;
                    $this->newAmout = 0;

                    $roulettePlayer->setBet($this->color, 0);

                    foreach(RouletteGame::getPlayer($player->getName())->getBetAmounts() as $bet => $amount) {
                        if($amount > $amountMinimum)
                            $amountMinimum = $amount;
                    }

                    if($amountMinimum <= 0)
                        RouletteGame::removePlayer($player->getName());

                    break;

                case "10":

                    $betAmount = $roulettePlayer->getBetAmount($this->color);

                    if($user->getPlayerMoney() >= $this->newAmout) {
                        if(($this->amount + $this->newAmout) > $betAmount)
                            $user->reducePlayerMoney($this->newAmout);
                        elseif(($this->amount + $this->newAmout) < $betAmount)
                            $user->addPlayerMoney(($betAmount - ($this->amount + $this->newAmout)));

                        if(($this->amount + $this->newAmout) !== 0)
                            $roulettePlayer->setBet($this->color, ($this->amount + $this->newAmout));
                    }else{
                        $this->closeFor($player);
                        $player->sendMessage(MessageUtil::format("Nie masz tyle pieniedzy aby taka kwote obstawic!"));
                    }

                    break;

            }

            $this->setItems();
        }

        if($sourceItem->getId() === Item::NETHER_STAR)
            (new ChooseColor($player))->openFor([$player]);

        PacketManager::unClickButton($player);
        return true;
    }
}