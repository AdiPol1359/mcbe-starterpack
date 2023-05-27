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

class RenewalCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("renewal", "", false, false, ["przedluz"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(($guild = Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName())) === null) {
            $sender->sendMessage(MessageUtil::format("Nie znajdujesz sie w zadnej gildii!"));
            return;
        }

        if(($guild->getExpireTime()) < (time() + Settings::$MAX_EXPIRE_TIME) && ($guild->getExpireTime() + (3600 * 24)) < (time() + Settings::$MAX_EXPIRE_TIME)) {
            $emerald = ItemFactory::getInstance()->get(ItemIds::EMERALD, 0, Settings::$GUILD_RENEWAL_COST);

            if($sender->getInventory()->contains($emerald)) {
                $sender->getInventory()->removeItem($emerald);
                $guild->setExpireTime($guild->getExpireTime() + (3600 * 24));
                $sender->sendMessage(MessageUtil::format("Przedluzyles waznosc gildii o §e1 §7dzien"));
            } else {
                $sender->sendMessage(MessageUtil::format("Nie masz wystarczajaco duzo emeraldow aby przedluzyc waznosc koszt przedluzenia to §e".Settings::$GUILD_RENEWAL_COST."x emeraldy!"));
            }
        } else
            $sender->sendMessage(MessageUtil::format("Twoja gildia osiagnela limit waznosci!"));
    }
}