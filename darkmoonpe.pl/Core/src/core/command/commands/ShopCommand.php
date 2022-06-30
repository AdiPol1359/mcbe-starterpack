<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\form\forms\shop\normal\ShopForm;
use core\Main;
use core\manager\managers\ServerManager;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

class ShopCommand extends BaseCommand {
    public function __construct() {
        parent::__construct("shop", "Shop Command", false, false, "Komenda sklep sluzy do otwierania menu sklepu", ["sklep"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(!ServerManager::isSettingEnabled(ServerManager::SHOP)) {
            $player->sendMessage(MessageUtil::format("Sklep jest aktualnie wylaczony!"));
            return;
        }

        $player->sendForm(new ShopForm(Main::getShopConfig()->get("defaultForm")));
    }
}