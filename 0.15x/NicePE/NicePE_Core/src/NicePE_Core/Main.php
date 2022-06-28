<?php

namespace NicePE_Core;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\Snowball;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\utils\MainLogger;
use pocketmine\block\Air;
use pocketmine\block\Stone;
use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pockemine\inventory\Inventory;
use pocketmine\block\Block;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\level\Position\getLevel;
use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\SetTimePacket;
use pocketmine\network\protocol\TextPacket;
use pocketmine\network\protocol\AddPlayerPacket;

class Main extends PluginBase implements Listener{
	public $talked = array();
	public $configFile;
	private $config;
	public $cfg;
    private $format;
	private $order;
	private $perla;
	
		public function __construct(){
		$this->order = array();
	}
	
	public function onEnable(){
		$this->getLogger()->info("NicePE_Core Załadowano!");
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getPluginManager()->registerEvents(new BlockCmdListener($this), $this);
        $this->cfg = new Config($this->getDataFolder() . 'bk.yml', Config::YAML, array());
		if (!file_exists($this->getDataFolder())) @mkdir($this->getDataFolder(), 0755, true);
		$this->config = new Config( $this->getDataFolder()."config.properties",Config::PROPERTIES,array("damage"=>5) );
        $this->format = new BlockCmdFormat($this);
		@mkdir($this->getDataFolder());
        $this->configFile = (new Config($this->getDataFolder()."config.yml", Config::YAML, array(
            "messages" => array(
                "§7Wszystkie informcje znajdziesz na Spawnie! §8•",
                "§7Zapraszaj znajomych! §8•",
                "§7Serwer używa paczki zaprojektowanej przez: §aMerTeam §8•",
                "§7Kupno rang znajdziesz pod komendą: §a/sms§7! §8•",
				"§7Listę komend znajdziesz pod: §a/pomoc §8•",
				"§7Przed grą przeczytaj Regulamin: §a/regulamin§7!",
            ),
            "time" => "60",
            "prefix" => "§aBOT§8",
            "color" => "§8"
        )))->getAll();
        $time = intval($this->configFile["time"]) * 20;
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new SimpleMessagesTask($this), $time);		
	$this->pandora = new Config($this->getDataFolder() . "pandora.yml", Config::YAML);
	$this->perla = new Config($this->getDataFolder() . "perla.yml", Config::YAML);
	$this->zloto = new Config($this->getDataFolder() . "zloto.yml", Config::YAML);
    $this->diament = new Config($this->getDataFolder() . "diament.yml", Config::YAML);
	$this->obsydian = new Config($this->getDataFolder() . "obsydian.yml", Config::YAML);
	$this->emerald = new Config($this->getDataFolder() . "emerald.yml", Config::YAML);
    $this->zelazo = new Config($this->getDataFolder() . "zelazo.yml", Config::YAML);
	$this->proszek = new Config($this->getDataFolder() . "proszek.yml", Config::YAML);
	$this->ksiazki = new Config($this->getDataFolder() . "ksiazki.yml", Config::YAML);
    $this->lapis = new Config($this->getDataFolder() . "lapis.yml", Config::YAML);
    $this->wegiel = new Config($this->getDataFolder() . "wegiel.yml", Config::YAML);
    $this->tnt = new Config($this->getDataFolder() . "tnt.yml", Config::YAML);
    $this->fly = new Config($this->getDataFolder() . "fly.yml", CONFIG::YAML, array(
				"Odleglosc" => 4
		));
	$this->config = new Config($this->getDataFolder()."mobinfo.yml", Config::YAML, array(
				"replace words" => "true",
				"Replacement Message" => "Im a Fuckboi!",
				"Blocked words" => array(
						"fuck","dick", "cunt","fag","faggit","fuk","fgt"
				),
				"interval" => 10
				
		
		
		
		));
		$this->config->save();
	}
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
			if($cmd->getName() == "cobblex"){
				if(empty($args)) {
					$sender->sendMessage("§l§8)§7===========§8( (§cCobbleX§8) )§7===========§8(");
					$sender->sendMessage("§c* §7CobbleX jest dodatkiem, ktory losuje nagrode.");
					$sender->sendMessage("§c* §7Jak go aktywowac?");
					$sender->sendMessage("   §7Wystarczy postawic CobbleX'a a wylosuje");
					$sender->sendMessage("   §7automatycznie jedna nagrode sposrod 25.");
					$sender->sendMessage("§c* §7CobbleX'a kupisz komenda: §c/cobblex kup§7.");
				    $sender->sendMessage("§l§8)§7===========§8( (§cCobbleX§8) )§7===========§8(");
					return false;
				}
					 if($args[0] == "kup") {
					 if($sender->getInventory()->contains(Item::get(4, 0, 576))){
						$sender->getInventory()->removeItem(Item::get(4, 0, 576));
						 $sender->getInventory()->addItem(Item::get(48, 0, 1));
						$sender->sendMessage("§8• (§cCobbleX§8) §7Zakupiłeś §cCobbleX§7! §8•");
            }
						else{
							$sender->sendMessage("§8• (§cCobbleX§8) §7Nie posiadasz tyle §aBruku! §8•");
		}
	}
}
		if($cmd->getName() == "vip"){
					$sender->sendMessage("§l§8)§7======================§8( (§cVIP§8) )§7======================§8(");
				$sender->sendMessage("§c* §7- Aby kupic wyslij sms'a o tresci: §cAP.HOSTMC §7pod numer: §c72068");
				$sender->sendMessage("    §7- Koszt to: §c2§7.§c46§7zl");
				$sender->sendMessage("§c* §7- Ranga §l§cVIP§7§r posiada permisje takie jak:");
				$sender->sendMessage(" §7- §c/kit vip \n §7- §c/repair§7 \n §7- §c/back \n §7- Krotszy czas teleportacji §8(§77s§8) \n §7- Mozliwosc pisania na chacie bez koniecznosci wykopania Cobbla");
				$sender->sendMessage("§c* §7- Kod podaj tylko do §cWlasciciela§7!");
				$sender->sendMessage("§c* §7Chcesz miec range §cVIP §7za darmo?");
				$sender->sendMessage("§c* §7Napisz §c/vip losuj§7!");
				$sender->sendMessage("§l§8)§7======================§8( (§cVIP§8) )§7======================§8(");
		}
		if($cmd->getName() == "svip"){
				$sender->sendMessage("§l§8)§7======================§8( (§cSVIP§8) )§7======================§8(");
				$sender->sendMessage("§c* §7- Aby kupic wyslij sms'a o tresci: §cAP.HOSTMC §7pod numer: §c76068");
				$sender->sendMessage("    §7- Koszt to: §c7§7.§c38§7zl");
				$sender->sendMessage("§c* §7- Ranga §l§cSVIP§7§r posiada permisje takie jak:");
				$sender->sendMessage(" §7- §cPowiekszony Drop \n §7- §c/kit vip \n §7- §c/kit svip \n §7- §c/repair§7 \n §7- §c/back \n §7- Krotszy czas teleportacji §8(§77s§8) \n §7- Mozliwosc pisania na chacie bez koniecznosci wykopania Cobbla");
				$sender->sendMessage("§c* §7- Kod podaj tylko §cWlascicielowi§7!");
				$sender->sendMessage("§l§8)§7===========§8( (§cSVIP§8) )§7===========§8(");
		}
		if($cmd->getName() == "sponsor"){
				$sender->sendMessage("§l§8)§7======================§8( (§cSPONSOR§8) )§7======================§8(");
				$sender->sendMessage("§c* §7- Aby kupic wyslij sms'a o tresci: §cAP.HOSTMC §7pod numer: §c92058");
				$sender->sendMessage("    §7- Koszt to: §c24§7.§c60§7zl");
				$sender->sendMessage("§c* §7- Ranga §l§cSPONSOR§7§r posiada permisje takie jak:");
				$sender->sendMessage(" §7- §cPowiekszony Drop§7 \n §7- §c/kit vip \n §7- §c/kit svip \n §7- §c/kit sponsor \n §7- §c/repair \n §7- §c/back \n §7- §c/heal \n §7- Krotszy czas teleportacji §8(§75s§8) \n §7- Mozliwosc pisania na chacie bez koniecznosci wykopania Cobbla");
				$sender->sendMessage("§c* §7- Kod z rangi zwrotnej podaj tylko do §cAdministracji§7!");
				$sender->sendMessage("§l§8)§7======================§8( (§cSPONSOR§8) )§7======================§8(");
		}
				if($cmd->getName() == "swagger"){
				$sender->sendMessage("§l§8)§7====================§8( (§cSWAGGER§8) )§7====================§8(");
				$sender->sendMessage("§c* §7- Aby kupic wyslij sms'a o tresci: §cAP.HOSTMC §7pod numer: §c92578");
				$sender->sendMessage("    §7- Koszt to: §c30§7.§c75§7zl");
				$sender->sendMessage("§c* §7- Ranga §l§cSWAGGER§7§r posiada permisje takie jak:");
				$sender->sendMessage(" §7- §cPowiekszony Drop na MAXA§7 \n §7- §c/kit vip \n §7- §c/kit svip \n §7- §c/kit sponsor \n §7- §c/repair \n §7- §c/back \n §7- §c/heal \n §7- §c/serce \n §7- §cDarmowa gildia \n §7- §cMozliwosc Zmiany Pogody \n §7- §cParticlesy pod nogami \n §7- Krotszy czas teleportacji §8(§75s§8) \n §7- Mozliwosc pisania na chacie bez koniecznosci wykopania Cobbla");
				$sender->sendMessage("\n§c* §7- Kod podaj tylko do §cWlasciciela§7!");
				$sender->sendMessage("§l§8)§7====================§8( (§cSWAGGER§8) )§7====================§8(");
		}
		if($cmd->getName() == "yt"){
				$sender->sendMessage("§l§8)§7===========§8( (§cYT§8) )§7===========§8(");
				$sender->sendMessage("§c* §7- Aby zdobyc range §cYT §7musisz posiadac §c150 §7Subskrybcji");
				$sender->sendMessage("    §7- Oraz trailer na swoim kanale!");
				$sender->sendMessage("    §7- Trailer musi posiadac §c50 §7wyswietlen");
				$sender->sendMessage("§c* §7- Ranga §cYT §7posiada takie permisje jak:");
				$sender->sendMessage(" §7- §c/kit vip§7 \n §7- §c/repair§7 \n §7- §c/back \n §7- Krotszy czas teleportacji §8(§77s§8) \n §7- Mozliwosc pisania na chacie bez koniecznosci wykopania Cobbla");
				$sender->sendMessage("§c* §7- Po range zglos sie do §cAdministacji§7!");
				$sender->sendMessage("§l§8)§7===========§8( (§cYT§8) )§7===========§8(");
		}
		if($cmd->getName() == "yt+"){
				$sender->sendMessage("§l§8)§7===========§8( (§cYT+§8) )§7===========§8(");
				$sender->sendMessage("§c* §7- Aby zdobyc range §cYT+ §7musisz posiadac §c300 §7Subskrybcji");
				$sender->sendMessage("    §7- Oraz trailer na swoim kanale!");
				$sender->sendMessage("    §7- Trailer musi posiadac §c100 §7wyswietlen");
				$sender->sendMessage("§c* §7- Ranga §cYT+ §7posiada takie permisje jak:");
				$sender->sendMessage(" §7- §c/kit vip§7 \n §7- §c/repair§7 \n §7- §c/back \n §7- §c/heal \n §7- Krotszy czas teleportacji §8(§75s§8) \n §7- Mozliwosc pisania na chacie bez koniecznosci wykopania Cobbla");
				$sender->sendMessage("§c* §7- Po range zglos sie do §cAdministacji§7!");
				$sender->sendMessage("§l§8)§7===========§8( (§cYT+§8) )§7===========§8(");
		}
				if($cmd->getName() == "nicki"){
				$sender->sendMessage("§l§8)§7===========§8( (§cNICKI§8) )§7===========§8(");
				$sender->sendMessage("§c* §7Dostepne kolory nicków:");
				$sender->sendMessage("§c* §7Kolor §aZielony§7:  §7\n  §7- Wyslij SMS'a o tresci: §cAP.HOSTMC §7pod numer: §c72068§7 \n   §7- Koszt to: §c2§7,§c46§7zl");
				$sender->sendMessage("§c* §7Kolor §bNiebieski§7:  §7\n  §7- Wyslij SMS'a o tresci: §cAP.HOSTMC §7pod numer: §c72068§7 \n   §7- Koszt to: §c2§7,§c46§7zl");
				$sender->sendMessage("§c* §7Kolor §6Pomaranczowy§7:  §7\n  §7- Wyslij SMS'a o tresci: §cAP.HOSTMC §7pod numer: §c72068§7 \n   §7- Koszt to: §c2§7,§c46§7zl");
				$sender->sendMessage("§c* §7Kolor §eZolty§7:  §7\n  §7- Wyslij SMS'a o tresci: §cAP.HOSTMC §7pod numer: §c72068§7 \n   §7- Koszt to: §c2§7,§c46§7zl");
				$sender->sendMessage("§c* §7Kolor §5Rozowy§7:  §7\n  §7- Wyslij SMS'a o tresci: §cAP.HOSTMC §7pod numer: §c72068§7 \n   §7- Koszt to: §c2§7,§c46§7zl");
				$sender->sendMessage("§c* §7Kolor §fBialy§7:  §7\n  §7- Wyslij SMS'a o tresci: §cAP.HOSTMC §7pod numer: §c72068§7 \n   §7- Koszt to: §c2§7,§c46§7zl");
				$sender->sendMessage("§l§8)§7===========§8( (§cNICKI§8) )§7===========§8(");
		}
				if($cmd->getName() == "sms"){
				$sender->sendMessage("§l§8)§7===========§8( (§cSMS§8) )§7===========§8(");
				$sender->sendMessage("§c* §7- Rangi §7- §c/rangi");
				$sender->sendMessage("§c* §7- PremiumCase §7- §c/premiumcase");
				$sender->sendMessage("§c* §7- Kolorowy Nick §7- §c/nicki");
				$sender->sendMessage("§c* §7- Particlesy Pod Nogami §7- §c/particleinfo");
				$sender->sendMessage("§l§8)§7===========§8( (§cSMS§8) )§7===========§8(");
		}
		if($cmd->getName() == "regulamin"){
				$sender->sendMessage("§l§8)§7===========§8( (§cREGULAMIN§8) )§7===========§8( ");
				$sender->sendMessage("§c1. §7Brak znajomosci regulaminu, nie zwalnia od jego przestrzegania");
				$sender->sendMessage("§c2. §7Cheatowanie = §cBAN §7(w zaleznosci od ustalenia z administratorem) wynosi od §c3 dni §7do §cPERM Bana");
				$sender->sendMessage("§c3. §7Przeklinanie oraz wyzywanie = §cMUTE §c10§7-§c30 §7min");
				$sender->sendMessage("§c4. §7Obraza administracji lub serwera = §cBAN 1 dzien ");
				$sender->sendMessage("§c5. §7Nie ponosimy odpowiedzialnosci za stracone itemy!");
				$sender->sendMessage("§c6. §7Reklamowanie innych serwerow lub uslug = §cPERM BAN §7(wyjatkiem jest reklamowanie live'ow na §cYT§7)");
				$sender->sendMessage("§c7. §7Nabijanie na multikontach = §cBAN 7 dni");
				$sender->sendMessage("§c8. §7Proszenie o rangi = §cBAN 2 dni");
				$sender->sendMessage("§c9. §7Bugowanie oraz wykorzystanie bagow serwera = §cBAN 10 dni");
				$sender->sendMessage("§c10. §7To normalne ze administracji nie ma §c24/7 §7:)");
				$sender->sendMessage("§l§8)§7===========§8( (§cREGULAMIN§8) )§7===========§8(");
		}
		if($cmd->getName() == "premiumcase"){
				$sender->sendMessage("§l§8)§7===========§8( (§cPREMIUMCASE§8) )§7===========§8(");
				$sender->sendMessage("§c* §7- §c16 §7PremiumCase - §c2§7.§c46§7zl");
				$sender->sendMessage("     §7Wyslij SmS'a o tresci: §cAP.HOSTMC §7pod numer: §c72068");
				$sender->sendMessage("§c* §7- §c32 §7PremiumCase - §c4§7.§c92§7zl");
				$sender->sendMessage("     §7Wyslij SmS'a o tresci: §cAP.HOSTMC §7pod numer: §c74068");
				$sender->sendMessage("§c* §7- §c64 §7PremiumCase - §c7§7.§c38§7zl");
				$sender->sendMessage("     §7Wyslij SmS'a o tresci: §cAP.HOSTMC §7pod numer: §c76068");
				$sender->sendMessage("§c* §7- §c128 §7PremiumCase - §c12§7.§c30§7zl");
				$sender->sendMessage("     §7Wyslij SmS'a o tresci: §cAP.HOSTMC §7pod numer: §c91058");
				$sender->sendMessage("§l§8)§7===========§8( (§cPREMIUMCASE§8) )§7===========§8(");
		}
		if($cmd->getName() == "pomoc"){
				$sender->sendMessage("§l§8)§7===========§8( (§cPOMOC§8) )§7===========§8( ");
				$sender->sendMessage("§c* §7Komendy serwera");
				$sender->sendMessage("§c* §7Komendy Gildi: §c/g pomoc §8(§c1§7-§c3§8)");
				$sender->sendMessage("§c* §7Top 10 graczy PvP: §c/topka");
				$sender->sendMessage("§c* §7Kupno rang pod: §c/rangi");
				$sender->sendMessage("§c* §7Dostepne efekty do PvP: §c/efekty");
				$sender->sendMessage("§c* §7Startowe itemki i nie tylko: §c/kit");
				$sender->sendMessage("§c* §7Wszystko, co mozna kupic przez SMS: §c/sms");
				$sender->sendMessage("§c* §7Administracja online pod: §c/administracja");
				$sender->sendMessage("§c* §7Schowek: §c/schowek");
				$sender->sendMessage("§c* §7Stan pieniedzy: §c/mymonety");
				$sender->sendMessage("§c* §7Wyslanie pieniedzy: §c/pay");
				$sender->sendMessage("§l§8)§7===========§8( (§cPOMOC§8) )§7===========§8(");
		}
		if($cmd->getName() == "rangi"){
				$sender->sendMessage("§l§8)§7===========§8( (§cRangi§8) )§7===========§8(");
				$sender->sendMessage("§c* §7- Ranga §8[§6VIP§8] §7- §c/vip");
				$sender->sendMessage("§c* §7- Ranga §8[§eSVIP§8] §7- §c/svip");
				$sender->sendMessage("§c* §7- Ranga §8[§9SPONSOR§8] §7- §c/sponsor");
				$sender->sendMessage("§c* §7- Ranga §8[§4S§cW§6A§eG§fG§aE§2R§8] §7- §c/swagger");
				$sender->sendMessage("§l§8)§7===========§8( (§cRangi§8) )§7===========§8(");
		}
		if($cmd->getName() == "limit"){
				$sender->sendMessage("§l§8)§7===========§8( (§cLimit§8) )§7===========§8(");
				$sender->sendMessage("§c* §7- Limit na serwerze wynosi");
				$sender->sendMessage("§c* §7- §c2§7 Koxy");
				$sender->sendMessage("§c* §7- §c8§7 Refow");
				$sender->sendMessage("§c* §7- §c4§7 Perly");
				$sender->sendMessage("§l§8)§7===========§8( (§cLimit§8) )§7===========§8(");
		}
		if($cmd->getName() == "administracja"){
			
			$xstrixu = $this->getServer()->getPlayer("xStrixU");
			$adipol1359 = $this->getServer()->getPlayer("AdiPol1359");
			$kraimer = $this->getServer()->getPlayer("kraimer");
			$davsonek__ = $this->getServer()->getPlayer("protect_cant");
			$roxerq = $this->getServer()->getPlayer("RoxerQ");
			$pralin = $this->getServer()->getPlayer("pralin");
			$pakujwalize_ = $this->getServer()->getPlayer("Pakujwalize_");
			$mc_mufina = $this->getServer()->getPlayer("Mc_Mufina");
			$tlorek = $this->getServer()->getPlayer("TLOREK");
			if($xstrixu){
				$sender->sendMessage("§l§8)§7=======§8( (§cADMINISTRACJA§8) )§7=======§8(");
				$sender->sendMessage("§8[§4W§8]§c xStrixU§7 jest teraz §8[§aONLINE§8] ");
			}else{
				$sender->sendMessage("§l§8)§7=======§8( (§cADMINISTRACJA§8) )§7=======§8(");
				$sender->sendMessage("§8[§4W§8]§c xStrixU§7 jest teraz §8[§cOFFLINE§8] ");
			}
			if($adipol1359){
				$sender->sendMessage("§8[§4W§8]§c AdiPol1359§7 jest teraz §8[§aONLINE§8] ");
			}else{
				$sender->sendMessage("§8[§4W§8]§c AdiPol1359§7 jest teraz §8[§cOFFLINE§8] ");
			}
			if($roxerq){
				$sender->sendMessage("§8[§4HA§8]§c RoxerQ§7 jest teraz §8[§aONLINE§8] ");
			}else{
				$sender->sendMessage("§8[§4HA§8]§c RoxerQ§7 jest teraz §8[§cOFFLINE§8] ");
			}
			if($davsonek__){
				$sender->sendMessage("§8[§4A§8]§c protect_cant§7 jest teraz §8[§aONLINE§8] ");
			}else{
				$sender->sendMessage("§8[§4A§8]§c protect_cant§7 jest teraz §8[§cOFFLINE§8] ");
			}
			if($mc_mufina){
				$sender->sendMessage("§8[§4A§8]§c Mc_Mufina§7 jest teraz §8[§aONLINE§8] ");
			}else{
				$sender->sendMessage("§8[§4A§8]§c Mc_Mufina§7 jest teraz §8[§cOFFLINE§8] ");
			}
			if($tlorek){
				$sender->sendMessage("§8[§2M§8]§c TLOREK§7 jest teraz §8[§aONLINE§8] ");
			}else{
				$sender->sendMessage("§8[§2M§8]§c TLOREK§7 jest teraz §8[§cOFFLINE§8] ");
				$sender->sendMessage("§l§8)§7=======§8( (§cADMINISTRACJA§8) )§7=======§8(");
			}
		}
		if($cmd->getName() == "zbanuj"){
		if((isset($args[0])) && (!isset($args[1]))){
		$nick = $args[0];
		Server::getInstance()->broadcastMessage("§8• §8(§cNicePE§8) §7Gracz §c" . $nick . "§7 zostal zbanowany z powodu:§4 Brak §8•");
		Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'ban ' . $nick . '');
		}
		if(isset($args[1])){
		$nick = $args[0];
		$powod = $args[1];
		Server::getInstance()->broadcastMessage("§8• §8(§cNicePE§8) §7Gracz §c" . $nick . "§7 zostal zbanowany z powodu:§4 " . $powod . " §8•");
		Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'ban ' . $nick . '');
		}
		}
				if($cmd->getName() == "zbanuj-ip"){
		if((isset($args[0])) && (!isset($args[1]))){
		$nick = $args[0];
		Server::getInstance()->broadcastMessage("§8• §8(§cNicePE§8) §7Gracz §c" . $nick . "§7 zostal zbanowany na IP z powodu:§4 Brak §8•");
		Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'ban-ip ' . $nick . '');
		}
		if(isset($args[1])){
		$nick = $args[0];
		$powod = $args[1];
		Server::getInstance()->broadcastMessage("§8• §8(§cNicePE§8) §7Gracz §c" . $nick . "§7 zostal zbanowany na IP z powodu:§4 " . $powod . " §8•");
		Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'ban-ip ' . $nick . '');
		}
		}
		if($cmd->getName() == "odbanuj"){
		$nick = $args[0];
		Server::getInstance()->broadcastMessage("§8• §8(§cNicePE§8) §7Gracz §c" . $nick . "§7 zostal odbanowany! §8•");
		Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'pardon ' . $nick . '');
		}
						if($cmd == "daj"){
			if($sender->hasPermission("daj.command")){
				if(empty($args)) {
					$sender->sendMessage("§cUżyj /daj <vip/svip/uvip/pc16/pc32/pc64/pc128/zielony/niebieski/pomaranczowy/zolty/rozowy/bialy/particlesy> <gracz>");
				}
		if($args[0] == "vip")  {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Kupił rangę §6VIP§7! §7DZIĘKUJEMY!");
	
Server::getInstance()->broadcastMessage("§7Koszt To: §c2§7,§c46§7zł");

Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					
Server::getInstance()->broadcastMessage("");
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'setgroup ' . $player . ' vip');
			 }
		} 
		if($args[0] == "svip") {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Kupił rangę §eSVIP§7! §7DZIĘKUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Koszt To: §c7§7,§c38§7zł");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'setgroup ' . $player . ' svip');
			 }
		}
		if($args[0] == "sponsor") {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Kupił rangę §9SPONSOR§7! §7DZIĘKUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Koszt To: §c24§7,§c60§7zł");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'setgroup ' . $player . ' sponsor');
			 }
		}
		if($args[0] == "swagger") {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Kupił rangę §4S§cW§6A§eG§fG§aE§2R§8§7! §7DZIĘKUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Koszt To: §c30§7,§c75§7zł");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'setgroup ' . $player . ' swagger');
			 }
		}
		if($args[0] == "yt")   {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Otrzymał rangę §fY§cT§7! §7GRATULUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Więcej Informacji Pod: §c/yt");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'setgroup ' . $player . ' yt');
			 }
		}
		if($args[0] == "yt+")  {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Otrzymał rangę §fY§cT§4+§7! §7GRATULUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Więcej Informacji Pod: §c/yt+");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'setgroup ' . $player . ' yt+');
			 }
		}
		if($args[0] == "pc16") {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Zakupił §c16 §7PremiumCase! §7DZIĘKUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Więcej Informacji Pod: §c/premiumcase");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");

Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'give ' . $player . ' 146 16');

			 }
		}
if($args[0] == "zielony") {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Zakupił §aZielony §7Nick! §7DZIĘKUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Więcej Informacji Pod: §c/nicki");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");

Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'nick ' . '§a' . $player . ' ' . $player);

			 }
		}
if($args[0] == "niebieski") {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Zakupił §bNiebieski §7Nick! §7DZIĘKUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Więcej Informacji Pod: §c/nicki");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");

Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'nick ' . '§b' . $player . ' ' . $player);

			 }
		}
if($args[0] == "pomaranczowy") {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Zakupił §6Pomarańczowy §7Nick! §7DZIĘKUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Więcej Informacji Pod: §c/nicki");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");

Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'nick ' . '§6' . $player . ' ' . $player);

			 }
		}
if($args[0] == "zolty") {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Zakupił §eZółty §7Nick! §7DZIĘKUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Więcej Informacji Pod: §c/nicki");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");

Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'nick ' . '§e' . $player . ' ' . $player);

			 }
		}
if($args[0] == "rozowy") {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Zakupił §5Różowy §7Nick! §7DZIĘKUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Więcej Informacji Pod: §c/nicki");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");

Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'nick ' . '§5' . $player . ' ' . $player);

			 }
		}
if($args[0] == "bialy") {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Zakupił §fBiały §7Nick! §7DZIĘKUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Więcej Informacji Pod: §c/nicki");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");

Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'nick ' . '§f' . $player . ' ' . $player);

			 }
		}
if($args[0] == "particlesy") {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Zakupił §cParticlesy §7DZIĘKUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Więcej Informacji Pod: §c/particleinfo");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");

Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'setuperm ' . $player . ' nicepe.particlesy');

			 }
		}
		if($args[0] == "pc32") {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Zakupił §c32 §7PremiumCase! §7DZIĘKUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Więcej Informacji Pod: §c/premiumcase");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");

Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'give ' . $player . ' 146 32');

			 }
		}
		if($args[0] == "pc64") {
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Zakupił §c64 §7PremiumCase! §7DZIĘKUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Więcej Informacji Pod: §c/premiumcase");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");

Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'give ' . $player . ' 146 64');
			 }
		}
		if($args[0] == "pc128"){
				if(isset($args[1])){
					$player = $args[1];
					Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");
					Server::getInstance()->broadcastMessage("§7Gracz §c" . $player . " §7Zakupił §c128 §7PremiumCase! §7DZIĘKUJEMY!");
					
Server::getInstance()->broadcastMessage("§7Więcej Informacji Pod: §c/premiumcase");
					
Server::getInstance()->broadcastMessage("§c•----------------> NicePE <---------------•");

Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'give ' . $player . ' 146 128');

			 }
		}
			} 
}
		if($cmd->getName() == "voucher"){
			if($sender->hasPermission("nicepe.voucher")){
				if(empty($args)){
					$sender->sendMessage("§8[§cNicePE§8] §7Użyj /voucher daj <vip/svip/uvip/pc16/pc32/pc64/pc128>");
				}
				if(strtolower($args[0] == "daj") && strtolower($args[1] == "vip")){
					$gracz = $sender->getPlayer();
					$vip = Item::get(340, 0, 1);
				    $vip->setCustomName("§r§l§cVoucher na VIP'a§r\n§r§f§lKliknij aby aktywowac§r");
			        $gracz->getInventory()->addItem($vip);
	                $sender->sendMessage("§8[§cNicePE§8]§7 Otrzymałeś voucher na VIP'a");
					return true;
				}
				if(strtolower($args[0] == "daj") && strtolower($args[1] == "svip")){
					$gracz = $sender->getPlayer();
					$svip = Item::get(340, 0, 1);
				    $svip->setCustomName("§r§l§cVoucher na SVIP'a§r\n§r§f§lKliknij aby aktywowac§r");
			        $gracz->getInventory()->addItem($svip);
	                $sender->sendMessage("§8[§cNicePE§8]§7 Otrzymałeś voucher na SVIP'a");
					return true;
				}
				if(strtolower($args[0] == "daj") && strtolower($args[1] == "uvip")){
					$gracz = $sender->getPlayer();
					$uvip = Item::get(340, 0, 1);
				    $uvip->setCustomName("§r§l§cVoucher na UVIP'a§r\n§r§f§lKliknij aby aktywowac§r");
			        $gracz->getInventory()->addItem($uvip);
	                $sender->sendMessage("§8[§cNicePE§8]§7 Otrzymałeś voucher na UVIP'a");
					return true;
				}
				if(strtolower($args[0] == "daj") && strtolower($args[1] == "pc16")){
					$gracz = $sender->getPlayer();
					$pc16 = Item::get(340, 0, 1);
				    $pc16->setCustomName("§r§l§cVoucher na 16 PremiumCase'ow§r\n§r§f§lKliknij aby aktywowac§r");
			        $gracz->getInventory()->addItem($pc16);
	                $sender->sendMessage("§8[§cNicePE§8]§7 Otrzymałeś voucher na 16 PremiumCase");
					return true;
				}
				if(strtolower($args[0] == "daj") && strtolower($args[1] == "pc32")){
					$gracz = $sender->getPlayer();
					$pc32 = Item::get(340, 0, 1);
				    $pc32->setCustomName("§r§l§cVoucher na 32 PremiumCase'y§r\n§r§f§lKliknij aby aktywowac§r");
			        $gracz->getInventory()->addItem($pc32);
	                $sender->sendMessage("§8[§cNicePE§8]§7 Otrzymałeś voucher na 32 PremiumCase");
					return true;
				}
				if(strtolower($args[0] == "daj") && strtolower($args[1] == "pc64")){
					$gracz = $sender->getPlayer();
					$pc64 = Item::get(340, 0, 1);
				    $pc64->setCustomName("§r§l§cVoucher na 64 PremiumCase'y§r\n§r§f§lKliknij aby aktywowac§r");
			        $gracz->getInventory()->addItem($pc64);
	                $sender->sendMessage("§8[§cNicePE§8]§7 Otrzymałeś voucher na 64 PremiumCase");
					return true;
				}
				if(strtolower($args[0] == "daj") && strtolower($args[1] == "pc128")){
					$gracz = $sender->getPlayer();
					$pc128 = Item::get(340, 0, 1);
				    $pc128->setCustomName("§r§l§cVoucher na 128 PremiumCase'ow§r\n§r§f§lKliknij aby aktywowac§r");
			        $gracz->getInventory()->addItem($pc128);
	                $sender->sendMessage("§8[§cNicePE§8]§7 Otrzymałeś voucher na 128 PremiumCase");
					return true;
				}
              }
}
		if($cmd->getName() == "sk"){
			if($sender->hasPermission("nicepe.skoczek")){
				if(empty($args)){
					$sender->sendMessage("§8• [§cNicePE§8] §7Użyj /sk daj");
				}
				if(strtolower($args[0] == "daj")){
					$gracz = $sender->getPlayer();
					$skoczek = Item::get(288, 0, 1);
				    $skoczek->setCustomName("§r§l§bSkoczek");
			        $gracz->getInventory()->addItem($skoczek);
	                $sender->sendMessage("§8• [§cNicePE§8]§7 Otrzymałeś skoczek");
					return true;
				}
              }
              }
		if($cmd->getName() == "specialarmor"){
			if($sender->hasPermission("nicepe.specialarmor")){
				if(empty($args) or $args[0] == "pomoc"){
					$sender->sendMessage("§8/specialarmor szybkiebuty/helmgornika");
					return true;
				}
				if($args[0] == "szybkiebuty"){
					$gracz = $sender->getPlayer();
					$item = Item::get(313, 0, 1);
				    $item->setCustomName("§r§l§fSzybkie Buty :D§r\n§r§l§aUbierz i poczuj moc!§r");
			        $gracz->getInventory()->addItem($item);
	                $sender->sendMessage("§aOtrzymales szybkie butu :D");
					return true;
				}
			    if($args[0] == "helmgornika"){
					$gracz = $sender->getPlayer();
					$item = Item::get(310, 0, 1);
				    $item->setCustomName("§r§l§aHelm Gornika :D§r\n§r§l§aUbierz i poczuj sie jak gornik!§r");
			        $gracz->getInventory()->addItem($item);
	                $sender->sendMessage("§aOtrzymales helm gornika :D");
					return true;
				}
		}
	}
		if($cmd->getName() == "miecz"){
			if($sender->hasPermission("nicepe.mieczyk")){
				if(empty($args) or $args[0] == "pomoc"){
					$sender->sendMessage("§8/mieczy daj");
					return true;
				}
				if($args[0] == "daj"){
					$gracz = $sender->getPlayer();
					$item = Item::get(276, 0, 1);
				    $item->setCustomName("§l§cMiecz Zaglady");
			        $gracz->getInventory()->addItem($item);
	              $sender->sendMessage("§aOtrzymales magiczny miecz zaglady :D");
					return true;
				}
		if(isset($args[1])){
			$gr = $args[1];
						$item = Item::get(276, 0, 1);
				    $item->setCustomName("§l§cMiecz Zaglady");
			        $gracz->getInventory()->addItem($item);
	              $sender->sendMessage("§aOtrzymales magiczny miecz zaglady :D");
	}
}
		}
			if($cmd->getName() == "pandora"){
								if(empty($args)) {
					$nick = $sender->getPlayer()->getDisplayName();
				$sender->sendMessage("§l§8)§7===========§8( (§cPremiumCase§8) )§7===========§8( ");
					$sender->sendMessage("§c* §7Aby wlaczyc/wylaczyc powiadomienia o otworzonym §cPC §7wpisz:");
					$sender->sendMessage("§c* §7/case §con§7/§coff");
					$sender->sendMessage($this->pandora->get($nick) == 0 ? "§c*§7 Aktualnie masz: §aON" : "§c* §7Aktualnie masz: §cOFF");
				$sender->sendMessage("§l§8)§7===========§8( (§cPremiumCase§8) )§7===========§8( ");
					return true;
				}
				if(count($args == 1)) {
					
					/////////////////////////////// CREATE ///////////////////////////////
					
					if($args[0] == "on") {
											$sender->sendMessage("§8> §7Wlaczyles powiadomienia o otworzonej pandorce");
    $nick = $sender->getPlayer()->getDisplayName();
	$this->pandora->set($nick,0);
	$this->pandora->save();
				}
				}
				if($args[0] == "off") {
																$sender->sendMessage("§8> §7Wylaczyles powiadomienia o otworzonej pandorce");
    $nick = $sender->getPlayer()->getDisplayName();
	$this->pandora->set($nick,1);
	$this->pandora->save();
				}
				}
	if($cmd->getName() == "pcase"){
                if($sender->hasPermission("pcase.command")){
                if(count($args) == 1 or empty($args)){
	      
                    $sender->sendMessage("§8• §7Uzyj: §c/pcase §c<ilosc> §c<gracz> §8•");
                }
                if(count($args) == 2){
                    if(is_numeric($args[0])){
                    $gracz = $this->getServer()->getPlayer($args[1]);
                    $gracz->getInventory()->addItem(Item::get(146, 0, $args[0]));
                    $gracz->sendMessage("§8• [§cPremiumCase§8] §7Otrzymales§c " . $args[0] . " §7PremiumCase! §8•");
                    $gracz->sendTip("§8• [§cPremiumCase§8] §7Otrzymales§c " . $args[0] . " §7PremiumCase! §8•");
                }
                else{
                    $sender->sendMessage("§8• [§cPremiumCase§8] §cNie prawidlowo wpisana komenda! §8•");
                }
            }
                                }
                else{
                    $sender->sendMessage("§8• §7Niestety nie możesz tego użyć! §8•");
                }
            }
			            if(strtolower($cmd->getName()) === "pall"){
                if($sender->hasPermission("pcase.command")){
                if(empty($args)){
                $sender->sendMessage("§8• §7Poprawne uzycie to: §c/pall §c<ilosc> §8•");
                }
                    if(count($args) == 1){
                        if(is_numeric($args[0])){
                            foreach($this->getServer()->getOnlinePlayers() as $p){
                            $p->getInventory()->addItem(Item::get(146, 0, $args[0]));
                            $p->sendTip("§8• §8[§cPremiumCase§8] §7Wszyscy otrzymali§c " . $args[0] . " §7PremiumCase od §c" . $sender->getName() . "§7! §8•");
                            $p->sendMessage("§8• §8[§cPremiumCase§8] §7Wszyscy otrzymali§c " . $args[0] . " §7PremiumCase od §c" . $sender->getName() . "§7! §8•");
                            }
                        }
                                            else{
                            $sender->sendMessage("§8• §8[§cPremiumCase§8] §7Nie prawidlowo wpisana komenda! §8•");
                        }
    }
                                }
                else{
                    $sender->sendMessage("§8• §7Nie posiadasz uprawnien do tej komendy! §8•");
                }
       }
			if($cmd->getName() == "drop"){
								if(empty($args)) {
					$sender->sendMessage("§l§8)§7================§8( (§cDROP§8) )§7================§8(");
					$sender->sendMessage("§c*§7 Perla §8(§cPonizej 20y§8)");
					$sender->sendMessage("§c*§7 Zloto §8(§cPonizej 10y§8)");
					$sender->sendMessage("§c*§7 Diament §8(§cPonizej 15y§8)");
					$sender->sendMessage("§c*§7 Obsydian §8(§cPonizej 35y§8)");
					$sender->sendMessage("§c*§7 Emerald §8(§cPonizej 30y§8)");
					$sender->sendMessage("§c*§7 Zelazo §8(§cPonizej 30y§8)");
					$sender->sendMessage("§c*§7 Proszek §8(§cPonizej 40y§8)");
					$sender->sendMessage("§c*§7 Ksiazki §8(§cPonizej 35y§8)");
					$sender->sendMessage("§c*§7 Lapis §8(§cPonizej 40y§8)");
					$sender->sendMessage("§c*§7 Wegiel §8(§cPonizej 50y§8)");
					$sender->sendMessage("§c*§7 TNT §8(§cPonizej 25y§8)");
					$sender->sendMessage("§c* §7/drop all on/off §8- §7Uzyj aby wlaczyc lub wylaczyc drop kazdego surowca");
					$sender->sendMessage("§c* §7/drop (surowiec) on/off §8- §7Uzyj aby wlaczyc lub wylaczyc drop danego surowca");
					$sender->sendMessage("§c* §7/drop info §8- §7Uzyj aby sprawdzic stan dropu");
					$sender->sendMessage("§l§8)§7================§8( (§cDROP§8) )§7================§8(");
				}
				if(count($args == 1)) {
				if($args[0] == "info"){
					$nick = $sender->getPlayer()->getDisplayName();
					$sender->sendMessage("§8########### §cNicePE §8###########");
					$sender->sendMessage($this->perla->get($nick) == 0 ? "§c*§7 Perla: §aON" : "§c* §7Perla: §cOFF");
					$sender->sendMessage($this->zloto->get($nick) == 0 ? "§c*§7 Zloto: §aON" : "§c* §7Zloto: §cOFF");
					$sender->sendMessage($this->diament->get($nick) == 0 ? "§c*§7 Diament: §aON" : "§c* §7Diament: §cOFF");
					$sender->sendMessage($this->obsydian->get($nick) == 0 ? "§c*§7 Obsydian: §aON" : "§c* §7Obsydian: §cOFF");
					$sender->sendMessage($this->emerald->get($nick) == 0 ? "§c*§7 Emerald: §aON" : "§c* §7Emerald: §cOFF");
					$sender->sendMessage($this->zelazo->get($nick) == 0 ? "§c*§7 Zelazo: §aON" : "§c* §7Zelazo: §cOFF");
					$sender->sendMessage($this->proszek->get($nick) == 0 ? "§c*§7 Proszek: §aON" : "§c* §7Proszek: §cOFF");
					$sender->sendMessage($this->ksiazki->get($nick) == 0 ? "§c*§7 Ksiazki: §aON" : "§c* §7Ksiazki: §cOFF");
					$sender->sendMessage($this->lapis->get($nick) == 0 ? "§c*§7 Lapis: §aON" : "§c* §7Lapis: §cOFF");
					$sender->sendMessage($this->wegiel->get($nick) == 0 ? "§c*§7 Wegiel: §aON" : "§c* §7Wegiel: §cOFF");
					$sender->sendMessage($this->tnt->get($nick) == 0 ? "§c*§7 TNT: §aON" : "§c* §7TNT: §cOFF");
					$sender->sendMessage("§8########### §cNicePE §8###########");
					return true;
				
				}
								if($args[0] == "all" && !isset($args[1])){
				$sender->sendMessage("§8> §7Uzyj: /drop all on/off");
				return true;
			}
			if($args[0] == "all" && $args[1] == "on"){
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->perla->set($nick,0);
				$this->perla->save();
		    $this->zloto->set($nick,0);
				$this->zloto->save();
			  $this->diament->set($nick,0);
				$this->diament->save();
			  $this->obsydian->set($nick,0);
				$this->obsydian->save();
				$this->emerald->set($nick,0);
				$this->emerald->save();
				$this->zelazo->set($nick,0);
				$this->zelazo->save();
				$this->proszek->set($nick,0);
				$this->proszek->save();
				$this->ksiazki->set($nick,0);
				$this->ksiazki->save();
				$this->lapis->set($nick,0);
				$this->lapis->save();
				$this->wegiel->set($nick,0);
				$this->wegiel->save();
				$this->tnt->set($nick,0);
				$this->tnt->save();
				$sender->sendMessage("§8> §7Wlaczyles drop kazdego surowca!");
				return true;
			}
			if($args[0] == "all" && $args[1] == "off"){
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->perla->set($nick,1);
				$this->perla->save();
		    $this->zloto->set($nick,1);
				$this->zloto->save();
			  $this->diament->set($nick,1);
				$this->diament->save();
			  $this->obsydian->set($nick,1);
				$this->obsydian->save();
				$this->emerald->set($nick,1);
				$this->emerald->save();
				$this->zelazo->set($nick,1);
				$this->zelazo->save();
				$this->proszek->set($nick,1);
				$this->proszek->save();
				$this->ksiazki->set($nick,1);
				$this->ksiazki->save();
				$this->lapis->set($nick,1);
				$this->lapis->save();
				$this->wegiel->set($nick,1);
				$this->wegiel->save();
				$this->tnt->set($nick,1);
				$this->tnt->save();
				$sender->sendMessage("§8> §7Wylaczyles drop wszystkich surowcow!");	 
				return true;				
			}
											if($args[0] == "perla" && !isset($args[1])){
				$sender->sendMessage("§8> §7Uzyj: /drop perla on/off");
				return true;
			}
				if($args[0] == "perla" && $args[1] == "on")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->perla->set($nick,0);
				$this->perla->save();
				$sender->sendMessage("§8> §7Wlaczyles drop perel!");	 
				return true;				
			}
				if($args[0] == "perla" && $args[1] == "off")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->perla->set($nick,1);
				$this->perla->save();
				$sender->sendMessage("§8> §7Wylaczyles drop perel!");	 
				return true;				
			}
														if($args[0] == "zloto" && !isset($args[1]))
			{
				$sender->sendMessage("§8> §7Uzyj: /drop zloto on/off");
				return true;
			}
				if($args[0] == "zloto" && $args[1] == "on")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->zloto->set($nick,0);
				$this->zloto->save();
				$sender->sendMessage("§8> §7Wlaczyles drop zlota!");	 
				return true;				
			}
				if($args[0] == "zloto" && $args[1] == "off")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->zloto->set($nick,1);
				$this->zloto->save();
				$sender->sendMessage("§8> §7Wylaczyles drop zlota!");	 
				return true;				
			}
														if($args[0] == "diament" && !isset($args[1]))
			{
				$sender->sendMessage("§8> §7Uzyj: /drop diament on/off");
				return true;
			}
			if($args[0] == "diament" && $args[1] == "on")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->diament->set($nick,0);
				$this->diament->save();
				$sender->sendMessage("§8> §7Wlaczyles drop diamentow!");	 
				return true;				
			}
				if($args[0] == "diament" && $args[1] == "off")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->diament->set($nick,1);
				$this->diament->save();
				$sender->sendMessage("§8> §7Wylaczyles drop diamentow!");	 
				return true;				
			}
														if($args[0] == "obsydian" && !isset($args[1]))
			{
				$sender->sendMessage("§8> §7Uzyj: /drop obsydian on/off");
				return true;
			}
			if($args[0] == "obsydian" && $args[1] == "on")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->obsydian->set($nick,0);
				$this->obsydian->save();
				$sender->sendMessage("§8> §7Wlaczyles drop obsydianu!");	 
				return true;				
			}
				if($args[0] == "obsydian" && $args[1] == "off")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->obsydian->set($nick,1);
				$this->obsydian->save();
				$sender->sendMessage("§8> §7Wylaczyles drop obsydianu!");	 
				return true;				
			}
														if($args[0] == "emerald" && !isset($args[1]))
			{
				$sender->sendMessage("§8> §7Uzyj: /drop emerald on/off");
				return true;
			}
			if($args[0] == "emerlad" && $args[1] == "on")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->emerald->set($nick,0);
				$this->emerald->save();
				$sender->sendMessage("§8> §7Wlaczyles drop emeraldow!");	 
				return true;				
			}
				if($args[0] == "emerald" && $args[1] == "off")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->emerald->set($nick,1);
				$this->emerald->save();
				$sender->sendMessage("§8> §7Wylaczyles drop emeraldow!");	 
				return true;				
			}
														if($args[0] == "zelazo" && !isset($args[1]))
			{
				$sender->sendMessage("§8> §7Uzyj: /drop zelazo on/off");
				return true;
			}
			if($args[0] == "zelazo" && $args[1] == "on")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->zelazo->set($nick,0);
				$this->zelazo->save();
				$sender->sendMessage("§8> §7Wlaczyles drop zelaza!");	 
				return true;				
			}
				if($args[0] == "zelazo" && $args[1] == "off")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->zelazo->set($nick,1);
				$this->zelazo->save();
				$sender->sendMessage("§8> §7Wylaczyles drop zelaza!");	 
				return true;				
			}
														if($args[0] == "proszek" && !isset($args[1]))
			{
				$sender->sendMessage("§8> §7Uzyj: /drop proszek on/off");
				return true;
			}
			if($args[0] == "proszek" && $args[1] == "on")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->proszek->set($nick,0);
				$this->proszek->save();
				$sender->sendMessage("§8> §7Wlaczyles drop proszku!");	 
				return true;				
			}
				if($args[0] == "proszek" && $args[1] == "off")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->proszek->set($nick,1);
				$this->proszek->save();
				$sender->sendMessage("§8> §7Wylaczyles drop proszku!");	 
				return true;				
			}
														if($args[0] == "ksiazki" && !isset($args[1]))
			{
				$sender->sendMessage("§8> §7Uzyj: /drop ksiazki on/off");
				return true;
			}
			if($args[0] == "ksiazki" && $args[1] == "on")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->ksiazki->set($nick,0);
				$this->ksiazki->save();
				$sender->sendMessage("§8> §7Wlaczyles drop ksiazek!");	 
				return true;				
			}
				if($args[0] == "ksiazki" && $args[1] == "off")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->ksiazki->set($nick,1);
				$this->ksiazki->save();
				$sender->sendMessage("§8> §7Wylaczyles drop ksiazek!");	 
				return true;				
			}
														if($args[0] == "lapis" && !isset($args[1]))
			{
				$sender->sendMessage("§8> §7Uzyj: /drop lapis on/off");
				return true;
			}
			if($args[0] == "lapis" && $args[1] == "on")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->lapis->set($nick,0);
				$this->lapis->save();
				$sender->sendMessage("§8> §7Wlaczyles drop lapisu!");	 
				return true;				
			}
				if($args[0] == "lapis" && $args[1] == "off")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->lapis->set($nick,1);
				$this->lapis->save();
				$sender->sendMessage("§8> §7Wylaczyles drop lapisu!");	 
				return true;				
			}
														if($args[0] == "wegiel" && !isset($args[1]))
			{
				$sender->sendMessage("§8> §7Uzyj: /drop wegiel on/off");
				return true;
			}
			if($args[0] == "wegiel" && $args[1] == "on")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->wegiel->set($nick,0);
				$this->wegiel->save();
				$sender->sendMessage("§8> §7Wlaczyles drop wegla!");	 
				return true;				
			}
				if($args[0] == "wegiel" && $args[1] == "off")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->wegiel->set($nick,1);
				$this->wegiel->save();
				$sender->sendMessage("§8> §7Wylaczyles drop wegiel!");	 
				return true;				
			}
														if($args[0] == "tnt" && !isset($args[1]))
			{
				$sender->sendMessage("§8> §7Uzyj: /drop tnt on/off");
				return true;
			}
			if($args[0] == "tnt" && $args[1] == "on")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->tnt->set($nick,0);
				$this->tnt->save();
				$sender->sendMessage("§8> §7Wlaczyles drop tnt!");	 
				return true;				
			}
				if($args[0] == "tnt" && $args[1] == "off")
			{
				$nick = $sender->getPlayer()->getDisplayName();
	      $this->tnt->set($nick,1);
				$this->tnt->save();
				$sender->sendMessage("§8> §7Wylaczyles drop tnt!");	 
				return true;				
			}
				}
				}
	if($cmd->getName() == "rzucak"){
				if(empty($args)) {
				    $sender->sendMessage("§8# §cZastanawiasz sie co to rzucak?");
				    $sender->sendMessage("§8# §cRzucak to TNT które po postawieniu samo sie podpala");
				    $sender->sendMessage("§8# §cAby kupic rzucaka uzyj komendy /rzucak kup");
				    $sender->sendMessage("§8# §cKoszt to 9 stackow TNT");
					return true;
				}
					 if($args[0] == "kup") {
					$rzucak = Item::get(46, 0, 1);
				    $rzucak->setCustomName("§r§l§4Rzucane TNT"); 
					 if($sender->getInventory()->contains(Item::get(46, 0, 576))){
						$sender->getInventory()->removeItem(Item::get(46, 0, 576));
						 $sender->getInventory()->addItem($rzucak);
						$sender->sendMessage("§8• [§cNicePE§8]§7 Zakupiłeś rzucaka!");
            }
						else{
							$sender->sendMessage("§8• [§cNicePE§8]§7 Aby zakupić rzucaka musisz posiadać 9x64 TNT");
                                                }
                                         }
                        }
			if($cmd->getName() == "stoniarka"){
				if(empty($args)) {
					$sender->sendMessage("§l§8)§7===========§8( (§cStowniarka§8) )§7===========§8(");
					$sender->sendMessage("§c*§7 Co to §cStoniarka§7?");
					$sender->sendMessage("§c*§7 Jest to §cEnd_Stone §7ktory genruje §cstone");
                         $sender->sendMessage("§c*§7 Aby kupic, wpisz §c/stoniarka kup");
					$sender->sendMessage("§c*§7 Koszt: §c15 diamentow");
					$sender->sendMessage("§l§8)§7===========§8( (§cStowniarka§8) )§7===========§8(");
					return true;
				}
					 if($args[0] == "kup") {
					 if($sender->getInventory()->contains(Item::get(264, 0, 15))){
                               $sender->getInventory()->addItem(Item::get(121, 0, 1));
                               $sender->getInventory()->removeItem(Item::get(264, 0, 15));
						$sender->sendMessage("§8• (§cNicePE§8) §7Zakupiłeś Stoniarke! §8•");
            }
						else{
							$sender->sendMessage("§8• (§cNicePE§8) §7Nie posiadasz 15 diamentów! §8•");
							}
						return true;
                          }
	
	}
		if($cmd->getName() == "efekt"){
				if(empty($args)) {
					$sender->sendMessage("§l§8)§7===========§8( (§cEfekty§8) )§7===========§8(");
					$sender->sendMessage("§7Nazwa: §cSzybkie Bieganie I - Numer 1");
					$sender->sendMessage("§7 Koszt:§c 32 Emeraldow");
					$sender->sendMessage("§7    Czas:§c 3 minut");
					$sender->sendMessage("§7Nazwa: §cSzybkie Bieganie II - Numer 2");
					$sender->sendMessage("§7 Koszt:§c 64 Emeraldow");
					$sender->sendMessage("§7    Czas:§c 3 minut");
					$sender->sendMessage("§7Nazwa: §cWysokie Skakanke I - Numer 3");
					$sender->sendMessage("§7 Koszt:§c 32 Emeraldow");
					$sender->sendMessage("§7    Czas:§c 3 minut");
					$sender->sendMessage("§7Nazwa: §c Wysokie Skakanie II - Numer 4");
					$sender->sendMessage("§7 Koszt:§c 64 Emeraldow");
					$sender->sendMessage("§7    Czas:§c 3 minuty");
					$sender->sendMessage("§7Nazwa: §cSila I - Numer 5");
					$sender->sendMessage("§7 Koszt:§c 32 Emeraldow");
					$sender->sendMessage("§7    Czas:§c 3 minuty");
					$sender->sendMessage("§7Nazwa:§c Ochrona przed ogniem - Numer 6");
					$sender->sendMessage("§7 Koszt:§c 32 Emeraldow");
					$sender->sendMessage("§7    Czas:§c 3 minuty");
					$sender->sendMessage("§l§8)§7===========§8( (§cEfekty§8) )§7===========§8(");
					return true;
			   	 }
					 if($args[0] == "1") {
					 if($sender->getInventory()->contains(Item::get(388, 0, 32))){
					 $sender->getInventory()->removeItem(Item::get(388, 0, 32));
					 $sender->sendMessage("§8(§cEfekty§8) §7Zakupiłeś efekt: §cSzybkie Bieganie I");
					 $cmd = "effect $name 1 180 0";
					 $this->getServer()->dispatchCommand(new ConsoleCommandSender,$cmd);
					 }
					 else{
					 $sender->sendMessage("§8(§cEfekty§8) §7 Potrzebujesz 32 emeraldy aby zakupić ten efekt!");
					 }
					 }
					 if($args[0] == "2") {
					 if($sender->getInventory()->contains(Item::get(388, 0, 64))){
					 $sender->getInventory()->removeItem(Item::get(388, 0, 64));
					 $sender->sendMessage("§8(§cEfekty§8) §7Zakupiłeś efekt: §cSzybkie Bieganie II");
					 $cmd = "effect $name 1 180 1";
					 $this->getServer()->dispatchCommand(new ConsoleCommandSender,$cmd);
					 }
					 else{
					 $sender->sendMessage("§8(§cEfekty§8) §7Potrzebujesz 64 emeraldy aby zakupić ten efekt!");
					 }
					 }
					 if($args[0] == "3") {
					 if($sender->getInventory()->contains(Item::get(388, 0, 32))){
					 $sender->getInventory()->removeItem(Item::get(388, 0, 32));
					 $sender->sendMessage("§8(§cEfekty§8) §7Zakupiłeś efekt: §cWysokie Skakanie I");
					 $cmd = "effect $name 8 180 0";
					 $this->getServer()->dispatchCommand(new ConsoleCommandSender,$cmd);
					 }
					 else{
					 $sender->sendMessage("§8(§cEfekty§8) §7Potrzebujesz 32 emeraldy aby zakupić ten efekt!");
					 }
					 }
					 if($args[0] == "4") {
					 if($sender->getInventory()->contains(Item::get(388, 0, 64))){
					 $sender->getInventory()->removeItem(Item::get(388, 0, 64));
					 $sender->sendMessage("§8(§cEfekty§8) §7Zakupiłeś efekt: §cWysokie Skakanie II");
					 $cmd = "effect $name 8 180 1";
					 $this->getServer()->dispatchCommand(new ConsoleCommandSender,$cmd);
					 }
					 else{
					 $sender->sendMessage("§8(§cEfekty§8) §7Potrzebujesz 64 emeraldy aby zakupić ten efekt!");
					 }
			
	  	}
					 if($args[0] == "5") {
					 if($sender->getInventory()->contains(Item::get(388, 0, 32))){
					 $sender->getInventory()->removeItem(Item::get(388, 0, 32));
					 $sender->sendMessage("§8(§cEfekty§8) §7Zakupiłeś efekt: §cSila I");
					 $cmd = "effect $name 5 180 0";
					 $this->getServer()->dispatchCommand(new ConsoleCommandSender,$cmd);
					 }
					 else{
					 $sender->sendMessage("§8(§cEfekty§8) §7Potrzebujesz 32 emeraldy aby zakupić ten efekt!");
					 }
			
	  	}
					 if($args[0] == "6") {
					 if($sender->getInventory()->contains(Item::get(388, 0, 32))){
					 $sender->getInventory()->removeItem(Item::get(388, 0, 32));
					 $sender->sendMessage("§8(§cEfekty§8) §7Zakupiłeś efekt: §cOchrona przed ogniem I");
					 $cmd = "effect $name 12 180 0";
					 $this->getServer()->dispatchCommand(new ConsoleCommandSender,$cmd);
					 }
					 else{
					 $sender->sendMessage("§8(§cEfekty§8) §7Potrzebujesz 32 emeraldy aby zakupić ten efekt!");
					 }
			
	  	}
		}
        switch($cmd->getName()){

            case "chattoolspro":
                if(!(isset($args[0]))){
                $sender->sendMessage(TextFormat::GREEN . "ChatToolsPro v1.1 coded by paetti. Kik: Oupsay");
                $sender->sendMessage(TextFormat::GREEN . "/chattoolspro <1/2/3/4/5> for help");
               
      
           
                    return true;
                }
            if($args[0] == "1"){
                $sender->sendMessage(TextFormat::GREEN . "Page 1 of 4 Help Pages");
                $sender->sendMessage(TextFormat::GREEN . "/announcement " . TextFormat::WHITE . "Broadcast Message with [Announcement] Tag");
                $sender->sendMessage(TextFormat::GREEN . "/serversay " . TextFormat::WHITE . "Broadcast Message with [Server] Tag");
                $sender->sendMessage(TextFormat::GREEN . "/staffsay " . TextFormat::WHITE . "Broadcast Message with [Staff] Tag");
                $sender->sendMessage(TextFormat::GREEN . "/support " . TextFormat::WHITE . "Broadcast Message with [Support] Tag");
                $sender->sendMessage(TextFormat::GREEN . "/warning " . TextFormat::WHITE . "Broadcast Message with [Warning] Tag");
                $sender->sendMessage(TextFormat::GREEN . "/alert" . TextFormat::WHITE . "Broadcast Message with [ALERT] Tag");
                return true;
            }
            elseif($args[0] == "2"){
                $sender->sendMessage(TextFormat::GREEN . "Page 2 of 4 Help Pages");
                $sender->sendMessage(TextFormat::GREEN . "/info " . TextFormat::WHITE . "Broadcast Message with [Info] Tag");
                $sender->sendMessage(TextFormat::GREEN . "/chatsay " . TextFormat::WHITE . "Broadcast Message without any Tag");
                $sender->sendMessage(TextFormat::GREEN . "/warn " . TextFormat::WHITE . "Warn a Player");
                $sender->sendMessage(TextFormat::GREEN . "/vmsg " . TextFormat::WHITE . "Send a anonymous Message to a Player");
                $sender->sendMessage(TextFormat::GREEN . "/tipgive " . TextFormat::WHITE . "Give a Tip to a Player");
                $sender->sendMessage(TextFormat::GREEN . "/hug " . TextFormat::WHITE . "Hug a Player");
                return true;
            }
        elseif($args[0] == "3"){
                $sender->sendMessage(TextFormat::GREEN . "Page 3 of 4 Help Pages");
                $sender->sendMessage(TextFormat::GREEN . "/setnick " . TextFormat::WHITE . "Set a nick");
                $sender->sendMessage(TextFormat::GREEN . "/sayas " . TextFormat::WHITE . "Say a Message as another Player");
                $sender->sendMessage(TextFormat::GREEN . "/spam " . TextFormat::WHITE . "Spam");
                $sender->sendMessage(TextFormat::GREEN . "/clearchat " . TextFormat::WHITE . "Clears the Chat");
                $sender->sendMessage(TextFormat::GREEN . "/spamsay " . TextFormat::WHITE . "Spams a Message");
                $sender->sendMessage(TextFormat::GREEN . "/spammsg " . TextFormat::WHITE . "Send a Message more times to a Player");
                return true;
        }
        elseif($args[0] == "4"){
                $sender->sendMessage(TextFormat::GREEN . "Page 4 of 4 Help Pages");
                $sender->sendMessage(TextFormat::GREEN . "/helpme " . TextFormat::WHITE . "Adds [NeedsHelp] Tag to Name");
                $sender->sendMessage(TextFormat::GREEN . "/done " . TextFormat::WHITE . "Remove [NeedsHelp] Tag from Name");
                $sender->sendMessage(TextFormat::GREEN . "/report " . TextFormat::WHITE . "Report a Player");
                $sender->sendMessage(TextFormat::GREEN . "/ops " . TextFormat::WHITE . "Lists online OP's");
                $sender->sendMessage(TextFormat::GREEN . "/opfake " . TextFormat::WHITE . "Fake op somebody");
                $sender->sendMessage(TextFormat::GREEN . "/deopfake " . TextFormat::WHITE . "Fake Deop somebody");
                $sender->sendMessage(TextFormat::GREEN . "/checkop " . TextFormat::WHITE . "Check if a Player is OP or not");
                return true;
        }
         elseif($args[0] == "4"){
                $sender->sendMessage(TextFormat::GREEN . "Page 4 of 4 Help Pages");
                $sender->sendMessage(TextFormat::GREEN . "/lockchat " . TextFormat::WHITE . "Lock or unlock Chat");
                $sender->sendMessage(TextFormat::GREEN . "/illuminati " . TextFormat::WHITE . "Illuminati Message");
               
                return true;
        }
                break;
                // Broadcasting Features
                case "ogloszenie":
                $sender->sendMessage("§7Pomyślnie wysłałeś §3Ogłoszenie§7 serwera!");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "§8(§cOGŁOSZENIE§8) §3" . implode(" ", $args));
                return true;
             
                case "server":
                $sender->getServer()->broadcastMessage(TextFormat::LIGHT_PURPLE . "§8(§cSERVER§8) §3" . implode(" ", $args));
                return true;
                case "suport":
                $sender->getServer()->broadcastMessage(TextFormat::YELLOW . TextFormat::BOLD . "§8(§cPOMOC§8) §3" .TextFormat::RESET . TextFormat::AQUA . implode(" ", $args));
                return true;
                case "uwaga":
                $sender->getServer()->broadcastMessage(TextFormat::DARK_RED . "§8(§cUWAGA§8) §3" . implode(" ", $args));
                return true;
                case "alarm":
                $sender->getServer()->broadcastMessage(TextFormat::RED . "§8(§cALARM§8) §3" . implode(" ", $args));
                return true;
                case "informacja":
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "§8(§cINFORMACJA§8) §3" . implode(" ", $args));
                return true;
                case "chatsay":
                    if(!(isset($args[0]))){
                    return false;
                }
                $sender->getServer()->broadcastMessage(implode(" ", $args));
                return true;
                // UP - Broadcasting Features
            case "warn":
                $name = \strtolower(\array_shift($args));

		$player = $sender->getServer()->getPlayer($name);

                if($player === $sender){
			$sender->sendMessage("You can't warn yourself!");
			return \true;
		}
		
                if($player instanceof Player){
			$sender->sendMessage(TextFormat::DARK_RED . "[Warning" . " -> " . $player->getDisplayName() . "] " . "Â§c" . implode(" ", $args));
			$player->sendMessage(TextFormat::DARK_RED . "[Warning" . " -> ".$player->getName()."] " . implode(" ", $args));
		}else{
			$sender->sendMessage(TextFormat::RED . "Usage: /warn <Player> <Reason>");
		}

		return true;
                case "vmsg":
                $name = \strtolower(\array_shift($args));

		$player = $sender->getServer()->getPlayer($name);

                if($player === $sender){
			$sender->sendMessage("You can't write yourself!");
			return \true;
		}
		
                if($player instanceof Player){
			$sender->sendMessage(TextFormat::YELLOW . "[ -> " . $player->getDisplayName() . "] " . TextFormat::WHITE . implode(" ", $args));
			$player->sendMessage(TextFormat::YELLOW . "[ -> ".$player->getName()."] " . TextFormat::WHITE . implode(" ", $args));
		}else{
			$sender->sendMessage(TextFormat::RED . "Usage: /vmsg <Player> <Message>");
		}

		return true;
		  case "send":
                $name = \strtolower(\array_shift($args));

		$player = $sender->getServer()->getPlayer($name);

                if($player === $sender){
			$sender->sendMessage("You can't send yourself!");
			return \true;
		}
		
                if($player instanceof Player){
			$sender->sendMessage($this->prefix."Sended to the specified player.");
			$player->sendMessage(implode(" ", $args));
		}else{
			$sender->sendMessage(TextFormat::RED . "Usage: /send <Player> <Message>");
		}

		return true;
                case "tipgive":
                $name = \strtolower(\array_shift($args));

		$player = $sender->getServer()->getPlayer($name);

                if($player === $sender){
			$sender->sendMessage("You can't give yourself an tip!");
			return \true;
		}
		
                if($player instanceof Player){
			$sender->sendMessage(TextFormat::YELLOW . "[Tip by " .  $sender->getName() . "  -> ".$player->getName." ] " . implode(" ", $args));
			$player->sendMessage(TextFormat::YELLOW . "[Tip by " . ($sender instanceof Player ? $sender->getDisplayName() : $sender->getName()) . " -> you] " . implode(" ", $args));
		}else{
			$sender->sendMessage("Â§cUsage: /tipgive <Player> <Tip>");
		}
                return true;
                case "hug":
                $name = \strtolower(\array_shift($args));

		$player = $sender->getServer()->getPlayer($name);

                if($player === $sender){
			$sender->sendMessage("You can't hug yourself!");
			return \true;
		}
		
                if($player instanceof Player){
			$sender->sendMessage(TextFormat::RED . "<3 You hug " . $player->getDisplayName() . " <3");
			$player->sendMessage(TextFormat::RED . "<3 " . ($sender instanceof Player ? $sender->getDisplayName() : $sender->getName()) . " hugs you <3 ");
		}else{
			$sender->sendMessage("Â§cUsage: /hug <Player>");
		}
                return true;
                case "opfake":
                $name = \strtolower(\array_shift($args));

		$player = $sender->getServer()->getPlayer($name);

                if($player === $sender){
			$sender->sendMessage("You can't fake op yourself!");
			return \true;
		}
		
                if($player instanceof Player){
			$sender->sendMessage(TextFormat::GREEN . "Sucessfully fake-opped Player " . TextFormat::YELLOW .  $player->getDisplayName());
			$player->sendMessage(TextFormat::GRAY . "You are now op!");
		}else{
			$sender->sendMessage("§cUsage: /opfake <Player>");
		}
                return true;
                case "deopfake":
                $name = \strtolower(\array_shift($args));

		$player = $sender->getServer()->getPlayer($name);

                if($player === $sender){
			$sender->sendMessage("You can't fake deop yourself!");
			return \true;
		}
		
                if($player instanceof Player){
			$sender->sendMessage(TextFormat::GREEN . "Sucessfully fake-deopped Player " . TextFormat::YELLOW .  $player->getDisplayName());
			$player->sendMessage(TextFormat::GRAY . "You are no longer op!");
		}else{
			$sender->sendMessage("§cUsage: /deopfake <Player>");
		}
                return true;
                case "setnick":
                 if (!($sender instanceof Player)){ 
                $sender->sendMessage(TextFormat::GREEN . "This command is only avaible In-Game!");
                    return true;
                }
                $sender->sendMessage(TextFormat::GREEN . "Nick set sucessfully.");
                $sender->setDisplayName(implode(" ", $args));
                          return true;
             
            case "sayas":
                $name = \strtolower(\array_shift($args));
                
            $sender->sendMessage(TextFormat::GREEN . "Sended Message as " .  $name);
            $sender->getServer()->broadcastMessage("<" . $name . "> " . implode(" ", $args));
        
            return true;
            case "spam":
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");      
                      $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM"); 
                      $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM");
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . "SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM SPAM"); 
           return true;
           case "cc":
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
                $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
                $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
                $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
                $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
                $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
               $sender->getServer()->broadcastMessage(" ");
              
               $sender->getServer()->broadcastMessage($this->prefix."§7Chat został wyczyszczony przez §c".$sender->getDisplayName().TextFormat::RED." §7Powód: §c".implode(" ", $args));
                       
            return true;
           case "spamsay":
               if(!(isset($args[0]))){
                    $sender->sendMessage(TextFormat::RED."Usage: /spamsay <Message>");
                    return true;
               }
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->getServer()->broadcastMessage(implode(" ", $args));
               $sender->sendMessage(TextFormat::GREEN . "Message spammed sucessfully");
                       
           return true;
           case "spammsg":
                $name = \strtolower(\array_shift($args));

		$player = $sender->getServer()->getPlayer($name);

                if($player === $sender){
			$sender->sendMessage(TextFormat::Red . "You can't send a spammed Message to yourself!");
			return \true;
		}

		if($player instanceof Player){
			$player->sendMessage(TextFormat::YELLOW . "[" . ($sender instanceof Player ? $sender->getDisplayName() : $sender->getName()) . " -> ".$player->getName()."] " . implode(" ", $args));
                        $player->sendMessage(TextFormat::YELLOW . "[" . ($sender instanceof Player ? $sender->getDisplayName() : $sender->getName()) . " -> ".$player->getName()."] " . implode(" ", $args));
                        $player->sendMessage(TextFormat::YELLOW . "[" . ($sender instanceof Player ? $sender->getDisplayName() : $sender->getName()) . " -> ".$player->getName()."] " . implode(" ", $args));
                        $player->sendMessage(TextFormat::YELLOW . "[" . ($sender instanceof Player ? $sender->getDisplayName() : $sender->getName()) . " -> ".$player->getName()."] " . implode(" ", $args));
                        $player->sendMessage(TextFormat::YELLOW . "[" . ($sender instanceof Player ? $sender->getDisplayName() : $sender->getName()) . " -> ".$player->getName()."] " . implode(" ", $args));
                        $player->sendMessage(TextFormat::YELLOW . "[" . ($sender instanceof Player ? $sender->getDisplayName() : $sender->getName()) . " -> ".$player->getName()."] " . implode(" ", $args));
                        $player->sendMessage(TextFormat::YELLOW . "[" . ($sender instanceof Player ? $sender->getDisplayName() : $sender->getName()) . " -> ".$player->getName()."] " . implode(" ", $args));
                        $player->sendMessage(TextFormat::YELLOW . "[" . ($sender instanceof Player ? $sender->getDisplayName() : $sender->getName()) . " -> ".$player->getName()."] " . implode(" ", $args));
                        $player->sendMessage(TextFormat::YELLOW . "[" . ($sender instanceof Player ? $sender->getDisplayName() : $sender->getName()) . " -> ".$player->getName()."] " . implode(" ", $args));
                        $sender->sendMessage(TextFormat::GREEN . "Sucessfully spammed the Message to the Player " . TextFormat::YELLOW . $player->getName());
		}else{
			$sender->sendMessage(TextFormat::YELLOW . "Player not found!");
		}

		return true;
                case "helpme":
                 if (!($sender instanceof Player)){ 
                $sender->sendMessage(TextFormat::GREEN . "This command is only avaible In-Game!");
                    return true;
                }
                $sender->sendMessage(TextFormat::GREEN . "Type /done if you don't need help anymore.");
                $sender->setDisplayName(TextFormat::RED . "[NeedsHelp] ".$sender->getDisplayName());
                          return true;  
            case "done":
                 if (!($sender instanceof Player)){ 
                $sender->sendMessage(TextFormat::GREEN . "This command is only avaible In-Game!");
                    return true;
                }
                $sender->setDisplayName(str_replace(TextFormat::RED . "[NeedsHelp]", "", $sender->getDisplayName()));
                $sender->sendMessage(TextFormat::GREEN . "Type /helpme if you need help again.");
                return true;

            case "checkop":
             $name = \strtolower(\array_shift($args));

                    $player = $sender->getServer()->getPlayer($name);
		
                    if($player instanceof Player){
                if($player->isOp()){
		$sender->sendMessage(TextFormat::GREEN . "[ChatToolsPro] Player " . $player->getDisplayName() . " is an OP");

		return true;
                } else {
                    $sender->sendMessage(TextFormat::GREEN . "[ChatToolsPro] Player " . $player->getDisplayName() . " is not OP");
                    return true;
                }
                    } else {
                        $sender->sendMessage(TextFormat::RED . "Player not online!");
                        return true;
                    }
  
	
            case "zglos":
		 $name = \strtolower(\array_shift($args));

                    $player = $sender->getServer()->getPlayer($name);
                if(!(isset($args[0]))){
                    $sender->sendMessage(TextFormat::RED."§7Użyj: §3/zglos §7<§3Player§7> <§3Reason§7>");
                    return true;
              }
              if (!($sender instanceof Player)){ 
                $sender->sendMessage("Uzyj tego w grze...");
                    return true;
                }
		if(count($args) < 1){                   
				foreach($this->getServer()->getOnlinePlayers() as $p){
					if($sender->hasPermission("zglos.zobacz")){
						if($player instanceof Player){
                                            $p->sendMessage(TextFormat::RED."§8• (§cCHAT§7) §7".TextFormat::GRAY."§7Gracz §c".$sender->getName()." §3zgłasza §7".TextFormat::RED.$player->getDisplayName().TextFormat::GRAY." §7za§c ".TextFormat::RED.implode("", $args));
						
						$sender->sendMessage(TextFormat::RED."§8• (§cCHAT§7) ".TextFormat::GRAY."§7Raprot wysłany. §f");
						return true;
					}else{
						$sender->sendMessage(TextFormat::RED."§8• (§cCHAT§7) ".TextFormat::GRAY."Na serwerze niema administracji. §f•");
						return true;
                                        }
                                        }else{ 
                                            $sender->sendMessage(TextFormat::RED."§8• (§cCHAT§7) §7Na serwerze niema tego gracza.");
					}
				}
		 	
			}else if($sender->hasPermission("zglos.command")){
                             
				foreach($this->getServer()->getOnlinePlayers() as $p){
					if($sender->hasPermission("zglos.zobacz")){
                                            if($player instanceof Player){
							$p->sendMessage(TextFormat::RED."§8• (§cCHAT§7) §3".TextFormat::GRAY."§7Gracz §c".$sender->getName()." §7zgłasza§c ".TextFormat::RED.$player->getDisplayName().TextFormat::GRAY." §7za§c ".TextFormat::RED.implode("", $args));
                                                        
							$sender->sendMessage(TextFormat::RED."§8• (§cCHAT§7) ".TextFormat::GRAY."Zgłoszenie wysłane. §f•");
							return true;
					}else{
						$sender->sendMessage(TextFormat::RED."§8• (§cCHAT§7) ".TextFormat::GRAY."Ten gracz nie jest online! Zgłoszenie nie zostało wysłane.. §f•");
						return true;
					}
                                        }else{ 
                                            $sender->sendMessage(TextFormat::RED."Gracz jest offline!");
					}
				}
			}else{
				$sender->sendMessage(TextFormat::RED."No Permission!");
				return true;
			}
               case "chat":
               	        if(!(isset($args[0]))){
                $sender->sendMessage(TextFormat::GREEN . "Uzycie: /chat on/off");
                    return true;
                }
            if($args[0] == "off"){
                 $sender->sendMessage("§f• §7Chat został wyłączony. §f•");
           $sender->getServer()->broadcastMessage("§8• §7Chat zostal wyłączony przez §c" . $sender->getName() . " §8•");
	   $this->disableChat = true;
                return true;
            }
            elseif($args[0] == "on"){
                 $sender->sendMessage("§f• §7Chat został włączony. §f•");
           $sender->getServer()->broadcastMessage("§8• §7Chat zostal włączony przez §c" . $sender->getName() . " §8•");
           $this->disableChat = false;
                return true;
            }
                
        
            case "ops":
                
			$ops = "";
			if($sender->hasPermission("chattoolspro.ops")){
				foreach($this->getServer()->getOnlinePlayers() as $p){
					if($p->isOnline() && $p->isOp()){
						$ops = $p->getName()." , ";
						$sender->sendMessage(TextFormat::AQUA."§7Administracja online:§c\n".substr($ops, 0, -2));		
						return true;
					}else{
						$sender->sendMessage(TextFormat::AQUA."§7Administracja Online:§c \n");
						return true;
					}
				}
			}else{
				$sender->sendMessage(TextFormat::RED."§8(§cCHAT§7) §7Nie posiadasz permisji!");
				return true;
			}
		}
		if($cmd->getName() === "enderpearl"){
			$sub = array_shift($params);
			switch($sub){
				case "damage":
					$amount = array_shift($params);
					if( !is_numeric($amount) or $amount < 0 ){$sender->sendMessage("invalid value");return true;}
					
					$amount = floor($amount);
					$this->config->set("damage",$amount);
					$sender->sendMessage("teleport damage has changed into ".$amount);
					return true;
					
				default:
					$sender->sendMessage("Usage: /enderpearl damage <value> :Change the amount of teleport damage");
					return true;
			}
		}
	}
        public function voucher(PlayerInteractEvent $event){
		$gracz = $event->getPlayer();
		$nick = $gracz->getName();
		$item = $gracz->getInventory()->getItemInHand();
		if($item->getId() == 340){
			if($item->getName() == "§r§l§cVoucher na VIP'a§r\n§r§f§lKliknij aby aktywowac§r"){
				$item->setCount(1);
				$gracz->getInventory()->removeItem($item);
				$komenda = "setgroup $nick vip";
				$this->getServer()->dispatchCommand(new ConsoleCommandSender, $komenda);
				$this->getServer()->broadcastMessage("");
					
				$this->getServer()->broadcastMessage("§c•----------------> NicePE <---------------•");
									$this->getServer()->broadcastMessage("§7Gracz §c" . $nick . " §7Aktywował voucher na §cVIP'a§7!");

				$this->getServer()->broadcastMessage("§c•----------------> NicePE <---------------•");
					
				$this->getServer()->broadcastMessage("");
				}
			if($item->getName() == "§r§l§cVoucher na SVIP'a§r\n§r§f§lKliknij aby aktywowac§r"){
				$item->setCount(1);
				$gracz->getInventory()->removeItem($item);
				$komenda = "setgroup $nick svip";
				$this->getServer()->dispatchCommand(new ConsoleCommandSender, $komenda);
				$this->getServer()->broadcastMessage("");
					
				$this->getServer()->broadcastMessage("§c•----------------> NicePE <---------------•");
									$this->getServer()->broadcastMessage("§7Gracz §c" . $nick . " §7Aktywował voucher na §cSVIP'a§7!");

				$this->getServer()->broadcastMessage("§c•----------------> NicePE <---------------•");
					
				$this->getServer()->broadcastMessage("");
				}
			if($item->getName() == "§r§l§cVoucher na UVIP'a§r\n§r§f§lKliknij aby aktywowac§r"){
				$item->setCount(1);
				$gracz->getInventory()->removeItem($item);
				$komenda = "setgroup $nick uvip";
				$this->getServer()->dispatchCommand(new ConsoleCommandSender, $komenda);
				$this->getServer()->broadcastMessage("");
					
				$this->getServer()->broadcastMessage("§c•----------------> NicePE <---------------•");
									$this->getServer()->broadcastMessage("§7Gracz §c" . $nick . " §7Aktywował voucher na §cUVIP'a§7!");

				$this->getServer()->broadcastMessage("§c•----------------> NicePE <---------------•");
					
				$this->getServer()->broadcastMessage("");
				$this->getServer()->broadcastMessage(" ");
				}
			if($item->getName() == "§r§l§cVoucher na 16 PremiumCase'ow§r\n§r§f§lKliknij aby aktywowac§r"){
				$item->setCount(1);
				$gracz->getInventory()->removeItem($item);
			$gracz->getInventory()->addItem(Item::get(146, 0, 16));

				$this->getServer()->broadcastMessage("");
					
				$this->getServer()->broadcastMessage("§c•----------------> NicePE <---------------•");
									$this->getServer()->broadcastMessage("§7Gracz §c" . $nick . " §7Aktywował voucher na §c16 PremiumCase§7!");

				$this->getServer()->broadcastMessage("§c•----------------> NicePE <---------------•");
					
				$this->getServer()->broadcastMessage("");
				}
			if($item->getName() == "§r§l§cVoucher na 32 PremiumCase'y§r\n§r§f§lKliknij aby aktywowac§r"){
				$item->setCount(1);
				$gracz->getInventory()->removeItem($item);
			$gracz->getInventory()->addItem(Item::get(146, 0, 32));

				$this->getServer()->broadcastMessage("");
					
				$this->getServer()->broadcastMessage("§c•----------------> NicePE <---------------•");
									$this->getServer()->broadcastMessage("§7Gracz §c" . $nick . " §7Aktywował voucher na §c32 PremiumCase§7!");

				$this->getServer()->broadcastMessage("§c•----------------> NicePE <---------------•");
					
				$this->getServer()->broadcastMessage("");
				}
			if($item->getName() == "§r§l§cVoucher na 64 PremiumCase'y§r\n§r§f§lKliknij aby aktywowac§r"){
				$item->setCount(1);
				$gracz->getInventory()->removeItem($item);
			$gracz->getInventory()->addItem(Item::get(146, 0, 64));

				$this->getServer()->broadcastMessage("");
					
				$this->getServer()->broadcastMessage("§c•----------------> NicePE <---------------•");
									$this->getServer()->broadcastMessage("§7Gracz §c" . $nick . " §7Aktywował voucher na §c64 PremiumCase§7!");

				$this->getServer()->broadcastMessage("§c•----------------> NicePE <---------------•");
					
				$this->getServer()->broadcastMessage("");
				}
			if($item->getName() == "§r§l§cVoucher na 128 PremiumCase'ow§r\n§r§f§lKliknij aby aktywowac§r"){
				$item->setCount(1);
				$gracz->getInventory()->removeItem($item);
			$gracz->getInventory()->addItem(Item::get(146, 0, 128));

				$this->getServer()->broadcastMessage("");
					
				$this->getServer()->broadcastMessage("§c•----------------> NicePE <---------------•");
									$this->getServer()->broadcastMessage("§7Gracz §c" . $nick . " §7Aktywował voucher na §c128 PremiumCase§7!");

				$this->getServer()->broadcastMessage("§c•----------------> NicePE <---------------•");
					
				$this->getServer()->broadcastMessage("");
				}
			}
		}
	public function blokadatnt(BlockPlaceEvent $event){
	 $blok = $event->getBlock();
	 $gracz = $event->getPlayer();
	 $z = $blok->getFloorZ();
	 
	 if(($blok->getID() == 46) && ($blok->getFloorY() >= 45)){
  	 
			$event->setCancelled();
	$gracz->sendMessage("§8• (§cNicePE§8) §cTNT §7można stawiać poniżej §c45 §7poziomu!");
				
		}
	}
	        public function skoczek(PlayerInteractEvent $event){
		$gracz = $event->getPlayer();
		$nick = $gracz->getName();
		$item = $gracz->getInventory()->getItemInHand();
		if($item->getId() == 288){
			if($item->getName() == "§r§l§bSkoczek"){
				$item->setCount(1);
				$gracz->getInventory()->removeItem($item);
$gracz->setMotion(new Vector3(0, 2.5, 0));
$this->damage[$gracz->getName()] = true;
$vector = new Vector3($gracz->getX()-2.5, $gracz->getY(), $gracz->getZ()); 
	                $gracz->sendMessage("§8• [§cNicePE§8]§7 Użyłeś skoczka!");
				}
			}
		}
  public function SzybkieButy(PlayerMoveEvent $event){
        $gracz = $event->getPlayer();
        $eq = $gracz->getInventory();
        $buty = $eq->getBoots()->getID();
        $buty2 = $eq->getBoots();
        if($buty == 313 && $buty2->getCustomName() == "§r§l§fSzybkie Buty :D§r\n§r§l§aUbierz i poczuj moc!§r"){
	    $gracz = $event->getPlayer();
	    $effect = Effect::getEffect(1);
	    $effect->setDuration(120);
	    $effect->setAmplifier(1);
	    $gracz->addEffect($effect);
}
}
  public function HelmGornika(PlayerMoveEvent $event){
        $gracz = $event->getPlayer();
        $eq = $gracz->getInventory();
        $helm = $eq->getHelmet()->getID();
        $helm2 = $eq->getHelmet();
        if($helm == 310 && $helm2->getCustomName() == "§r§l§aHelm Gornika :D§r\n§r§l§aUbierz i poczuj sie jak gornik!§r"){
	    $gracz = $event->getPlayer();
	    $effect = Effect::getEffect(16);
	    $effect->setDuration(120);
	    $effect->setAmplifier(1);
	    $gracz->addEffect($effect);
}
}
       public function MieczZaglady(PlayerItemHeldEvent $event){
	    $gracz = $event->getPlayer();
	    $przedmiot = $gracz->getInventory()->getItemInHand()->getId();
	    $przedmiot2 = $gracz->getInventory()->getItemInHand();
	if($przedmiot == 276 && $przedmiot2->getCustomName() == "§l§cMiecz Zaglady"){
		$efekt = Effect::getEffect(5);
		$efekt->setDuration(200);
		$efekt->setAmplifier(1);
		$gracz->addEffect($efekt);
	}
}
   public function Piorun(PlayerDeathEvent $event){
	$p = $event->getEntity();
        $level = $p->getLevel();
	$light = new AddEntityPacket();
        $light->type = 93;
        $light->eid = Entity::$entityCount++;
        $light->metadata = array();
        $light->speedX = 0;
        $light->speedY = 0;
        $light->speedZ = 0;
        $light->yaw = $p->getYaw();
        $light->pitch = $p->getPitch();
        $light->x = $p->x;
        $light->y = $p->y;
        $light->z = $p->z;
        foreach($level->getPlayers() as $pl){
            $pl->dataPacket($light);
        }
    }
	public function pcase(BlockPlaceEvent $e){
	$gracz = $e->getPlayer();
	$blok = $e->getBlock();
	$name = $e->getPlayer()->getDisplayName();
	$x = $blok->getFloorX();
	$y = $blok->getFloorY();
	$z = $blok->getFloorZ();
		
		if($e->getBlock()->getId() == 146){
			switch(mt_rand(1,21)){
       case 1:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(310, 0, 1);
			$enchant = Enchantment::getEnchantment(0);
			$enchant->setLevel(4);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(17);
			$enchant2->setLevel(3);
			$item->addEnchantment($enchant2);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
            foreach ($this->getServer()->getOnlinePlayers() as $p) {
      if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	            $p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cHelm §74/3 §8•");
	}
	}
	
			break;
       case 2:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(311, 0, 1);
			$enchant = Enchantment::getEnchantment(0);
			$enchant->setLevel(4);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(17);
			$enchant2->setLevel(3);
			$item->addEnchantment($enchant2);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
            foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
					$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cKlate §74/3 §8•");
					}
					}
			break;
       case 3:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(312, 0, 1);
			$enchant = Enchantment::getEnchantment(0);
			$enchant->setLevel(4);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(17);
			$enchant2->setLevel(3);
			$item->addEnchantment($enchant2);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
				            foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cSpodnie §74/3 §8•");
	}
	}
			break;
       case 4:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(313, 0, 1);
			$enchant = Enchantment::getEnchantment(0);
			$enchant->setLevel(4);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(17);
			$enchant2->setLevel(3);
			$item->addEnchantment($enchant2);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
								            foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cButy §74/3 §8•");
	}
	}
			break;
       case 5:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(276, 0, 1);
			$enchant = Enchantment::getEnchantment(9);
			$enchant->setLevel(5);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(13);
			$enchant2->setLevel(2);
			$item->addEnchantment($enchant2);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
												            foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cMiecz Sharpness §75/2 §8•");
	}
	}
			break;
       case 6:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(276, 0, 1);
			$enchant = Enchantment::getEnchantment(9);
			$enchant->setLevel(5);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(12);
			$enchant2->setLevel(2);
			$item->addEnchantment($enchant2);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
																            foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cMiecz Knockback §75/2 §8•");
	}
	}
			break;
       case 7:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(278, 0, 1);
			$enchant = Enchantment::getEnchantment(15);
			$enchant->setLevel(5);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(17);
			$enchant2->setLevel(3);
			$item->addEnchantment($enchant2);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
																				            foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cKilof §75/3 §8•");
	}
	}
			break;
       case 8:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(373, 31, 1);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
										 foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cSile §71 §8•");
	}
	}
			break;
       case 9:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(466, 1, 1);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
											 foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cKoxa §71 §8•");
	}
	}
			break;
       case 10:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(466, 1, 3);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
														 foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cKoxy §73 §8•");
	}
	}
			break;
       case 11:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(466, 1, 5);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
														 foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cKoxy §75 §8•");
	}
	}
			break;
       case 12:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(322, 0, 10);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
														 foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cRefy §710 §8•");
	}
	}
			break;
       case 13:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(332, 0, 5);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
														 foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cPerly §75 §8•");
	}
	}
			break;
       case 14:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(129, 0, 5);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
														 foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cCobblexy §75 §8•");
	}
	}
			break;
       case 15:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(388, 0, 64);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
														 foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cEmeraldy §764 §8•");
	}
	}
			break;
       case 16:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(264, 0, 64);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
														 foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cDiamenty §764 §8•");
	}
	}
			break;
       case 17:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(46, 0, 32);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
														 foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cTNT §732 §8•");
	}
	}
			break;
       case 18:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(0, 0, 0);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
														 foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cNic  §8•");
	}
	}
			break;
			       case 19:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(310, 0, 1);
			$item->setCustomName("§r§l§aHelm Gornika :D§r\n§r§l§aUbierz i poczuj sie jak gornik!§r");
			$enchant = Enchantment::getEnchantment(0);
			$enchant->setLevel(4);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(17);
			$enchant2->setLevel(3);
			$item->addEnchantment($enchant2);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
            foreach ($this->getServer()->getOnlinePlayers() as $p) {
      if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	            $p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cHelm Gornika §8•");
	}
	}
			break;
			       case 20:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(313, 0, 1);
			$item->setCustomName("§r§l§fSzybkie Buty :D§r\n§r§l§aUbierz i poczuj moc!§r");
			$enchant = Enchantment::getEnchantment(0);
			$enchant->setLevel(4);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(17);
			$enchant2->setLevel(3);
			$item->addEnchantment($enchant2);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
            foreach ($this->getServer()->getOnlinePlayers() as $p) {
      if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	            $p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cButy Szybkosci §8•");
	}
	}
			break;
			      case 21;
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(276, 0, 1);
			$item->setCustomName("§l§cMiecz Zaglady");
			$enchant = Enchantment::getEnchantment(9);
			$enchant->setLevel(5);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(13);
			$enchant2->setLevel(2);
			$item->addEnchantment($enchant2);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);			
			$e->setCancelled();
							$item = $e->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
												            foreach ($this->getServer()->getOnlinePlayers() as $p) {
        if(	$this->pandora->get($p->getPlayer()->getDisplayName()) == 0) {	
	$p->sendMessage("§8• [§cPremiumCase§8] §7Gracz §c$name §7wylosował: §cMiecz Zaglady §8•");
	}
	}
			break;
			}
		}
		}
					  public function FirstJoin(PlayerJoinEvent $event){
	if(	$this->pandora->exists($event->getPlayer()->getName())){
		}
        else{
	if(!(	$this->pandora->exists($event->getPlayer()->getName()))){
        $name = $event->getPlayer()->getDisplayName();
        $this->getLogger()->info("Dodaje gracza $name do bazy danych!");
	  		$this->pandora->set($name,0);
	#	$graczedata->save();	
	$this->pandora->save();
	}
	}
	}
	public function dropStone(BlockBreakEvent $e){
		$player = $e->getPlayer();
		$block = $e->getBlock();
		$gracz = $e->getPlayer()->getName();
		$y = $player->getFloorY();
		$nick = $e->getPlayer()->getDisplayName();
		if($e->getBlock()->getId() == 1){
			 switch(mt_rand(1,90)){
 		 case 1:
		 if($y <= 20){
		 if($this->perla->get($nick) == 0) {	
         $player->sendTip("§c[§e+§c] §d Perla §cszt. §e1");
         $player->getInventory()->addItem(Item::get(332, 0, 1));
		 $player->addExperience(6);
		 }
		 }
         case 2:
		 		 if($y <= 10){
		 if(	$this->zloto->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §6 Zloto §cszt. §e3");
         $player->getInventory()->addItem(Item::get(266, 0, 3));
		 $player->addExperience(4);
		 }
		 }
         break;
         case 3:
		 		 if($y <= 10){
		 if(	$this->zloto->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §6 Zloto §cszt. §e2");
         $player->getInventory()->addItem(Item::get(266, 0, 2));
		 $player->addExperience(3);
		 }
		 }
         break;
         case 4:
		 		 if($y <= 10){
		 if(	$this->zloto->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §6 Zloto §cszt. §e1");
         $player->getInventory()->addItem(Item::get(266, 0, 1));
		 $player->addExperience(2);
		 }
				 }
         break;
		 		 case 5:
				 		 if($y <= 15){
				 if(	$this->diament->get($nick) == 0) {
		 $player->sendTip("§c[§e+§c] §b Diament §cszt. §e3");
		 $player->getInventory()->addItem(Item::get(264, 0, 3));
		 		 $player->addExperience(3);
				 }
						 }
		 break;
		 		 case 6:
				 if($y <= 15){
				 if(	$this->diament->get($nick) == 0) {
		 $player->sendTip("§c[§e+§c] §b Diament §cszt. §e2");
		 $player->getInventory()->addItem(Item::get(264, 0, 2));
		 		 $player->addExperience(2);
				 }
				 }
		 break;
		 		 case 7:
				 if($y <= 15){
				 if(	$this->diament->get($nick) == 0) {
		 $player->sendTip("§c[§e+§c] §b Diament §cszt. §e1");
		 $player->getInventory()->addItem(Item::get(264, 0, 1));
		 		 $player->addExperience(1);
				 }
				 }
		 break;
		          case 8:
				  if($y <= 35){
				  if(	$this->obsydian->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §0 Obsydian §cszt. §e3");
         $player->getInventory()->addItem(Item::get(49, 0, 3));
		 $player->addExperience(2);
				  }
				  }
         break;
		          case 9:
				  if($y <= 35){
				  if(	$this->obsydian->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §0 Obsydian §cszt. §e2");
         $player->getInventory()->addItem(Item::get(49, 0, 2));
		 $player->addExperience(2);
				  }
				  }
         break;
		          case 10:
				  if($y <= 35){
				  if(	$this->obsydian->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §0 Obsydian §cszt. §e1");
         $player->getInventory()->addItem(Item::get(49, 0, 1));
		 $player->addExperience(2);
				  }
				  }
         break;
		          case 11:
				  if($y <= 30){
				  if(	$this->emerald->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §a Emerald §cszt. §e3");
         $player->getInventory()->addItem(Item::get(388, 0, 3));
		 $player->addExperience(2);
				  }
				  }
         break;
		          case 12:
				  if($y <= 30){
				  if(	$this->emerald->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §a Emerald §cszt. §e2");
         $player->getInventory()->addItem(Item::get(388, 0, 2));
		 $player->addExperience(2);
				  }
				  }
         break;
		          case 13:
				  if($y <= 30){
				  if(	$this->emerald->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §a Emerald §cszt. §e1");
         $player->getInventory()->addItem(Item::get(388, 0, 1));
		 $player->addExperience(2);
				  }
				  }
         break;
		 		 case 14:
				 if($y <= 30){
				 if(	$this->zelazo->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §f Zelazo §cszt. §e3");
         $player->getInventory()->addItem(Item::get(265, 0, 3));
		 $player->addExperience(2);
				 }
				 }
         break;
		 		 case 15:
				 if($y <= 30){
				 if(	$this->zelazo->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §f Zelazo §cszt. §e2");
         $player->getInventory()->addItem(Item::get(265, 0, 2));
		 $player->addExperience(2);
				 }
				 }
         break;
		 		 case 16:
				 if($y <= 30){
				 if(	$this->zelazo->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §f Zelazo §cszt. §e1");
         $player->getInventory()->addItem(Item::get(265, 0, 1));
		 $player->addExperience(2);
				 }
				 }
         break;
		 		          case 17:
						  if($y <= 40){
						  if(	$this->proszek->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §7 Proszek §cszt. §e3");
         $player->getInventory()->addItem(Item::get(289, 0, 3));
		 $player->addExperience(3);
						  }
						  }
         break;
		 		          case 18:
						  if($y <= 40){
						  if(	$this->proszek->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §7 Proszek §cszt. §e2");
         $player->getInventory()->addItem(Item::get(289, 0, 2));
		 $player->addExperience(2);
						  }
						  }
         break;
		 		          case 19:
						  if($y <= 40){
						  if(	$this->proszek->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §7 Proszek §cszt. §e1");
         $player->getInventory()->addItem(Item::get(289, 0, 1));
		 $player->addExperience(1);
						  }
						  }
         break;
		 		 case 20:
				 if($y <= 35){
				 if(	$this->ksiazki->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §3 Ksiazki §cszt. §e3");
         $player->getInventory()->addItem(Item::get(340, 0, 3));
		 $player->addExperience(1);
				 }
				 }
         break;
		 		 case 21:
				 if($y <= 35){
				 if(	$this->ksiazki->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §3 Ksiazki §cszt. §e2");
         $player->getInventory()->addItem(Item::get(340, 0, 2));
		 $player->addExperience(1);
				 }
				 }
         break;
		 		 case 22:
				 if($y <= 35){
				 if(	$this->ksiazki->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §3 Ksiazki §cszt. §e1");
         $player->getInventory()->addItem(Item::get(340, 0, 1));
		 $player->addExperience(1);
				 }
				 }
         break;
		 		 		 case 23:
						 if($y <= 40){
						 if(	$this->lapis->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §9 Lapis §cszt. §e3");
         $player->getInventory()->addItem(Item::get(351, 4, 3));
		 $player->addExperience(1);
						 }
						 }
         break;
		 		 		 case 24:
						 if($y <= 35){
						 if(	$this->lapis->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §9 Lapis §cszt. §e2");
         $player->getInventory()->addItem(Item::get(340, 0, 2));
		 $player->addExperience(1);
						 }
						 }
         break;
		 		 		 case 25:
						 if($y <= 35){
						 if(	$this->lapis->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §9 Lapis §cszt. §e1");
         $player->getInventory()->addItem(Item::get(340, 0, 1));
		 $player->addExperience(1);
						 }
						 }
         break;
         case 26:
		 if($y <= 50){
		 if(	$this->wegiel->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §8 Wegiel §cszt. §e3");
         $player->getInventory()->addItem(Item::get(263, 0, 3));
		 $player->addExperience(1);
		 }
		 }
         break;
		          case 27:
				  if($y <= 50){
				  if(	$this->wegiel->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §8 Wegiel §cszt. §e2");
         $player->getInventory()->addItem(Item::get(263, 0, 2));
		 $player->addExperience(1);
				  }
				  }
         break;
		          case 28:
				  if($y <= 50){
				  if(	$this->wegiel->get($nick) == 0) {
         $player->sendTip("§c[§e+§c] §8 Wegiel §cszt. §e1");
         $player->getInventory()->addItem(Item::get(263, 0, 1));
		 $player->addExperience(1);
				  }
				  }
         break;

		          case 29:
				  if($y <= 25){
				  if(	$this->tnt->get($nick) == 0) {	
         $player->sendTip("§c[§e+§c] §4TNT §cszt. §e1");
         $player->getInventory()->addItem(Item::get(46, 0, 1));
		 $player->addExperience(1);
				  }
				  }
         break;
		          case 30:
				  if($y <= 25){
				  if(	$this->tnt->get($nick) == 0) {	
         $player->sendTip("§c[§e+§c] §4TNT §cszt. §e2");
         $player->getInventory()->addItem(Item::get(46, 0, 2));
		 $player->addExperience(1);
				  }
				  }
         break;
		          case 31:
				  if($y <= 25){
				  if(	$this->tnt->get($nick) == 0) {	
         $player->sendTip("§c[§e+§c] §4TNT §cszt. §e3");
         $player->getInventory()->addItem(Item::get(46, 0, 3));
		 $player->addExperience(1);
				  }
				  }
         break;
			 }
			 if($y >= 120){
			 switch(mt_rand(1,1000)){
			case 1:
			Server::getInstance()->broadcastMessage("§8• §7Gracz§c " . $gracz . " §7Wydropil ze stone range §bVIP §7GRATULACJE! §8•");
			Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'setgroup ' . $gracz . ' vip');
			}
			}
		}
	}
	public function dropLiscie(BlockBreakEvent $e){
		$player = $e->getPlayer();
		$block = $e->getBlock();
		$gracz = $e->getPlayer()->getName();
		if($e->getBlock()->getId() == 18){
			 switch(mt_rand(1,2)){
		          case 1:
         $player->sendTip("§c[§e+§c] §cJablko szt.§e 1");
         $player->getInventory()->addItem(Item::get(260, 0, 1));
		 $player->addExperience(1);
         break;
			 }
		}
		}
		 public function DropFirstJoin(PlayerJoinEvent $event){
	if(	$this->perla->exists($event->getPlayer()->getName())){
		}
        else{
	if(!(	$this->perla->exists($event->getPlayer()->getName()))){
        $nick = $event->getPlayer()->getDisplayName();
        $this->getLogger()->info("Dodaje gracza $nick do bazy danych!");
	      $this->perla->set($nick,0);
				$this->perla->save();
		    $this->zloto->set($nick,0);
				$this->zloto->save();
			  $this->diament->set($nick,0);
				$this->diament->save();
			  $this->obsydian->set($nick,0);
				$this->obsydian->save();
				$this->emerald->set($nick,0);
				$this->emerald->save();
				$this->zelazo->set($nick,0);
				$this->zelazo->save();
				$this->proszek->set($nick,0);
				$this->proszek->save();
				$this->ksiazki->set($nick,0);
				$this->ksiazki->save();
				$this->lapis->set($nick,0);
				$this->lapis->save();
				$this->wegiel->set($nick,0);
				$this->wegiel->save();
				$this->tnt->set($nick,0);
				$this->tnt->save();
}
		}
	}
	public function WorldBorder(PlayerMoveEvent $event){
	$player = $event->getPlayer();
	$x = $player->getX();
	$y = $player->getY();
	$z = $player->getZ();
	if($player->getX() >= 1500){
    $player->knockBack($player, 0, -2, 0, 0.5);
	}
	if($player->getZ() >= 1500){
    $player->knockBack($player, 0, 0, -2, 0.5);
	}
	if($player->getZ() <= -1500){
    $player->knockBack($player, 0, 0, 2, 0.5);
	}
	if($player->getX() <= -1500){
    $player->knockBack($player, 0, 2, 0, 0.5);
	}
	}
	    public function LosoweTP(PlayerInteractEvent $e) {
        if($e->getBlock()->getId() == 19) {
            $x = rand(1,1450);
            $y = 100;
            $z = rand(1,1450);
            $e->getPlayer()->sendMessage("§8• §8(§cLosoweTP§8) §7Teleportowanie w losowe miejsce: X: §c$x §7Y: §c$y §7Z: §c$z §8•");
            $this->players[$e->getPlayer()->getName()] = true;
            $e->getPlayer()->teleport(new Position($x, $y, $z, $e->getPlayer()->getLevel()));
            $e->getPlayer()->addEffect(Effect::getEffect(9)->setAmplifier(2)->setDuration(20*10));
            $e->getPlayer()->addEffect(Effect::getEffect(1)->setAmplifier(3)->setDuration(20*15));
        }
    }

    public function LosoweDamage(EntityDamageEvent $e) {
        if($e->getCause() == EntityDamageEvent::CAUSE_FALL) {
            if(isset($this->players[$e->getEntity()->getName()])) {
                $e->setCancelled(true);
                unset($this->players[$e->getEntity()->getName()]);
            }
        }
    }
    public function RzucaneTNT(BlockPlaceEvent $event){
   		$nazwa = $event->getItem()->getCustomName();
         $player = $event->getPlayer();
		 $blok = $event->getBlock();
		 if($nazwa === "§r§l§4Rzucane TNT"){
         if($blok->getId() == 46){
			 $player->getInventory()->removeItem(Item::get(46, 0, 1));
			 $event->setCancelled();
			 $x = $blok->getFloorX();
			 $y = $blok->getFloorY();
			 $z = $blok->getFloorZ();
			$cmd = "summon PrimedTNT $x $y $z";	
			$this->getServer()->dispatchCommand(new ConsoleCommandSender,$cmd);
			echo "2\n";
			$player->sendMessage("§8• [§cNicePE§8]§7 Użyłeś Rzucaka!");
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$player->getInventory()->setItemInHand($item);
       }
    }
}
	public function stoniarkaPostaw(BlockPlaceEvent $event){
	 $blok = $event->getBlock();
	 $gracz = $event->getPlayer();
	 $y = $blok->getFloorY();
	 $x = $blok->getFloorX();
	 $z = $blok->getFloorZ();
	 
  	 if($blok->getId() == 121){
  	  if(!($event->isCancelled())){
	  $gracz->sendMessage("§8• (§cNicePE§8)§7 Postawiles stoniarke!");
	  $gracz->sendMessage("§8• (§cNicePE§8)§7 Postaw na niej stone aby ja aktywowac!");
        $center = new Vector3($x, $y, $z);
        for($yaw = 0, $y = $center->y; $y < $center->y + 3; $yaw += (M_PI * 2) / 20, $y += 1 / 20) {
            $x = -sin($yaw) + $center->x;
            $z = cos($yaw) + $center->z;
    }
	  }else{
	  $gracz->sendMessage("§8(§cNicePE§8)§7 Ten teren jest zajęty!");
	  }	 
	 }
 }
 	 public function stoneZniszcz(BlockBreakEvent $event){
	  $blok = $event->getBlock();
	  $gracz = $event->getPlayer();
	  $y = $blok->getFloorY();
	  $x = $blok->getFloorX();
  	 $z = $blok->getFloorZ();
  	  if($blok->getId() == 1){
  	   if($gracz->getLevel()->getBlock(new Vector3($x, $y-1, $z))->getId() == 121) {
		$gracz->getInventory()->addItem(Item::get(4, 0, 1));
	   $task = new NicePE_Stoniarka($this, $event->getBlock()->getFloorX(), $event->getBlock()->getFloorY(), $event->getBlock()->getFloorZ());
       $this->getServer()->getScheduler()->scheduleDelayedTask($task, 30);
	   $drops = array(Item::get(0, 0, 0));
      $event->setDrops($drops);
  	      }
  	   }
	 }
	 	public function fly(PlayerMoveEvent $ev){
		$gracz = $ev->getPlayer();
		$level = $this->getServer()->getDefaultLevel();
		$distance = $level->getSpawnLocation()->distance($gracz);
		if($gracz->isOp()){
			$gracz->setAllowFlight(true);
			}else if($gracz->hasPermission("nicepe.fly")){
		if($distance >= $this->fly->get("Odleglosc")){
			$gracz->setAllowFlight(false);
			$gracz->sendTip("false");
			}
			if($distance <= $this->fly->get("Odleglosc")) {
			$gracz->setAllowFlight(true);
			$gracz->sendTip("true");
			}
		}
		}
	public function onTalk(PlayerChatEvent $ev){
		if ($ev->getPlayer()->hasPermission("simplenospam.bypass")){
			$ev->setCancelled(false);
			return true;
		}
		if (in_array($ev->getPlayer()->getName(), $this->talked)){
			$config = $this->config->getAll();
			$ev->setCancelled();
			$ev->getPlayer()->sendMessage("§8• (§cCHAT§8) §7Mozesz pisac co §c10 §7sekund! §8•");
		}
		else if (!in_array($ev->getPlayer()->getName(), $this->talked)){
			$bw = $this->config->getAll();
			$msg = explode(" ",$ev->getMessage());
			foreach ($msg as $word){
				foreach ($bw['Blocked words'] as $blw){
					if ($blw === strtolower($word)){
						if ($bw['replace words'] === "true"){
							$ev->setMessage($bw['Replacement Message']);
							$ev->getPlayer()->sendMessage("§cNie mozesz pisac tego slowa!".$blw);
							return true;
						}else{
							$ev->setCancelled();
							$ev->getPlayer()->sendMessage("[AnitSpam] You are not allowed to say the word ".$blw);
							return true;
						}
					}
				}
			}
		 	array_push($this->talked, $ev->getPlayer()->getName());
		 	$task = new allowtalk($this, $ev->getPlayer());
		 	$this->getServer()->getScheduler()->scheduleDelayedTask($task, 10* $bw['interval']);
		 	return;
		}
		
		
	}
        public function ChatOff(PlayerChatEvent $event) {
    
      if(!($event->getPlayer()->hasPermission("chattoolspro.lockchat"))) {
      
        if($this->disableChat) {
        
          $event->setCancelled(true);
          
          $event->getPlayer()->sendMessage("§8• §7Niestety chat główny jest wyłączony! §8•");
          
        }
        
      }
      
    }
    public function getCmd(){
        return (array)$this->cfg->get('block.cmd');
    }
    public function getMsg(){
        $m = $this->cfg->get('block.message');
        $m = $this->getFormat()->translate($m);
        return $m;
    }
    public function getFormat(){
        return $this->format;
    }
	public function perlaTP(ProjectileLaunchEvent $event){
		$entity = $event->getEntity();
		if($entity instanceof Snowball){
			$shooter = $entity->shootingEntity;
			$ballid = $entity->getId();
			if($shooter instanceof Player){
				$id = $shooter->getId();
				if( array_key_exists($id,$this->order) ){array_push($this->order[$id],$ballid);}
					else{$this->order += array($id => array($ballid));}
			}
		}
	}
	public function perlaDead(PlayerDeathEvent $event){
		$entity = $event->getEntity();
		if($entity instanceof Player){
			$id = $entity->getId();
//			$this->getLogger()->info($entity->getName()."is dead");
			if(array_key_exists($id,$this->order)){$this->order[$id]=array();}
		}
	}
	public function perlaClose(EntityDespawnEvent $event){
		if($event->getType() === 81){	//81=Snowball
			$entity = $event->getEntity();
			$ballid = $entity->getId();
			$shooter = $entity->shootingEntity;
			$posTo = $entity->getPosition();
			
			if($posTo instanceof Position){
				if($shooter instanceof Player && $shooter->hasPermission("enderpearl.teleport")){
					$id = $shooter->getId();
					$key = array_search($ballid,$this->order[$id]);
					if(array_key_exists($id,$this->order) && $key!==false ){
						unset($this->order[$id][$key]);
						$posFrom = $shooter->getPosition();
						
						$shooter->teleport($posTo);
						if(!$shooter->isCreative()){
							$ev = new EntityDamageEvent( $shooter, EntityDamageEvent::CAUSE_MAGIC, $this->config->get("damage") );
							$shooter->attack($ev->getFinalDamage(), $ev);
						}
					}
				}
			}
		}
		if( $event->isHuman() ){		// log out
			$entity = $event->getEntity();
			$id = $entity->getId();
			if(array_key_exists($id,$this->order)){
//				$this->getLogger()->info($entity->getName());
				unset($this->order[$id]);
			}
		}
	}
	public function cx(BlockBreakEvent $e){
	$block = $e->getBlock();
	$gracz = $e->getPlayer()->getName();
	$player = $e->getPlayer();
	$x = $block->getFloorX();
	$y = $block->getFloorY();
	$z = $block->getFloorZ();
		if($e->getBlock()->getId() == 48){
			$drops = array(Item::get(0, 0, 0));
			$e->setDrops($drops);
			 switch(mt_rand(1,20)){
         case 1:
        $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(2) §7Mikstura Niewidzialności 3:00 §8•");
		$item = Item::get(373, 7, 1);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 2:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(2) §7Mikstura Regeneracji 2:00 §8•");
		$item = Item::get(48, 0, 3);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 3:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(3) §7Mikstura Siły §8•");
		$item = Item::get(373, 32, 1);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 4:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(3) §7CobbleX §8•");
		$item = Item::get(48, 0, 3);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 5:
         $player->sendMessag("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(22) §7Diamentów §8•");
		$item = Item::get(264, 0, 22);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 6:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(64) §7Melon §8•");
 		$item = Item::get(360, 0, 64);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 7:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(37) §7Kamieni §8•");
		$item = Item::get(1, 0, 37);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 8:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(13) §7Jabłek §8•");
		$item = Item::get(260, 0, 13);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 9:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(21) §7Żelazo §8•");
		$item = Item::get(265, 0, 21);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 10:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(17) §7Złota §8•");
 		$item = Item::get(266, 0, 17);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 11:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(47) §7Piasek §8•");
		$item = Item::get(12, 0, 47);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 12:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(64) §7Szkła §8•");
		$item = Item::get(20, 0, 64);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 13:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(1) §7Stół do enchantowania §8•");
		$item = Item::get(116, 0, 1);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 14:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(1) §7Statyw alchemiczny §8•");
		$item = Item::get(379, 0, 1);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 15:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(38) §7obsydian §8•");
		$item = Item::get(49, 0, 38);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 16:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(34) §7Półki z książkami §8•");
		$item = Item::get(47, 0, 34);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 17:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(37) §7Obsidian §8•");
		$item = Item::get(49, 0, 37);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);;
         break;
         case 18:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7wylosował: §c(47) §7Steków §8•");
		$item = Item::get(364, 0, 47);
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
         break;
         case 19:
         $player->sendMessage("§8• §8(§cCobbleX§8) §7Gracz §c$gracz §7nic nie wylosował §f8");
		$item = Item::get();
		$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);

}
		}
	}
}