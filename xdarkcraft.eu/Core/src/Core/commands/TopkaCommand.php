<?php

namespace Core\commands;

use pocketmine\Player;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

use Core\form\TopMenuForm;

class TopkaCommand extends CoreCommand {
	
	public function __construct() {
		parent::__construct("topka", "Komenda topka");
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender))
		    return;

		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}
		
		$sender->sendForm(new TopMenuForm());
	}
}