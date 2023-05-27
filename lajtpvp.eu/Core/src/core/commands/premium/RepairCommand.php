<?php

declare(strict_types=1);

namespace core\commands\premium;

use core\commands\BaseCommand;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class RepairCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("repair", "", true, false, [], [
            0 => [
                $this->commandParameter("repairOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "repairOptions", ["all"])
            ]
        ]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(empty($args) || !isset($args[0])){
            $item = $sender->getInventory()->getItemInHand();

            if($this->repairable($item))
                $item->setDamage(0);

            $sender->getInventory()->setItemInHand($item);

            $sender->sendMessage(MessageUtil::format("Naprawiono trzymany item!"));
            return;
        }

        if($args[0] === "all") {
            if(!PermissionUtil::has($sender, Settings::$PERMISSION_TAG."repair.all")) {
                $this->permissionMessage(Settings::$PERMISSION_TAG . "repair.all");
                return;
            }

            foreach($sender->getInventory()->getContents(false) as $slot => $invItem) {
                if(!$this->repairable($invItem))
                    continue;

                $invItem->setDamage(0);
                $sender->getInventory()->setItem($slot, $invItem);
            }

            foreach($sender->getArmorInventory()->getContents(false) as $slot => $invItem) {
                if(!$this->repairable($invItem))
                    continue;

                $invItem->setDamage(0);
                $sender->getArmorInventory()->setItem($slot, $invItem);
            }

            $sender->sendMessage(MessageUtil::format("Naprawiono wszystkie itemy w twoim ekwipunku!"));
        }
    }

    private function repairable(Item $item) : bool {
        return $item instanceof Tool || $item instanceof Armor;
    }
}