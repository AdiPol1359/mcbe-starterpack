<?php

namespace Core\commands;

use pocketmine\Player;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

use Core\inventory\{
	PreprocessEnderchestInventory, EnderchestInventory
};

class EcCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("ec", "Komenda ec", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}
		$size = EnderchestInventory::SIZE_SMALL;

		if($sender->hasPermission("PolishHard.ec.large"))
            $size = EnderchestInventory::SIZE_LARGE;

		new PreprocessEnderchestInventory($sender, null, $size);
	}
}