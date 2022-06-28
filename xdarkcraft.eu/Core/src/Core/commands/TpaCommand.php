<?php

namespace Core\commands;

use pocketmine\Player;

use pocketmine\command\{
	Command, CommandSender
};

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;

use Core\Main;

class TpaCommand extends CoreCommand {
	
	public function __construct() {
		parent::__construct("tpa", "Komenda tpa");
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}

		if(empty($args)) {
		    $sender->sendMessage("Poprane uzycie: /tpa §8(§4nick§8)");
		    return;
		}

		$player = $sender->getServer()->getPlayer($args[0]);

		if($player == null) {
		    $sender->sendMessage("§8§l>§r §7Ten gracz jest §coffline§7!");
		    return;
		}
		
		if($player->getName() == $sender->getName()) {
		    $sender->sendMessage("§8§l>§r §7Nie mozesz wyslac teleportacji do siebie!");
		    return;
		}

		$nick = $sender->getName();
		$tp_nick = $player->getName();

		if(isset(Main::$tp[$tp_nick][$nick])) {
		    $sender->sendMessage("§8§l>§r §7Wyslano juz prosbe o teleportacje do tego gracza!");
		    return;
		}

        Main::$tp[$tp_nick][$nick] = time();

        $sender->sendMessage(Main::format("Wyslano prosbe o teleportacje do gracza §4".$tp_nick."§7!"));

        $player->sendMessage(Main::formatLines(["Gracz §4$nick §7wyslal do Ciebie prosbe o teleportacje!", "Uzyj §4/tpaccept§7, aby zaakceptowac", "Albo §4/tpdeny§7, aby odrzucic"]));
    }
}