<?php

namespace Core\commands;

use Core\form\ShopForm;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\{
	Command, CommandSender
};
use Core\Main;

class SklepCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("sklep", "Komenda sklep");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

        if(!$sender instanceof Player) {
            $sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
            return;
        }

        $shopCfg = Main::getInstance()->getShopConfig();

        $sender->sendForm(new ShopForm($shopCfg->get("defaultForm")));
	}
}