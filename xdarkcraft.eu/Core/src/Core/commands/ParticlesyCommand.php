<?php

namespace Core\commands;

use Core\api\ParticlesyAPI;
use pocketmine\Player;

use pocketmine\command\CommandSender;

use Core\Main;

use Core\form\ParticlesyForm;

class ParticlesyCommand extends CoreCommand {
	
	public function __construct() {
		parent::__construct("particlesy", "Komenda particlesy", true);
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}

		$sender->sendForm(new ParticlesyForm($sender));
	}
}