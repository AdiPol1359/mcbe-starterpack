<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\entities\custom\VillagerShopEntity;
use core\Main;
use core\utils\Settings;
use core\utils\MessageUtil;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class VillagerShopCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("villagershop", "", true, false, ["shop", "sklep", "vs", "vshop"]);

        $parameters = [
            0 => [
                $this->commandParameter("idVillagerShop", AvailableCommandsPacket::ARG_TYPE_STRING, false, "idVillagerShop", ["id"])
            ],

            1 => [
                $this->commandParameter("createVillagerShop", AvailableCommandsPacket::ARG_TYPE_STRING, false, "createVillagerShop", ["create"]),
                $this->commandParameter("createVillagerShopName", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ],

            2 => [
                $this->commandParameter("removeVillagerShop", AvailableCommandsPacket::ARG_TYPE_STRING, false, "removeVillagerShop", ["remove"]),
                $this->commandParameter("removeVillagerShopId", AvailableCommandsPacket::ARG_TYPE_INT, false)
            ],

            3 => [
                $this->commandParameter("itemVillagerShop", AvailableCommandsPacket::ARG_TYPE_STRING, false, "itemVillagerShop", ["item"]),
                $this->commandParameter("itemVillagerShopAdd", AvailableCommandsPacket::ARG_TYPE_STRING, false, "itemVillagerShop", ["add"]),
                $this->commandParameter("itemVillagerShopId", AvailableCommandsPacket::ARG_TYPE_INT, false),
                $this->commandParameter("itemVillagerShopCost", AvailableCommandsPacket::ARG_TYPE_INT, false),
            ],

            4 => [
                $this->commandParameter("itemVillagerShop", AvailableCommandsPacket::ARG_TYPE_STRING, false, "itemVillagerShop", ["item"]),
                $this->commandParameter("itemVillagerShopRemove", AvailableCommandsPacket::ARG_TYPE_STRING, false, "itemVillagerShop", ["remove"]),
                $this->commandParameter("itemVillagerShopId", AvailableCommandsPacket::ARG_TYPE_INT, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(empty($args)) {
            $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Pokazuje id villagera" => ["id"], "Tworzy nowego villagera" => ["create", "§8(§enazwa§8)"], "Usuwa villagera o podanym id" => ["remove", "§8(§eid§8)"], "Dodaje item do villagera" => ["item", "add", "§8(§eid§8)", "§8(§ecena§8)"], "Usuwa item z villagera" => ["item", "§eremove§", "§8(§eid§8)", "§8(§eslot§8)"]]));
            return;
        }

        switch($args[0]) {

            case "id":
                $user = Main::getInstance()->getUserManager()->getUser($sender->getName());

                if($user->hasLastData(Settings::$CHOOSE_SHOP_VILLAGER)) {
                    $sender->sendMessage(MessageUtil::format("Juz wybierasz id villagera!"));
                    return;
                }

                $user->setLastData(Settings::$CHOOSE_SHOP_VILLAGER, (time() + Settings::$CHOOSE_SHOP_VILLAGER_TIME), Settings::$TIME_TYPE);
                $sender->sendMessage(MessageUtil::format("Uderz villagera aby sprawdzic jego id masz na to §e".Settings::$CHOOSE_SHOP_VILLAGER_TIME." §7sekund!"));
                break;

            case "create":
                if(!isset($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Nie podales nazwy!"));
                    return;
                }

                $position = Position::fromObject($sender->getPosition()->round(1), $sender->getWorld());

                Main::getInstance()->getVillagerShopManager()->createShop($id = Main::getInstance()->getVillagerShopManager()->getHighestId(), $args[1], $position);
                Main::getInstance()->getVillagerShopManager()->spawnVillager($id, $position);

                $sender->sendMessage(MessageUtil::format("Stworzono sklep!"));
                break;

            case "remove":
                if(!isset($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Nie podales id!"));
                    return;
                }

                if(($villager = Main::getInstance()->getVillagerShopManager()->getVillager((int)$args[1])) === null) {
                    $sender->sendMessage(MessageUtil::format("Nie znaleziono villagera o podanym id!"));
                    return;
                }

                foreach($sender->getWorld()->getEntities() as $entity) {
                    if($entity instanceof VillagerShopEntity) {
                        if(($vShop = $entity->getVillager()) !== null) {
                            if($vShop->getId() === $villager->getId())
                                $entity->close();
                        } else
                            $entity->close();
                    }
                }

                Main::getInstance()->getVillagerShopManager()->removeVillager($villager->getId());
                $sender->sendMessage(MessageUtil::format("Usunieto villagera!"));
                break;

            case "item":
                if(!isset($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                    return;
                }

                switch($args[1]) {
                    case "add":

                        if(!is_numeric((int)$args[2])) {
                            $sender->sendMessage(MessageUtil::format("Id musi byc numerczyne!"));
                            return;
                        }

                        if(($villager = Main::getInstance()->getVillagerShopManager()->getVillager((int)$args[2])) === null) {
                            $sender->sendMessage(MessageUtil::format("Nie znaleziono villagera o podanym id"));
                            return;
                        }

                        if(!isset($args[3])) {
                            $sender->sendMessage(MessageUtil::format("Nie podales ceny!"));
                            return;
                        }

                        if(!is_numeric((int)$args[3])) {
                            $sender->sendMessage(MessageUtil::format("Wartosc musi byc numeryczna bez liczb po przecinku!"));
                            return;
                        }

                        if(($slot = $villager->getEmptySlot()) === null) {
                            $sender->sendMessage(MessageUtil::format("Nie ma juz miejsca w tym villagerze na wiecej itemow!"));
                            return;
                        }

                        $item = $sender->getInventory()->getItemInHand();

                        if($item->getId() === ItemIds::AIR) {
                            $sender->sendMessage(MessageUtil::format("Musisz trzymac jakis itemy aby dodac go do sklepu!"));
                            return;
                        }

                        $villager->addItem($slot, (int)round($args[3]), $item);
                        $sender->sendMessage(MessageUtil::format("Poprawnie dodano item do villagera!"));
                        break;

                    case "remove":

                        if(!is_numeric($args[2])) {
                            $sender->sendMessage(MessageUtil::format("Id musi byc numerczyne!"));
                            return;
                        }

                        if(($villager = Main::getInstance()->getVillagerShopManager()->getVillager((int)$args[2])) === null) {
                            $sender->sendMessage(MessageUtil::format("Nie znaleziono villagera o podanym id"));
                            return;
                        }

                        if(!isset($args[3])) {
                            $sender->sendMessage(MessageUtil::format("Nie podales slota!"));
                            return;
                        }

                        $slot = $args[3];

                        if(!is_numeric($slot)) {
                            $sender->sendMessage(MessageUtil::format("Wartosc musi byc numeryczna bez liczb po przecinku!"));
                            return;
                        }

                        $villager->removeItem(($slot - 1));
                        $sender->sendMessage(MessageUtil::format("Usunieto item ze slota §e".$slot));
                        break;

                    default:
                        $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                        break;
                }
                break;

            default:
                $sender->sendMessage(MessageUtil::format("Niezanany argument!"));
                break;
        }
    }

    #[Pure] public static function getYaw(int $yaw) : float{
        $closest = 360;

        foreach ([45, 90, 135, 180, 225, 270, 315, 360] as $item) {
            if ($closest === null || abs($yaw - $closest) > abs($item - $yaw))
                $closest = $item;
        }

        return $closest;
    }
}