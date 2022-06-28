<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class PomocCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("pomoc", "Komenda pomoc");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

	  $sender->sendMessage(" \n§8          §4§lPolishHard§7.EU\n§r");
	  $sender->sendMessage("§8§l>§r §4/vip §8- §7Informacje o randze VIP");
	  $sender->sendMessage("§8§l>§r §4/svip §8- §7Informacje o randze SVIP");
	  $sender->sendMessage("§8§l>§r §4/yt §8- §7Informacje o randze YT");
	  $sender->sendMessage("§8§l>§r §4/yt+ §8- §7Informacje o randze YT+");
	  $sender->sendMessage("§8§l>§r §4/g §8- §7Pomoc dla gildii");
	  $sender->sendMessage("§8§l>§r §4/drop §8- §7Informacje na temat dropu");$sender->sendMessage("§8§l>§r §4/depozyt §8- §7Pozwala sprawdzic stan schowka");
      $sender->sendMessage("§8§l>§r §4/spawn §8- §7Tepa na serwerowy spawn");
      $sender->sendMessage("§8§l>§r §4/tpa §8- §7Tepasz sie do wybranego gracza");
      $sender->sendMessage("§8§l>§r §4/home §8- §7Teleportuje do ustawionego domu");
      $sender->sendMessage("§8§l>§r §4/cx §8- §7Tworzy cobbleX'a");
      $sender->sendMessage("§8§l>§r §4/enchanting §8- §7Otwiera przenosny enchanting");
      $sender->sendMessage("§8§l>§r §4/efekty §8- §7Efekty do PVP");
      $sender->sendMessage("§8§l>§r §4/ec §8- §7Otwiera przenosny enderchest");
      $sender->sendMessage("§8§l>§r §4/case on/off §8- §7Wlacza / wylacza powiadomienia z premiumcase'ow'");
      $sender->sendMessage("§8§l>§r §4/kordy §8- §7Pokazuje twoja pozycje");
      $sender->sendMessage("§8§l>§r §4/warp §8- §7Teleportuje w wybranego warpa");
      $sender->sendMessage("§8§l>§r §4/feed §8- §7Napelnia wartosci glodu");
      $sender->sendMessage("§8§l>§r §4/repair §8- §7Naprawia wybrany item");
      $sender->sendMessage("§8§l>§r §4/heal §8- §7Leczy gracza");
      $sender->sendMessage("§8§l>§r §4/kit §8- §7Pokazuje dostepne zestawy");
      $sender->sendMessage("§8§l>§r §4/gracz §8- §7Pokazuje twoje statystyki na serwerze");
      $sender->sendMessage("§8§l>§r §4/gracz §8(§4nick§8) §8- §7Pokazuje statystyki wybranego gracza");
      $sender->sendMessage("§8§l>§r §4/topka §8- §7Pokazuje liste najlepszych graczy na serwerze");
      $sender->sendMessage("§8§l>§r §4/list §8- §7Pokazuje aktualna liczbe graczy na serwerze");
      $sender->sendMessage("§8§l>§r §4/helpop §8- §7Wyslanie wiadomosci do administracji");
      $sender->sendMessage("\n§7Limity:");
      $sender->sendMessage("§8§l>§r §7Koxy: §4".MAIN::LIMIT_KOXY);
      $sender->sendMessage("§8§l>§r §7Refy: §4".MAIN::LIMIT_REFY);
      $sender->sendMessage("§8§l>§r §7Perly: §4".MAIN::LIMIT_PERLY);
      $sender->sendMessage(" ");
      if($sender->hasPermission("PolishHard.command.pomoc"))
      $sender->sendMessage("§7Komendy §4administracyjne§8:");
      if($sender->hasPermission("PolishHard.command.gamemode"))
      $sender->sendMessage("§8§l>§r §4/gamemode §8- §7Zmienia twoj tryb gry");
      if($sender->hasPermission("PolishHard.command.ban"))
        $sender->sendMessage("§8§l>§r §4/ban §8- §7Banuje gracza na nick");
      if($sender->hasPermission("PolishHard.command.banip"))
        $sender->sendMessage("§8§l>§r §4/ban-ip §8- §7Banuje gracza na adres IP");
      if($sender->hasPermission("PolishHard.command.tempban"))
      $sender->sendMessage("§8§l>§r §4/tempban §8- §7Banuje tymczasowo gracza na nick");
      if($sender->hasPermission("PolishHard.command.unban"))
      $sender->sendMessage("§8§l>§r §4/unban §8- §7Odbanowywuje gracza");
      if($sender->hasPermission("pocketmine.command.kick"))
      $sender->sendMessage("§8§l>§r §4/kick §8- §7Wyrzyca gracza z serwera");
      if($sender->hasPermission("PolishHard.command.mute"))
      $sender->sendMessage("§8§l>§r §4/mute §8- §7Daje blokade na pisanie dla wybranego gracza");
      if($sender->hasPermission("PolishHard.command.unmute"))
      $sender->sendMessage("§8§l>§r §4/unmute §8- §7Odblokowywuje pisanie wybranemu graczowi");
      if($sender->hasPermission("pocketmine.command.teleport"))
      $sender->sendMessage("§8§l>§r §4/tp §8- §7Teleportujesz sie do wybranego gracza");
      if($sender->hasPermission("PolishHard.command.vanish"))
      $sender->sendMessage("§8§l>§r §4/vanish §8- §7Stajesz sie niewidzialny");
      if($sender->hasPermission("PolishHard.command.god"))
      $sender->sendMessage("§8§l>§r §4/god §8- §7Stajesz sie niesmiertelny");
      if($sender->hasPermission("PolishHard.command.fly"))
      $sender->sendMessage("§8§l>§r §4/fly §8- §7Wlacza / wylacza latanie");
      if($sender->hasPermission("PolishHard.command.clear"))
      $sender->sendMessage("§8§l>§r §4/clear §8- §7Czysci ekwipunek");
      if($sender->hasPermission("PolishHard.command.sprawdzanie"))
      $sender->sendMessage("§8§l>§r §4/sprawdzanie §8- §7Informacje o sprawdzaniu");
      if($sender->hasPermission("PolishHard.command.alert"))
      $sender->sendMessage("§8§l>§r §4/alert §8- §7Nadaje komunikat dla calego serwera");
      if($sender->hasPermission("PolishHard.command.pcase"))
      $sender->sendMessage("§8§l>§r §4/pcase §8- §7Daje premiumcase'a wybranemu graczowi");
      if($sender->hasPermission("PolishHard.command.pall"))
      $sender->sendMessage("§8§l>§r §4/pall §8- §7Daje calemu serwerowi premiumcase'y");
      if($sender->hasPermission("PolishHard.command.chat")){
      $sender->sendMessage("§8§l>§r §4/chat §8- §7Opcje dla chatu");
      $sender->sendMessage(" ");
  }
	}
}