<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender, ConsoleCommandSender
};

use Core\Main;

class DajCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("daj", "Komenda daj", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender)) return;

		if(empty($args)) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /daj §8(§4nick§8) §8(§4usluga§8)"));
			return;
		}
		
		if(isset($args[1])){
		 if($args[1] == "vip"){
		 $sender->getServer()->dispatchCommand(new ConsoleCommandSender, "pex user $args[0] group add vip 30d");
		 	$sender->getServer()->broadcastMessage(Main::formatLines(["Gracz §4$args[0] §7zakupil range §4VIP§7!", "Koszt to §44§7.§492§7zl VAT", "Nasza strona WWW: §4www.PolishHard.EU", "Dziekujemy za wsparcie!"]));
		 	return;
		 }
		 if($args[1] == "svip"){
		 $sender->getServer()->dispatchCommand(new ConsoleCommandSender, "pex user $args[0] group add svip 30d");
		 	$sender->getServer()->broadcastMessage(Main::formatLines(["Gracz §4$args[0] §7zakupil range §4SVIP§7!", "Koszt to §47§7.§438§7zl VAT", "Nasza strona WWW: §4www.PolishHard.EU", "Dziekujemy za wsparcie!"]));
		 	return;
		 }
		 if($args[1] == "sponsor"){
		 $sender->getServer()->dispatchCommand(new ConsoleCommandSender, "pex user $args[0] group add sponsor 30d");
		 	$sender->getServer()->broadcastMessage(Main::formatLines(["Gracz §4$args[0] §7zakupil range §4SPONSOR§7!", "Koszt to §420§7.§491§7zl VAT", "Nasza strona WWW: §4www.PolishHard.EU", "Dziekujemy za wsparcie!"]));
		 	return;
		 }
		 if($args[1] == "pc16"){
		 $sender->getServer()->dispatchCommand(new ConsoleCommandSender, "pcase 16 $args[0]");
		 	$sender->getServer()->broadcastMessage(Main::formatLines(["Gracz §4$args[0] §7zakupil §4PREMIUMCASE §8(§fx16§8)§7!", "Koszt to §42§7.§446§7zl VAT", "Nasza strona WWW: §4www.PolishHard.EU", "Dziekujemy za wsparcie!"]));
		 	return;
		 }
		 if($args[1] == "pc32"){
		 $sender->getServer()->dispatchCommand(new ConsoleCommandSender, "pcase 32 $args[0]");
		 	$sender->getServer()->broadcastMessage(Main::formatLines(["Gracz §4$args[0] §7zakupil §4PREMIUMCASE §8(§fx32§8)§7!", "Koszt to §44§7.§492§7zl VAT", "Nasza strona WWW: §4www.PolishHard.EU", "Dziekujemy za wsparcie!"]));
		 	return;
		 }
		 if($args[1] == "pc64"){
		 $sender->getServer()->dispatchCommand(new ConsoleCommandSender, "pcase 64 $args[0]");
		 	$sender->getServer()->broadcastMessage(Main::formatLines(["Gracz §4$args[0] §7zakupil §4PREMIUMCASE §8(§fx64§8)§7!", "Koszt to §47§7.§438§7zl VAT", "Nasza strona WWW: §4www.PolishHard.EU", "Dziekujemy za wsparcie!"]));
		 	return;
		 }
		 if($args[1] == "pc128"){
		 $sender->getServer()->dispatchCommand(new ConsoleCommandSender, "pcase 128 $args[0]");
		 	$sender->getServer()->broadcastMessage(Main::formatLines(["Gracz §4$args[0] §7zakupil §4PREMIUMCASE §8(§fx128§8)§7!", "Koszt to §412§7.§430§7zl VAT", "Nasza strona WWW: §4www.PolishHard.EU", "Dziekujemy za wsparcie!"]));
		 	return;
		 }
		 if($args[1] == "pc256"){
		     $sender->getServer()->dispatchCommand(new ConsoleCommandSender, "pcase 256 $args[0]");
		     $sender->getServer()->broadcastMessage(Main::formatLines(["Gracz §4$args[0] §7zakupil §4PREMIUMCASE §8(§fx256§8)§7!", "Koszt to §420§7.§491§7zl VAT", "Nasza strona WWW: §4www.PolishHard.EU", "Dziekujemy za wsparcie!"]));
		     return;
		 }
		 if($args[1] == "pc512"){
		     $sender->getServer()->dispatchCommand(new ConsoleCommandSender, "pcase 512 $args[0]");
		     $sender->getServer()->broadcastMessage(Main::formatLines(["Gracz §4$args[0] §7zakupil §4PREMIUMCASE §8(§fx512§8)§7!", "Koszt to §430§7.§475§7zl VAT", "Nasza strona WWW: §4www.PolishHard.EU", "Dziekujemy za wsparcie!"]));
		     return;
		 }
		 if($args[1] == "particlesy"){
		     $sender->getServer()->dispatchCommand(new ConsoleCommandSender, "pex user $args[0] group add particlesy 30d");
		     $sender->getServer()->broadcastMessage(Main::formatLines(["Gracz §4$args[0] §7zakupil §4PARTICLESY§7!", "Koszt to §47§7.§438§7zl VAT", "Nasza strona WWW: §4www.PolishHard.EU", "Dziekujemy za wsparcie!"]));
		     return;
		 }
		 if($args[1] == "unban"){
		 $sender->getServer()->dispatchCommand(new ConsoleCommandSender, "unban $args[0]");
		 	$sender->getServer()->broadcastMessage(Main::formatLines(["Gracz §4$args[0] §7zakupil §4UNBANA NA NICK§7!", "Koszt to §412§7.§430§7zl VAT", "Nasza strona WWW: §4www.PolishHard.EU", "Dziekujemy za wsparcie!"]));
		 	return;
		 }

		}
		else{
						$sender->sendMessage(Main::format("Poprawne uzycie: /daj §8(§4nick§8) §8(§4usluga§8)"));
						return;
		}
		
	}
}