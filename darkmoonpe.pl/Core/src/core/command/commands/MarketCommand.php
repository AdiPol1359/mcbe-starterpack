<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\fakeinventory\inventory\market\OffersInventory;
use core\fakeinventory\inventory\market\PlayerOffersInventory;
use core\manager\managers\market\MarketManager;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class MarketCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("market", "Market Command", false, true, "Komenda sluzy do otwierania lub zaarzadzania oferami na rynku", ["olx", "allegro", "rynek", "bazar"]);

        $parameters = [
            0 => [
                $this->commandParameter("marketOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "marketOptions", ["wystaw"]),
                $this->commandParameter("cena", AvailableCommandsPacket::ARG_TYPE_FLOAT, false)
            ],

            1 => [
                $this->commandParameter("marketOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "marketOption", ["help"]),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        if(empty($args)) {
            (new OffersInventory($player))->openFor([$player]);
            return;
        }

        switch($args[0]) {
            case "oferty":
                (new PlayerOffersInventory($player, isset($args[1]) ? $args[1] : $player->getName()))->openFor([$player]);
                break;

            case "wystaw":
                if(!isset($args[1])) {
                    $player->sendMessage($this->correctUse($this->getCommandLabel(), [["wystaw"], ["cena"]]));
                    return;
                }

                if(!is_numeric($args[1])) {
                    $player->sendMessage(MessageUtil::format("Podana cena nie jest liczba!"));
                    return;
                }

                $money = (float) number_format($args[1], 2, '.', '');

                if($money <= 0){
                    $player->sendMessage(MessageUtil::format("Cena musi byc wieksza od §l§90§r§7!"));
                    return;
                }

                $item = $player->getInventory()->getItemInHand();

                if($item->getId() === Item::AIR) {
                    $player->sendMessage(MessageUtil::format("Musisz trzymac przedmiot w reku aby go dodac do rynku!"));
                    return;
                }

                if(count(MarketManager::getPlayerOffers($player->getName())) >= ($limit = MarketManager::getMaxPlayerOfferCount($player)) && !$player->isOp()){
                    $player->sendMessage(MessageUtil::format("Osiagnales limit wystawionych ofert na rynku, twoj limit wynosi: §l§9".$limit));
                    return;
                }

                MarketManager::createOffer($player->getName(), $money, $item);
                $player->getInventory()->setItemInHand(Item::get(Item::AIR));
                $player->sendMessage(MessageUtil::format("Dodano item na rynek!"));
                break;

            default:
                $player->sendMessage(MessageUtil::formatLines([
                    "§l§9/rynek §r§8-§7 Otwiera rynek",
                    "§l§9/rynek oferty §r§8-§7 Otwiera rynek z twoimi ofertami",
                    "§l§9/rynek wystaw [cena] §r§8-§7 Wystawia przedmiot na rynek"
                ]));

        }
    }
}