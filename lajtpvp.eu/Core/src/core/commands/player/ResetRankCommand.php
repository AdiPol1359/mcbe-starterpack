<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\Main;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class ResetRankCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("resetrank", "", false, false, ["resetujranking", "resetrank"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($sender->getName());

        if(!$user)
            return;

        $item = ItemFactory::getInstance()->get(ItemIds::EMERALD_BLOCK, 0, Settings::$RESET_RANK_COST);

        if(!$sender->getInventory()->contains($item)) {
            $sender->sendMessage(MessageUtil::format("Potrzebujesz ".Settings::$RESET_RANK_COST."x bloki emeraldow aby zresetowan ranking"));
            return;
        }

        $sender->getInventory()->removeItem($item);

        $user->getStatManager()->setStat(Settings::$STAT_POINTS, Settings::$STAT_DEFAULT_POINTS);

        $sender->sendMessage(MessageUtil::format("Zresetowales ranking!"));
    }
}