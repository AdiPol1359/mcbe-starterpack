<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\guild\ItemsInventory;
use core\Main;
use core\managers\ServerManager;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ItemsCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("items", "", false, false, ["itemy"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(!Main::getInstance()->getServerManager()->isSettingEnabled(ServerManager::GUILD_ITEMS) && !$sender->hasPermission(Settings::$PERMISSION_TAG."command.server")) {
            $sender->sendMessage(MessageUtil::format("Itemy na gildie sa wylaczone!"));
            return;
        }

        (new ItemsInventory($sender))->openFor([$sender]);
    }
}