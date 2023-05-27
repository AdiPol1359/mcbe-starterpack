<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\kit\KitInventory;
use core\Main;
use core\managers\ServerManager;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class KitCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("kit", "", false, false, ["kity", "kits"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(!Main::getInstance()->getServerManager()->isSettingEnabled(ServerManager::KIT) && !PermissionUtil::has($sender, Settings::$PERMISSION_TAG."command.server")) {
            $sender->sendMessage(MessageUtil::format("Kity sa wylaczone!"));
            return;
        }

        (new KitInventory($sender))->openFor([$sender]);
    }
}