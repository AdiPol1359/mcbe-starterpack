<?php

namespace core\form\forms\shop\normal;

use core\form\forms\Error;
use core\form\BaseForm;
use core\manager\managers\LogManager;
use core\manager\managers\ServerManager;
use core\user\User;
use core\util\utils\InventoryUtil;
use core\util\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\Player;

class ShopConfirmForm extends BaseForm{

    public const BUY = 1;
    public const SELL = 2;
    private int $type;
    private User $userManager;
    private float $cost;
    private Item $item;
    private BaseForm $form;

    public function __construct(int $type, User $userManager, float $cost, Item $item, int $count, BaseForm $form){

        switch($type){
            case self::BUY:

                $data = [
                    "type" => "form",
                    "title" => "§l§9POTWIERDZENIE KUPNA",
                    "content" => "§7Na ten zakup wydasz lacznie: §l§9".$cost."§r§7zl\n"."§r§7Zostanie ci na koncie: §l§9".abs($userManager->getPlayerMoney() - $cost)."§r§7zl",
                    "buttons" => []
                ];

                $data["buttons"][] = ["text" => "§8§l» §9Kupuje §8§l«§r\n§8Kliknij aby kupic"];
                $data["buttons"][] = ["text" => "§8§l» §9Anuluj §8§l«§r\n§8Kliknij aby anulowac", "image" => ["type" => "path", "data" => "textures/blocks/barrier"]];
                break;

            case self::SELL:

                $data = [
                    "type" => "form",
                    "title" => "§l§9POTWIERDZENIE SPRZEDAZY",
                    "content" => "§7Po sprzedazy zarobisz: §l§9".$cost."§r§7zl\n"."§r§7Na koncie bedziesz posiadal: §l§9".abs($userManager->getPlayerMoney() + $cost)."§r§7zl",
                    "buttons" => []
                ];

                $data["buttons"][] = ["text" => "§8§l» §9Sprzedaje §8§l«§r\n§8Kliknij aby sprzedac"];
                $data["buttons"][] = ["text" => "§8§l» §9Anuluj §8§l«§r\n§8Kliknij aby anulowac", "image" => ["type" => "path", "data" => "textures/blocks/barrier"]];

                break;

            default:

                $data = [
                    "type" => "form",
                    "title" => "§l§9ERROR",
                    "content" => "",
                ];

                $data["buttons"][] = ["text" => "§8§l» §9Cofnij §8§l«§r\n§8Kliknij aby cofnac", "image" => ["type" => "path", "data" => "textures/blocks/barrier"]];

                break;
        }

        $item->setCount($count);

        $this->type = $type;
        $this->userManager = $userManager;
        $this->cost = $cost;
        $this->item = $item;
        $this->data = $data;
        $this->form = $form;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        if(!ServerManager::isSettingEnabled(ServerManager::SHOP)) {
            $player->sendForm(new Error($player, "Sklep jest aktualnie wylaczony!", $this));
            return;
        }

        switch($data){
            case 0:

                if($this->type === self::BUY) {
                    if($this->userManager->getPlayerMoney() < $this->cost) {
                        $player->sendForm(new Error($player, "Nie masz wystarczajaco duzo pieniedzy aby kupic ten przedmiot, brakuje ci §l§9" . abs($this->cost - $this->userManager->getPlayerMoney()) . "§7zl", $this));
                        return;
                    }
                    $this->userManager->reducePlayerMoney($this->cost);

                    InventoryUtil::addItem($this->item, $player);
                    LogManager::sendLog($player, "BuyItem: ".$this->item->getId().":".$this->item->getDamage().":".$this->item->getCount()." [".$this->cost."zl]", LogManager::SHOP);

                    $player->sendMessage(MessageUtil::format("Poprawnie zakupiles przedmiot!"));
                }

                if($this->type === self::SELL){
                    $inv = $player->getInventory();
                    if(!$inv->contains($this->item)) {
                        $player->sendForm(new Error($player, "Nie masz wystarczajaco duzej ilosci tego przedmiotu aby go sprzedac!", $this));
                        return;
                    }

                    $inv->removeItem($this->item);
                    $this->userManager->addPlayerMoney($this->cost);
                    LogManager::sendLog($player, "SellItem: ".$this->item->getId().":".$this->item->getDamage().":".$this->item->getCount()." [".$this->cost."zl]", LogManager::SHOP);

                    $player->sendMessage(MessageUtil::format("Poprawnie sprzedales przedmiot!"));
                }

                $player->sendForm($this->form);
                break;
            case "1":
                $player->sendForm($this->form);
                break;
        }
    }
}