<?php

namespace Core\commands;

use pocketmine\Player;

use pocketmine\command\{
	Command, CommandSender
};

use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

use pocketmine\level\Position;

use Core\Main;

use Core\task\TpTask;

class TpacceptCommand extends CoreCommand {
	
	public function __construct() {
		parent::__construct("tpaccept", "Komenda tpaccept");
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender))
		    return;

		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}

	$nick = $sender->getName();

	if(empty($args)) {
	    if(empty(Main::$tp[$nick])) {
	        $sender->sendMessage("§8§l>§r §7Nikt nie wyslal do Ciebie prosby o teleportacje!");
	        return;
	    }

	    if(count(Main::$tp[$nick]) == 1) {
	        $player = $sender->getServer()->getPlayer(key(Main::$tp[$nick]));

	        $this->teleportProccess($sender, $player);
	    } else {
	        $sender->sendMessage(Main::format("Twoje prosby o teleportacje: "));

	        $requests = [];

	        foreach(Main::$tp[$nick] as $p => $time)
	            $requests[] = $p;

	        $sender->sendMessage(Main::format(implode("§7, §4", $requests)));
	    }
	    return;
	}

	if($args[0] == "*") {
	    foreach(Main::$tp[$nick] as $player => $time) {
	        $player = $sender->getServer()->getPlayer($player);

	        $this->teleportProccess($sender, $player);
	    }
	} else {
	    $player = $sender->getServer()->getPlayer($args[0]);

	    if($player == null || !isset(Main::$tp[$nick][$player->getName()])) {
	        $sender->sendMessage("§8§l>§r §7Ten gracz nie wyslal do Ciebie porsby o teleportacje!");
	        return;
	    }

	    $this->teleportProccess($sender, $player);
	    }
	}

	private function teleportProccess(Player $player, Player $tp_player) {
	    $nick = $player->getName();
	    $tp_nick = $tp_player->getName();

	    if(time() - Main::$tp[$nick][$tp_nick] > 15) {
	        $player->sendMessage("§8§l>§r §7Prosba o teleportacje wygasla!");
	        unset(Main::$tp[$nick][$tp_nick]);
	        return;
	    }

	    unset(Main::$tp[$nick][$tp_nick]);

	    $player->sendMessage(Main::format("Pomyslnie zaakceptowano prosbe o teleportacje gracza §4{$tp_nick}§7!"));
	    $tp_player->sendMessage(Main::format("Gracz §4$nick §7zaakceptowal twoja porsbe o teleportacje!"));

	    $time = Main::getInstance()->getTeleportTime($tp_player);
	    $tp_player->addEffect(new EffectInstance(Effect::getEffect(9), 20*$time, 3));

        if(isset(Main::$tpTask[$tp_nick]))
            Main::$tpTask[$tp_nick]->cancel();

	    Main::$tpTask[$tp_nick] = Main::getInstance()->getScheduler()->scheduleDelayedTask(new TpTask($tp_player, Position::fromObject($player, $player->getLevel())), 20*$time);
	}
}