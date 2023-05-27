<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use core\utils\Settings;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;

class BlocksCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("blocks", "", false, false, ["bloki"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $itemFactory = ItemFactory::getInstance();

        $items = [];

        foreach($sender->getInventory()->getContents(false) as $slot => $item) {
            foreach(Settings::$BLOCKS as $itemId => $blockId) {
                if($item->equals($itemFactory->get($itemId)))
                    isset($items[$item->getName()]) ? $items[$item->getName()]->setCount($items[$item->getName()]->getCount() + $item->getCount()) : $items[$item->getName()] = $item;
            }
        }

        foreach($items as $itemName => $itemData) {
            $removeItemData = 0;

            for($i = $itemData->getCount(); $i >= 9; $i -= 9)
                $removeItemData += 9;

            $itemData->setCount($removeItemData);
            $sender->getInventory()->removeItem($itemData);
            $sender->getInventory()->addItem($itemFactory->get(Settings::$BLOCKS[$itemData->getId()], 0, (int)round($removeItemData / 9)));
        }

        $sender->sendMessage(MessageUtil::format("Przemieniles mineraly na bloki"));
    }
}