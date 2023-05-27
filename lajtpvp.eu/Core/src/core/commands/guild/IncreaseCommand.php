<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\Main;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class IncreaseCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("increase", "", false, false, ["powieksz"]);
    }
    
    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(($guild = Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName())) === null) {
            $sender->sendMessage(MessageUtil::format("Nie znajdujesz sie w zadnej gildii!"));
            return;
        }

        if($guild->getSize() < Settings::$MAX_GUILD_SIZE) {
            $emerald = ItemFactory::getInstance()->get(ItemIds::EMERALD, 0, Settings::$GUILD_INCREASE_COST);

            if($sender->getInventory()->contains($emerald)) {
                $sender->getInventory()->removeItem($emerald);
                $guild->setSize($guild->getSize() + Settings::$GUILD_TERRAIN_UPGRADE);
                $sender->sendMessage(MessageUtil::format("Powiekszyles teren gildii o §e".Settings::$GUILD_TERRAIN_UPGRADE." §7kratek"));
            } else {
                $sender->sendMessage(MessageUtil::format("Nie masz wystarczajaco duzo emeraldow aby powiekszyc teren!"));
            }
        } else
            $sender->sendMessage(MessageUtil::format("Twoja gildia osiagnela limit wielkosci!"));
    }
}