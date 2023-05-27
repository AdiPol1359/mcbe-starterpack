<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class PluginsCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("plugins", "", true, true, ["pl", "pluginy"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        $list = array_map(function(Plugin $plugin) : string{
            return ($plugin->isEnabled() ? "§a" : "§c") . $plugin->getDescription()->getFullName();
        }, $sender->getServer()->getPluginManager()->getPlugins());
        sort($list, SORT_STRING);

        $sender->sendMessage(MessageUtil::format("Pluginy na serwerze: ". implode("§7, ", $list)));
    }
}