<?php

namespace Core\commands;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class CaseCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("case", "Komenda case");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		$db = Main::getInstance()->getDb();

		$array = $db->query("SELECT * FROM 'case' WHERE nick = '{$sender->getName()}'")->fetchArray();

		if(empty($array)) {
			$db->query("INSERT INTO 'case' (nick) VALUES ('{$sender->getName()}')");

			$sender->sendMessage(Main::format("Informacje o otworzonych PremiumCase zostaly §cwylaczone"));
		} else {
			$db->query("DELETE FROM 'case' WHERE nick = '{$sender->getName()}'");

			$sender->sendMessage(Main::format("Informacje o otworzonych PremiumCase zostaly §4wlaczone"));
		}
	}
}
