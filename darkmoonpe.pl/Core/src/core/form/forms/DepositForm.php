<?php

namespace core\form\forms;

use core\form\BaseForm;
use core\manager\managers\StatsManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use core\util\utils\InventoryUtil;
use core\util\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\Player;

class DepositForm extends BaseForm {

    public function __construct(Player $player) {

        $user = UserManager::getUser($player->getName());

        $data = [
            "type" => "form",
            "title" => "§l§9DEPOZYT",
            "content" => "",
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "Koxy §l§9".$user->getStat(StatsManager::KOXY), "image" => ["type" => "url", "data" => "https://static.wikia.nocookie.net/hypixel-skyblock/images/4/4d/Enchanted_Golden_Apple.gif/revision/latest?cb=20200619230630"]];

        $data["buttons"][] = ["text" => "Refile §l§9".$user->getStat(StatsManager::REFY), "image" => ["type" => "path", "data" => "textures/items/apple_golden"]];

        $data["buttons"][] = ["text" => "Perly §l§9".$user->getStat(StatsManager::PERLY), "image" => ["type" => "path", "data" => "textures/items/ender_pearl"]];

        $data["buttons"][] = ["text" => "§9Dopelnij do limitu"];
        $data["buttons"][] = ["text" => "§9Wyplac wszystko"];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        $user = UserManager::getUser($player->getName());

        switch($data) {
            case "0":
                if($user->getStat(StatsManager::KOXY) <= 0) {
                    $player->sendMessage(MessageUtil::format("Nie posiadasz koxow w schowku!"));
                    return;
                }

                $user->reduceStat(StatsManager::KOXY, 1);

                $player->getInventory()->addItem(Item::get(Item::ENCHANTED_GOLDEN_APPLE));
                $player->sendMessage(MessageUtil::format("Wyplaciles koxa!"));
                break;

            case "1":
                if($user->getStat(StatsManager::REFY) <= 0) {
                    $player->sendMessage(MessageUtil::format("Nie posiadasz refow w schowku!"));
                    return;
                }

                $user->reduceStat(StatsManager::REFY, 1);

                $player->getInventory()->addItem(Item::get(Item::GOLDEN_APPLE));
                $player->sendMessage(MessageUtil::format("Wyplaciles refa!"));
                break;

            case "2":
                if($user->getStat(StatsManager::PERLY) <= 0) {
                    $player->sendMessage(MessageUtil::format("Nie posiadasz perel w schowku!"));
                    return;
                }

                $user->reduceStat(StatsManager::PERLY, 1);

                $player->getInventory()->addItem(Item::get(Item::ENDER_PEARL));
                $player->sendMessage(MessageUtil::format("Wyplaciles §9§lperle§r§7!"));
                break;

            case "3":

                $koxy = 0;
                $refy = 0;
                $perly = 0;

                foreach($player->getInventory()->getContents() as $item) {
                    if($item->getId() === Item::ENCHANTED_GOLDEN_APPLE)
                        $koxy += $item->getCount();

                    if($item->getId() === Item::GOLDEN_APPLE)
                        $refy += $item->getCount();

                    if($item->getId() === Item::ENDER_PEARL)
                        $perly += $item->getCount();
                }

                if($koxy < ConfigUtil::LIMIT_KOXY)
                    if((ConfigUtil::LIMIT_KOXY - $koxy) <= $user->getStat(StatsManager::KOXY))
                        $koxy = ConfigUtil::LIMIT_KOXY - $koxy;
                    else
                        $koxy = $user->getStat(StatsManager::KOXY);
                else
                    $koxy = 0;

                if($refy < ConfigUtil::LIMIT_REFY)
                    if((ConfigUtil::LIMIT_REFY - $refy) <= $user->getStat(StatsManager::REFY))
                        $refy = ConfigUtil::LIMIT_REFY - $refy;
                    else
                        $refy = $user->getStat(StatsManager::REFY);
                else
                    $refy = 0;

                if($perly < ConfigUtil::LIMIT_PERLY)
                    if((ConfigUtil::LIMIT_PERLY - $perly) <= $user->getStat(StatsManager::PERLY))
                        $perly = ConfigUtil::LIMIT_PERLY - $perly;
                    else
                        $perly = $user->getStat(StatsManager::PERLY);
                else
                    $perly = 0;

                $user->reduceStat(StatsManager::KOXY, $koxy);
                $user->reduceStat(StatsManager::REFY, $refy);
                $user->reduceStat(StatsManager::PERLY, $perly);

                $player->getInventory()->addItem(Item::get(466, 0, $koxy));
                $player->getInventory()->addItem(Item::get(322, 0, $refy));
                $player->getInventory()->addItem(Item::get(368, 0, $perly));

                $player->sendMessage(MessageUtil::format("Wyplacono §9$koxy §7koxow, §9$refy §7refow i §9$perly §7perel!"));
                break;

            case "4":
                $koxy = $user->getStat(StatsManager::KOXY);
                $refy = $user->getStat(StatsManager::REFY);
                $perly = $user->getStat(StatsManager::PERLY);

                $user->reduceStat(StatsManager::KOXY, $koxy);
                $user->reduceStat(StatsManager::REFY, $refy);
                $user->reduceStat(StatsManager::PERLY, $perly);

                InventoryUtil::addItem(Item::get(466, 0, $koxy), $player);
                InventoryUtil::addItem(Item::get(466, 0, $refy), $player);
                InventoryUtil::addItem(Item::get(466, 0, $perly), $player);

                $player->sendMessage(MessageUtil::format("Wyplacono §9$koxy §7koxow, §9$refy §7refow i §9$perly §7perel!"));
                break;
        }

        $player->sendForm(new DepositForm($player));
    }
}