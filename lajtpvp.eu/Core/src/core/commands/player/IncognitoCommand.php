<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\IncognitoInventory;
use core\Main;
use core\utils\MessageUtil;
use core\utils\Settings;
use core\utils\TimeUtil;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class IncognitoCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("incognito", "", false, false, ["inco"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($sender->getName());

        if(!$user) {
            return;
        }

        $statManager = $user->getStatManager();

        $timePlayed = (time() - ($sender->getServer()->getPlayerExact($user->getName()) ? $statManager->getStat(Settings::$STAT_LAST_JOIN_TIME) : 0));

        if($timePlayed <= Settings::$INCOGNITO_BLOCK && !$sender->getServer()->isOp($sender->getName())) {
            $sender->sendMessage(MessageUtil::format("Zarzadzac incognito mozna dopiero po spedzonych §e2 §7godzinach na serwerze, musisz jeszcze odczekac " . TimeUtil::convertIntToStringTime((Settings::$INCOGNITO_BLOCK - $timePlayed), "§e", "§7")));
            return;
        }

        (new IncognitoInventory($sender))->openFor([$sender]);
    }
}