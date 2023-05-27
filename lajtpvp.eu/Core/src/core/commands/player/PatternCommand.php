<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\PatternInventory;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class PatternCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("pattern", "", false, false);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $item = $sender->getInventory()->getItemInHand();

        if(!Main::getInstance()->getSafeManager()->isSafe($item)) {
            $sender->sendMessage(MessageUtil::format("Trzymany item musi byc sejfem!"));
            return;
        }

        $safe = Main::getInstance()->getSafeManager()->getSafeById($item->getNamedTag()->getInt("safeId"));

        if($safe->getName() !== $sender->getName()) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz zmienic paternu zablokowanego sejfa!"));
            return;
        }

        (new PatternInventory())->openFor([$sender]);
    }
}