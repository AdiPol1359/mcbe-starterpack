<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\market\OffersInventory;
use core\inventories\fakeinventories\market\PlayerOffersInventory;
use core\Main;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class MarketCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("market", "", false, true, ["olx", "allegro", "rynek", "bazar"]);

        $parameters = [
            0 => [
                $this->commandParameter("marketOptionList", AvailableCommandsPacket::ARG_TYPE_STRING, false, "marketList", ["wystaw"]),
                $this->commandParameter("cena", AvailableCommandsPacket::ARG_TYPE_FLOAT, false)
            ],

            1 => [
                $this->commandParameter("marketOptionHelp", AvailableCommandsPacket::ARG_TYPE_STRING, false, "marketHelp", ["help"]),
            ],

            2 => [
                $this->commandParameter("marketOptionOffers", AvailableCommandsPacket::ARG_TYPE_STRING, false, "marketOffers", ["oferty"]),
            ],
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(empty($args)) {
            (new OffersInventory($sender))->openFor([$sender]);
            return;
        }

        switch($args[0]) {
            case "oferty":
                (new PlayerOffersInventory($args[1] ?? $sender->getName()))->openFor([$sender]);
                break;

            case "wystaw":
                if(!isset($args[1])) {
                    $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["wystaw"], ["cena"]]));
                    return;
                }

                if(!is_numeric($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Podana cena nie jest liczba!"));
                    return;
                }

                $money = round((int)$args[1]);

                if($money <= 0){
                    $sender->sendMessage(MessageUtil::format("Cena musi byc wieksza niz §e0§r§7!"));
                    return;
                }

                if($money > Settings::MAX_ITEM_COST) {
                    $sender->sendMessage(MessageUtil::format("Maksymalna ilosc zlota za jaka mozna wystawic przedmiot to §e".Settings::MAX_ITEM_COST));
                    return;
                }

                $item = $sender->getInventory()->getItemInHand();

                if($item->getId() === ItemIds::AIR) {
                    $sender->sendMessage(MessageUtil::format("Musisz trzymac przedmiot w reku aby go dodac do rynku!"));
                    return;
                }

                if(count(Main::getInstance()->getMarketManager()->getPlayerOffers($sender->getName())) >= ($limit = Main::getInstance()->getMarketManager()->getMaxPlayerOfferCount($sender)) && !$sender->getServer()->isOp($sender->getName())){
                    $sender->sendMessage(MessageUtil::format("Osiagnales limit wystawionych ofert na rynku, twoj limit wynosi: §e".$limit));
                    return;
                }

                Main::getInstance()->getMarketManager()->createOffer($sender->getName(), $money, $item);
                $sender->getInventory()->setItemInHand(ItemFactory::air());
                $sender->sendMessage(MessageUtil::format("Dodano item na rynek!"));
                break;

            default:
                $sender->sendMessage(MessageUtil::formatLines([
                    "§e/".$this->getCommandLabel()." §r§8-§7 Otwiera rynek",
                    "§e/".$this->getCommandLabel()." oferty §r§8-§7 Otwiera rynek z twoimi ofertami",
                    "§e/".$this->getCommandLabel()." wystaw [cena] §r§8-§7 Wystawia przedmiot na rynek"
                ]));

        }
    }
}