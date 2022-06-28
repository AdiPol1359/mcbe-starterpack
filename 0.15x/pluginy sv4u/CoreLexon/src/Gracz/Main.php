<?php
namespace Gracz;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\IPlayer;
use pocketmine\command\{Command, CommandSender};
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\level\particle\Particle;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\level\Position\getLevel;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Config;
use pocketmine\utils\Utils;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use xSmoothy\FactionsPro\FactionMain;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\Item;
use pockemine\inventory\Inventory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\entity\Effect;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\entity\Snowball;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\level\particle\BubbleParticle;
use pocketmine\level\particle\LavaParticle;
use pocketmine\level\sound\BatSound;
use pocketmine\level\sound;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\block\Air;
use pocketmine\block\Obsidian;
use pocketmine\block\Sand;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\entity\EntityShootBowEvent;

class Main extends PluginBase implements Listener{
	
 CONST prefix = TF::RED.TF::BOLD."Elo ".TF::RESET;
    /** @var Config $eloyaml */
 public $eloyaml;
    /** @var Config $config */
 public $config;
 	
 	private $cooldown = [];
	
	public $players = [];
	
		    public function getAPI()
    {
        return Server::getInstance()->getPluginManager()->getPlugin("FactionsPro");
    }
		    public function getAPI2()
    {
        return Server::getInstance()->getPluginManager()->getPlugin("Elo");
    }
			    public function getAPI3()
    {
        return Server::getInstance()->getPluginManager()->getPlugin("EconomyAPI");
    }
			    public function getAPI4()
    {
        return Server::getInstance()->getPluginManager()->getPlugin("EssentialsPE");
    }
			    public function getAPI5()
    {
        return Server::getInstance()->getPluginManager()->getPlugin("PurePerms");
    }
			public function getAPI6()
			{
				return Server::getInstance()->getPluginManager()->getPlugin("ServerAtuh");
			}
			
	
	public function onEnable(){
		@mkdir($this->getDataFolder());
		$server = $this->getServer();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("§1E§2n§3a§4b§5l§6e§7d§a!");
		$this->saveDefaultConfig();
		$cmd = "setworldspawn -79 80 1";
		$this->getServer()->dispatchCommand(new ConsoleCommandSender,$cmd);
		$this->db = new \SQLite3($this->getDataFolder() . "StoniarkiPro.db");
		$this->db->exec("CREATE TABLE IF NOT EXISTS stoniarki (x INT, y INT, z INT);");
		}
public function process(): void {

		$flag = true;
		$name = $this->args->getName();
		$manager = $this->pl->getServer()->getPluginManager();

		if($this->pl->config->get("rank") == 1){
			$pp = $manager->getPlugin("PurePerms");
			if(!is_null($func = $pp->getUserDataMgr()->getGroup($this->args))){
				$rank = $func->getName();
			}
		else{
			$rank = '-';
		}
		}
}

public function onDeath(PlayerDeathEvent $event){
    $deathdata = new Config($this->getDataFolder() . "/smierci.yml", Config::YAML);
        $name = $event->getPlayer()->getDisplayName();
        $deaths = $deathdata->get($name);
        $deathdata->set($name,$deaths+1);
        $deathdata->save();
    }
public function onDeath2(PlayerDeathEvent $event){
    $pointdata = new Config($this->getDataFolder() . "/punkty.yml", Config::YAML);
    $entity = $event->getEntity();
    $cause = $entity->getLastDamageCause();
        $killer = $cause->getDamager();
    if($killer instanceof Player){
      $name = $killer->getName();
      $points = $pointdata->get($name);
      $pointdata->set($name,$points+100);
      $pointdata->save();
    }
  }
 public function onDeath3(PlayerDeathEvent $event){
     $pointdata = new Config($this->getDataFolder() . "/punkty.yml", Config::YAML);
   $pkt = 100;
   $player = $event->getPlayer()->getDisplayName();
     $punkty = $pointdata->get($player);
     $ustawpkt = $punkty - $pkt;
      $pointdata->set($player, $ustawpkt);
        $pointdata->save();
        $pointdata->reload();
 }
   public function onDeath4(PlayerDeathEvent $event){
    $killdata = new Config($this->getDataFolder() . "/zabicia.yml", Config::YAML);
    $entity = $event->getEntity();
    $cause = $entity->getLastDamageCause();
    $killer = $cause->getDamager();
    if($killer instanceof Player){
      $name = $killer->getName();
      $kills = $killdata->get($name);
      $killdata->set($name,$kills+1);
      $killdata->save();
    }
  }
	
  public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
	  //// BEZ ARGUMENTU \\\\
        if($cmd->getName() == "gracz"){
			if($sender instanceof Player) {
				if(empty($args)){
			$gracz = $sender->getName();
            $deathdata = new Config($this->getDataFolder() . "/smierci.yml", Config::YAML);
            $deaths = $deathdata->get($sender->getName());
            $killdata = new Config($this->getDataFolder() . "/zabicia.yml", Config::YAML);
            $kills = $killdata->get($sender->getName());
            $pointdata = new Config($this->getDataFolder() . "/punkty.yml", Config::YAML);
            $points = $pointdata->get($sender->getName());
			$playername = $sender->getName();
			$gildia = $this->getAPI()->getPlayerFaction($gracz);
			$punktciory = $this->getAPI2()->getElo($gracz);
			$monety = $this->getAPI3()->myMoney($gracz);
			$monety2 = $this->getAPI3()->getMonetaryUnit();
			$rank = $this->getAPI5()->getUserDataMgr()->getGroup($sender)->getName();
			$sender->sendMessage("§8[§7 ------------§8 [ §3§lGRACZ §r§8] §7------------ §8]");
			$sender->sendMessage("§3× §eGracz: §3" . $gracz . "");
			if($this->getAPI()->isInFaction($sender->getName())){
			$sender->sendMessage("§3× §eGildia: §3" . strtoupper($gildia) . "");
			}
			else{
			$sender->sendMessage("§3* §7Gildia: §3BRAK GILDII");
			}
			// str_replace(array('NAN', 'INF'), '0.00', "§3* §7K/D: §3" . round($kills / $deaths, 2) . ""); zamieni INF oraz NAN ktore jest mnozozne przez 0:0 lub 1:0, 0:1 na 0 lub 0.00
			$sender->sendMessage("§3× §ePunkty: §3" . $punktciory . "");
			$sender->sendMessage("§3× §eZabojstwa: §3" . $kills . "");
			$sender->sendMessage("§3× §eSmierci: §3" . $deaths . "");
			$sender->sendMessage("§8[§7 ------------§8 [ §3§lGRACZ §r§8] §7------------ §8]");
			}
			}
			///// Z ARGUMENTEM \\\\\\
			if(count($args) == 1){
			$playername = $args[0];
            $killdata = new Config($this->getDataFolder() . "/zabicia.yml", Config::YAML);
            $kills = $killdata->get($playername);
            $deathdata = new Config($this->getDataFolder() . "/smierci.yml", Config::YAML);
            $deaths = $deathdata->get($playername);
            $pointdata = new Config($this->getDataFolder() . "/punkty.yml", Config::YAML);
            $points = $pointdata->get($playername);
			$gildia = $this->getAPI()->getPlayerFaction($playername);
			$punkty = $this->getAPI2()->getElo($playername);
			$monety = $this->getAPI3()->myMoney($playername);
			$monety2 = $this->getAPI3()->getMonetaryUnit();
			$plik = new Config($this->getDataFolder() . "rangi.yml", Config::YAML);
			$rank = $plik->get($playername);
			if($killdata->exists($playername) &&  $deathdata->exists($playername) &&    $placedata->exists($playername) &&  $joindata->exists($playername) &&  $koxydata->exists($playername) &&  $refiledata->exists($playername)){
			if($this->getServer()->getNameBans()->isBanned(strtolower($playername))){
			$sender->sendMessage("§8[§7 ------------§8 [ §3§lGRACZ §r§8] §7------------ §8]");
			$sender->sendMessage("§3× §eGracz: §3" . $playername . "");
			if($this->getAPI()->isInFaction($args[0])){
			$sender->sendMessage("§3× §eGildia: §3" . strtoupper($gildia) . "");
			}
			else{
			$sender->sendMessage("§3× §eGildia: §3BRAK GILDII");
			}
			$sender->sendMessage("§3× §ePunkty: §3" . $punkty . "");
			$sender->sendMessage("§3× §eZabojstwa: §3" . $kills . "");
			$sender->sendMessage("§3× §eSmierci: §3" . $deaths . "");
			$sender->sendMessage("§8[§7 ------------§8 [ §3§lGRACZ §r§8] §7------------ §8]");
			}
			else{
			$sender->sendMessage("§8[§7 ------------§8 [ §3§lGRACZ §r§8] §7------------ §8]");
			$sender->sendMessage("§3× §eGracz: §3" . $playername . "");
			if($this->getAPI()->isInFaction($args[0])){
			$sender->sendMessage("§3× §eGildia: §3" . strtoupper($gildia) . "");
			}
			else{
			$sender->sendMessage("§3× §eGildia: §3BRAK GILDII");
			}
			$sender->sendMessage("§3× §ePunkty: §3" . $punkty . "");
			$sender->sendMessage("§3× §eZabojstwa: §3" . $kills . "");
			$sender->sendMessage("§3× §eSmierci: §3" . $deaths . "");	
			$sender->sendMessage("§8[§7 ------------§8 [ §3§lGRACZ §r§8] §7------------ §8]");
			}
  }
  else{
	  $sender->sendMessage("§f• §8[§e×SV4U×§8] §7Nie odnaleziono takiej osoby w bazie danych! §f•");
  }
			}
		}
			if($cmd->getName() == "premiumcase"){
				if($sender->hasPermission("mieso.command")){
				$item = Item::get(146, 0, $args[0]);
				if(count($args) == 1 or empty($args)){
					$sender->sendMessage("§eUzyj: /premiumcase <ilosc> <gracz>");
				}
				if(count($args) == 2){
					if(is_numeric($args[0])){
					$gracz = $this->getServer()->getPlayer($args[1]);
					$gracz->getInventory()->addItem($item);
					$gracz->sendMessage("§eOtrzymales§3 " . $args[0] . " §7PC! ");
				}
				else{
					$sender->sendMessage("§7Argument 1 musi byc numeryczny!");
				}
			}
  								}
				else{
					$sender->sendMessage("§8• §7Nie mozesz tego uzyc! §8•");
				}
			}
		 	if(strtolower($cmd->getName()) === "pall"){
				if($sender->hasPermission("mieso.command")){
				if(empty($args)){
					$sender->sendMessage("§e• §7Poprawne uzycie to /pall <ilosc> §8•");
				}
					if(count($args) == 1){
						if(is_numeric($args[0])){
							foreach($this->getServer()->getOnlinePlayers() as $p){
							$p->getInventory()->addItem(Item::get(146, 0, $args[0]));
                    $p->getInventory()->addItem(Item::get(247, 0, $args[0]));
                    $p->getInventory()->addItem(Item::get(421, 0, $args[0]));
							$p->sendMessage("§8• §8[§3" . $sender->getName() . "§8] §4Wszyscy otrzymali§3 " . $args[0] . " §4TOP BOXY / MC / Klucze! §8•");
							}
						}
											else{
							$sender->sendMessage("§8• §8[§3PremiumCase§8] §eArgument 1 musi byc numeryczny! §8•");
						}
	}
								}
				else{
					$sender->sendMessage("§8• §7Nie mozesz tego uzyc! §8•");
				}
	}
				if($cmd->getName() == "vip"){
					$sender->sendMessage("§a§l××VIP××");
					$sender->sendMessage("§f× §eFunkcje:");
					$sender->sendMessage("§f× §f1. §aRezerwacja slota");
					$sender->sendMessage("§f× §f2. §aKolorowy nick na czacie i mniejsze ograniczenia (gracz 20s, vip 3s)");
					$sender->sendMessage("§f× §f3. §aTOP BOXY x2 w zestawie");
					$sender->sendMessage("§f× §f4. §aWieksza ilosc osob w gildii (35 graczy)");
					$sender->sendMessage("§f× §f5. §aWlasny kit (/kit vip)");
					$sender->sendMessage("");
					$sender->sendMessage("§f• §6Aktualna ranga: §7$rank");
					$sender->sendMessage("");
					$sender->sendMessage("§f× §eZakup:");
					$sender->sendMessage("§f× §6Koszt: §e7,38");
					$sender->sendMessage("§f× §6Tresc: §eAP.HOSTMC §6Numer: §e76068");
					$sender->sendMessage("§f × Kod podaj do §eWlascicieli");
				}
				if($cmd->getName() == "svip"){
					$sender->sendMessage("§a§l××SVIP××");
					$sender->sendMessage("§f× §eFunkcje:");
					$sender->sendMessage("§f× §f1. §aRezerwacja slota");
					$sender->sendMessage("§f× §f2. §aKolorowy nick na czacie i mniejsze ograniczenia (gracz 20s, vip 3s, svip 1s)");
					$sender->sendMessage("§f× §f3. §aPowiekszony drop surowcow o 40%%");
					$sender->sendMessage("§f× §f4. §aWieksza ilosc osob w gildii (60 graczy)");
					$sender->sendMessage("§f× §f5. §aWlasny kit (/kit svip)");
					$sender->sendMessage("");
					$sender->sendMessage("§f• §6Aktualna ranga: §7$rank");
					$sender->sendMessage("");
					$sender->sendMessage("§f× §eZakup:");
					$sender->sendMessage("§f× §6Koszt: §e12,53");
					$sender->sendMessage("§f× §6Tresc: §eAP.HOSTMC §6Numer: §e91058");
					$sender->sendMessage("§f× Kod podaj do §eWlascicieli");
				}
				if($cmd->getName() == "cobblex"){
				if(empty($args)) {
					$sender->sendMessage("§f§l===============CX===============");
					$sender->sendMessage("§6Cobble X to unikalny block z ktorego wypadaja losowe itemy:");
					$sender->sendMessage("");
					$sender->sendMessage("§e- Rzadki Drop%%");
					$sender->sendMessage("§esztabka zelaza, diament, szmaragd, sztabka zlota, perla, jasnoglaz");
					$sender->sendMessage("§e- Czesty Drop%%");
					$sender->sendMessage("§ejablko, biblioteczka, quartz, sadzonka, siarka, skora");
					$sender->sendMessage("§eCobbleX kupuje sie za 9 x 64 cobblestone");
					$sender->sendMessage("§eAby zakupic wpisz /cobblex kup");
					$sender->sendMessage("§f§l==============CX===============");
					return true;
				}
				if($args[0] == "kup") {
				if($sender->getInventory()->contains(Item::get(4, 0, 576))){
				   $sender->getInventory()->removeItem(Item::get(4, 0, 576));
				   $sender->getInventory()->addItem(Item::get(129, 0, 1));
				   $sender->sendMessage("§8[ §6CobbleX §8] §7Zakupiłeś §cCobbleX");
            }
						else{
							$sender->sendMessage("§8[§6CobbleX§8] §7Nie posiadasz tyle §cBruku!");
                                                }
                                         }
                        }
				if($cmd->getName() == "stoniarka"){
				if(empty($args)) {
					$sender->sendMessage("§7[ ---------- §aStoniarka§7 ---------- ]");
					$sender->sendMessage("§eCo to §6Stoniarka?");
					$sender->sendMessage("§o§3  × Jest to blok ktory po postawieniu tworzy");
					$sender->sendMessage("§o§3  × nad soba stone block");
					$sender->sendMessage("§3Aby kupic wpisz:");
					$sender->sendMessage("§o§3  × /stoniarka kup §6- §o§cKoszt: 2 diamenty");
					$sender->sendMessage("§7[ ---------- §aStoniarka§7 ---------- ]");
					return true;
				}
				if($args[0] == "kup") {
				if($sender->getInventory()->contains(Item::get(264, 0, 2))){
				   $sender->getInventory()->removeItem(Item::get(264, 0, 2));
				   $sender->getInventory()->addItem(Item::get(121, 0, 4));
				   $sender->sendMessage("§8[ §6Stoniarka §8] §7Zakupiłeś §cStoniarke");
            }
						else{
							$sender->sendMessage("§8[§6Stoniarka§8] §7Nie posiadasz tyle §cdiamentow!");
                                                }
                                         }
                        }
				if($cmd->getName() == "klucz"){
				if(empty($args)) {
					$sender->sendMessage("§7[ ========== §6Klucz§7 ========== ]");
					$sender->sendMessage("§eCo to Klucz?");
					$sender->sendMessage("§o§e  × Jest to klucz ktory dropi na spawn rozne rzeczy");
					$sender->sendMessage("§eTop Boxy , Koxy , Magiczne Casy i wiele wiecej");
					$sender->sendMessage("§eAby kupic wpisz:");
					$sender->sendMessage("§o§e  × /klucz kup §6- §o§cKoszt: 128 Diamenty.");
					$sender->sendMessage("§7[ ========== §6Klucz§7 ========== ]");
					return true;
				}
					 if($args[0] == "kup") {
					 if($sender->getInventory()->contains(Item::get(264, 0, 128))){
						$sender->getInventory()->addItem(Item::get(421, 0, 1));
						 $sender->getInventory()->removeItem(Item::get(264, 0, 128));
						$sender->sendMessage("§8[§eSV4U§8] §7Zakupiłeś §cKlucz!");
						}
						else{
							$sender->sendMessage("§cNiemasz tyle potrzebnych itemów, potrzebujesz: 128 diamenty.");
							}
						return true;
                          }
	
	}		
				if($cmd->getName() == "yt"){
					$sender->sendMessage("§e§m-------------------------");
					$sender->sendMessage("§6Ranga Youtuber posiada!");
					$sender->sendMessage("§8§m-------------------------");
					$sender->sendMessage("§8» §7To samo co SVIP");
					$sender->sendMessage("§e» §7Prefix (YT)  przed nickiem!");
					$sender->sendMessage("§8§m-------------------------");
					$sender->sendMessage("§6Wymagania na Youtubera");
					$sender->sendMessage("§8» §7Duza liczba wyswietlen (30+)");
					$sender->sendMessage("§8» §7Potwierdzenie swojej tozsamosci");
					$sender->sendMessage("§e» §7Duza liczba widzow +120");
					$sender->sendMessage("§e» §7Chcesz z nami wspolpracowac? Napisz do nas!");
					$sender->sendMessage("§e§m-------------------------");
				}
				if($cmd->getName() == "regulamin"){
					$sender->sendMessage("§8§m-------------------------");
					$sender->sendMessage("§6Regulamin serwera");
					$sender->sendMessage("§8§m-------------------------");
					$sender->sendMessage("§8» §31. Zakaz cheatowania itp.");
					$sender->sendMessage("§8» §32. Zakaz reklamowania");
					$sender->sendMessage("§8» §33. Zakaz prob oszustwa administracji");
					$sender->sendMessage("§8» §34. Nie wykorzystuj bugow serwera");
					$sender->sendMessage("§8» §35. Nie spam na helpop bez potrzeby");
					$sender->sendMessage("§8» §36. Zakaz ddosowania itp.");
					$sender->sendMessage("§8» §37. Administracja nie rozdaje itemkow");
					$sender->sendMessage("§8» §38. Administracja zawsze ma racje");
					$sender->sendMessage("§8§m-------------------------");
				}
				if($cmd->getName() == "ts3"){
					$sender->sendMessage("§8§l§m«--------» §6§lTS3 §8§l§m«--------»");
					$sender->sendMessage("§6» §7Nasz TeamSpeak - §6PRACE TRWAJA");
					$sender->sendMessage("§8§l§m«--------» §6§lTS3 §8§l§m«--------»");
				}
				if($cmd->getName() == "pomoc"){
					$sender->sendMessage("§7×××Nasze komendy×××");
					$sender->sendMessage("§6 × /g pomoc §7- spis komend dla gildii");
					$sender->sendMessage("§3 × /helpop <nick> §7- zglos gracza");
      				$sender->sendMessage("§6 × /gracz <nick> §7- ranking gracza");
					$sender->sendMessage("§3 × /vip §7- informacje o vipie");
					$sender->sendMessage("§6 × /svip §7- informacje o svipie");
					$sender->sendMessage("§3 × /klucz §7- zakup klucza ");
					$sender->sendMessage("§6 × /yt §7- info o youtube");
					$sender->sendMessage("§3 × /coins §7- Wymiana Coins Na Casy itp.");
					$sender->sendMessage("§e × /cx §7- informacje o cobblexie");
					$sender->sendMessage("§8§l§m«--------» §6§l ×POMOC× §8§l§m«--------»");
				}
				if($cmd->getName() == "plugins"){
					$sender->sendMessage("§fPlugins (1): §aSV4UCORE");
				}
  
  if($cmd->getName() == "ip") {
	  if($sender->hasPermission("ip.ip")){
	  if(empty($args)){
		  $sender->sendMessage("§f• §8[§eSV4U§8] §7Uzyj: /ip on/off! §f•");
	  }
	  if($args[0] == "on"){
			$sprawdz = new Config($this->getDataFolder() . "/pickup.yml", Config::YAML);
			$sprawdz->set($sender->getName(), "1");
			$sprawdz->save();
			$sender->sendMessage("§f• §8[§eSV4U§8] §7Wlaczono podnoszenie itemow! §f•");
	  }
	  	  if($args[0] == "off"){
			$sprawdz = new Config($this->getDataFolder() . "/pickup.yml", Config::YAML);
			$sprawdz->set($sender->getName(), "0");
			$sprawdz->save();
			$sender->sendMessage("§f• §8[§eSV4U§8] §7Wylaczono podnoszenie itemow! §f•");
	  }
  }
  else{
	$sender->sendMessage("§3You don't have permission to use this command");  
  }
  }
  
  if($cmd->getName() == "cobblestone"){
	  if(empty($args)){
		  $sender->sendMessage("§f• §8[§eSV4U§8] §7Uzyj: /cobblestone on/off aby wlaczyc lub wylaczyc drop cobblestona! §f•");
	  }
	  if($args[0] == "on"){
		  $cfg = new Config($this->getDataFolder() . "cobble.yml", Config::YAML);
		  $cfg->set($sender->getName(), "0");
		  $cfg->save();
		  $sender->sendMessage("§f• §8[§eSV4U§8] §7Wlaczyles drop cobblestona! §f•");
	  }
	  if($args[0] == "off"){
		  $cfg = new Config($this->getDataFolder() . "cobble.yml", Config::YAML);
		  $cfg->set($sender->getName(), "1");
		  $cfg->save();
		  $sender->sendMessage("§f• §8[§eSV4U§8] §7Wylaczyles drop cobblestona! §f•");
	  }
  }
  }
  public function trueFirstJoin(PlayerJoinEvent $event){
	$graczedata = new Config($this->getDataFolder() ."/gracze.yml", Config::YAML);
	if($graczedata->exists($event->getPlayer()->getName())){
		$event->getPlayer()->sendMessage("§eWitaj na serwerze MegaDrop > SV4U.PL !");
		$event->getPlayer()->sendMessage("§e × Jezeli jestes tu nowy pamietaj zapoznaj sie z podstawowymi komendami /pomoc! × ");
		$event->getPlayer()->sendMessage("§e» §7Aktualnie na serwerze jest§a " . count($this->getServer()->getOnlinePlayers())  . " §7graczy! §8«");
		}
else{
	if(!($graczedata->exists($event->getPlayer()->getName()))){
        $name = $event->getPlayer()->getDisplayName();
        $gracze = $graczedata->get($name);
        $graczedata->set($name,$gracze+1);
		$cmd = "sudo $name kit start";
		$this->getServer()->dispatchCommand(new ConsoleCommandSender,$cmd);
		$graczedata->save();	
}
}
}
 
	public function trueYAMLRegisters(PlayerJoinEvent $event){
		$player = $event->getPlayer()->getName();
            $killdata = new Config($this->getDataFolder() . "/zabicia.yml", Config::YAML);
            $kills = $killdata->get($player);
            $deathdata = new Config($this->getDataFolder() . "/smierci.yml", Config::YAML);
            $deaths = $deathdata->get($player);
			$ipdata = new Config($this->getDataFolder() . "/pickup.yml", Config::YAML);
			$acdata = new Config($this->getDataFolder() . "ac.yml", Config::YAML);
			$ips = $ipdata->get($player);
			if($killdata->exists($player)){
			}
			else{
				$killdata->set($player, "0");
				$killdata->save();
			}
						if($deathdata->exists($player)){
			}
			else{
				$deathdata->set($player, "0");
				$deathdata->save();
			}
			if($ipdata->exists($player)){
			}
			else{
				$ipdata->set($player, "1");
				$ipdata->save();
			}
			if($acdata->exists($player)){
			}
			else{
				$acdata->set($player, "0");
				$acdata->save();
			}
	}
	
public function truePandors(BlockPlaceEvent $event){
	$gracz = $event->getPlayer();
	$blok = $event->getBlock();
	$name = $event->getPlayer()->getDisplayName();
	$x = $blok->getFloorX();
	$y = $blok->getFloorY();
	$z = $blok->getFloorZ();
		if($blok->getId() == 146){
			switch(mt_rand(1, 15)){
						case 1:
			$item = Item::get(320, 0, 1);
			$item->setCustomName("§l§bNieskonczone Mieso");
			$enchant = Enchantment::getEnchantment(17);
			$enchant->setLevel(3);
			$item->addEnchantment($enchant);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
			$gracz->sendPopup("§f• §7Gracz§3 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3Nieskonczone Mieso §f•");
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
						case 2:
			$item = Item::get(278, 0, 1);
			$item->setCustomName("§l§4Kilof Szybkosci");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
			$gracz->sendPopup("§f• §7Gracz§4 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3Kilof Szybkosci §f•");
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
			case 3:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(466, 0, 3));
			$gracz->sendPopup("§f• §7Gracz§4 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3Koxy §8(§33§8) §f•");
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
						case 4:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(332, 0, 4));
			$gracz->sendPopup("§f• §7Gracz§4 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3Perla §8(§34§8) §f•");
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
						case 5:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(310, 0, 1);
			$enchant = Enchantment::getEnchantment(0);
			$enchant->setLevel(4);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(17);
			$enchant2->setLevel(3);
			$item->addEnchantment($enchant2);
			$gracz->sendPopup("§f• §7Gracz§4 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3Helm 4/3 §f•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
						case 6:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(311, 0, 1);
			$enchant = Enchantment::getEnchantment(0);
			$enchant->setLevel(4);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(17);
			$enchant2->setLevel(3);
			$item->addEnchantment($enchant2);
			$gracz->sendPopup("§f• §7Gracz§4 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3Klate 4/3 §f•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
						case 7:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(312, 0, 1);
			$enchant = Enchantment::getEnchantment(0);
			$enchant->setLevel(4);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(17);
			$enchant2->setLevel(3);
			$item->addEnchantment($enchant2);
			$gracz->sendPopup("§f• §7Gracz§4 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3Spodnie 4/3 §f•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
						case 8:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(313, 0, 1);
			$enchant = Enchantment::getEnchantment(0);
			$enchant->setLevel(4);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(17);
			$enchant2->setLevel(3);
			$item->addEnchantment($enchant2);
			$gracz->sendPopup("§f• §7Gracz§4 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3Buty 4/3 §f•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
						case 9:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(276, 0, 1);
			$enchant = Enchantment::getEnchantment(9);
			$enchant->setLevel(5);
			$item->addEnchantment($enchant);
			$enchant2 = Enchantment::getEnchantment(12);
			$enchant2->setLevel(2);
			$item->addEnchantment($enchant2);
			$gracz->sendPopup("§f• §7Gracz§4 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3Miecz 5/2 §f•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
						case 10:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$item = Item::get(276, 0, 1);
			$enchant = Enchantment::getEnchantment(12);
			$enchant->setLevel(2);
			$item->addEnchantment($enchant);
			$gracz->sendPopup("§f• §7Gracz§4 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3Miecz Knockback 2 §f•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
									case 11:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$gracz->sendPopup("§f• §7Gracz§4 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3aKlucz §8(§32§8) §f•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(421, 0, 2));
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
									case 12:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$gracz->sendPopup("§f• §7Gracz§4 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3TNT §8(§332§8) §f•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(46, 0, 32));
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
									case 13:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$gracz->sendPopup("§f• §7Gracz§4 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3Obsydian §8(§364§8) §f•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(49, 0, 64));
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
												case 14:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$gracz->sendPopup("§f• §7Gracz§4 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3Magiczny Case §8(§31§8) §f•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(247, 0, 1));
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
			case 15:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$gracz->sendPopup("§f• §7Gracz§4 " . $name . " §7otworzyl §3TOP BOXA §7i wylosowal §3Dirt §8(§364§8) §f•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(3, 0, 64));
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
			}
}
}
public function truePolishGamemode(PlayerCommandPreprocessEvent $event){
	  $command = explode(" ", strtolower($event->getMessage()));
      $player = $event->getPlayer();
	if($command[0] === "/gamemode" or $command[0] === "/gm") {
		if($player->hasPermission("gejmod.uzyj")){
		if(empty($command[1])){
		}
		if(count($command[0]) == 1){
		if($command[1] == "1" && is_numeric($command[1])){
		$event->setCancelled();
		$player->setGamemode(1);
		$player->sendMessage("§7Ustawiles swoj tryb gry na §6KREATYWNY");
}
	}
		if(count($command[0]) == 1){
		if($command[1] == "0" && is_numeric($command[1])){
		$event->setCancelled();
		$player->setGamemode(0);
		$player->sendMessage("§7Ustawiles swoj tryb gry na §6PRZETRWANIE");
}
			}
					if(count($command[0]) == 1){
		if($command[1] == "2" && is_numeric($command[1])){
		$event->setCancelled();
		$player->setGamemode(2);
		$player->sendMessage("§7Ustawiles swoj tryb gry na §6PRZYGODA");
}
			}
					if(count($command[0]) == 1){
		if($command[1] == "3" && is_numeric($command[1])){
		$event->setCancelled();
		$player->setGamemode(3);
		$player->sendMessage("§7Ustawiles swoj tryb gry na §6OBSERWATOR");
}
			}
									}
				else{
					$player->sendMessage("§8• §7Nie mozesz tego uzyc! §8•");
				}
}
}
public function trueSpeedyPickaxe(BlockBreakEvent $event){
	$player = $event->getPlayer();
	$item = $player->getInventory()->getItemInHand()->getId();
	$item2 = $player->getInventory()->getItemInHand();
	if($item == 278 && $item2->getCustomName() == "§l§eKilof Szybkosci"){
		$efekt = Effect::getEffect(3);
		$efekt->setDuration(60);
		$efekt->setAmplifier(6);
		$player->addEffect($efekt);
	}
}
	public function trueHealth(EntityDamageEvent $event){
		if($event instanceof EntityDamageByEntityEvent && $event->getDamager() instanceof Player) {
        $hit = $event->getEntity();
        $damager = $event->getDamager();
		if($damager->hasPermission("zycie.zycie") or $damager->isOp()){
		$damager->sendPopup("§f• §7Pozostalo§3 " . $hit->getHealth() * 5 . "§7/§3" . $hit->getMaxHealth() * 5 . " §7zycia dla§3 " . $hit->getName() . " §f•");
		}
	}
}
public function trueCustomDeathMessage(PlayerDeathEvent $event){
        $cause = $event->getEntity()->getLastDamageCause();
        if($cause instanceof EntityDamageByEntityEvent){
            $killer = $cause->getDamager();
            if($killer instanceof Player){
            $event->getPlayer()->sendMessage("§f• §8[§eSV4U§8] §7Zostales zabity przez§3 " . $killer->getName() . "! §f•");
			$event->setDeathMessage("&c" . $event->getPlayer()->getName() . " &6[&c" . $this->getAPI()->getPlayerFaction($event->getPlayer()->getName()) . "&6] &6zostal zabity przez: &c" . $killer->getName() . " &6[&c" . $this->getAPI()->getPlayerFaction($killer->getName()) . "&6]&c(+25)");
			switch(mt_rand(1, 3)){
				case 1:
				$killer->sendMessage("§f• §8[§4SV4U§8] §7Otzymales efekt: §3Szybkosc I §7za zabicie§3 " . $event->getPlayer()->getName() . " §7! §f•");
				$efekt = Effect::getEffect(1);
				$efekt->setAmplifier(0);
				$efekt->setDuration(1200);
				$killer->addEffect($efekt);
				break;
								case 2:
				$killer->sendMessage("§f• §8[§4SV4U§8] §7Otzymales efekt: §3Regeneracja I §7za zabicie§3 " . $event->getPlayer()->getName() . " §7! §f•");
				$efekt = Effect::getEffect(10);
				$efekt->setAmplifier(0);
				$efekt->setDuration(1200);
				$killer->addEffect($efekt);
				break;
				case 3:
				$killer->sendMessage("§f• §8[§4SV4U§8] §7Zabiles§3 " . $event->getPlayer()->getName() . "§7! §f•");
				break;
			}
            }
}
		}
	
public function trueCustomDrops(BlockBreakEvent $event){
	$player = $event->getPlayer();
	$block = $event->getBlock();
	$cfg = new Config($this->getDataFolder() . "cobble.yml", Config::YAML);
	$s = $cfg->get($player->getName());
	$x = $block->getX();
	$y = $block->getY();
	$z = $block->getZ();
	if($player->getGamemode() == 0){
		if($block->getId() == 97){
		if(!$event->isCancelled()){
		$drops = array(Item::get(0, 0, 1));
		$event->setDrops($drops);
		if($s == 0){
		if(!$event->isCancelled()){
		if($player->getInventory()->canAddItem(Item::get(97, 0, 1))){
		$player->getInventory()->addItem(Item::get(97, 0, 1));
		}
		else{
		$player->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(4, 0, 1));
		}
		}
		}
		}
	}
		if($block->getId() == 2){
		if(!$event->isCancelled()){
		$drops = array(Item::get(0, 0, 1));
		$event->setDrops($drops);
		$player->getInventory()->addItem(Item::get(3, 0, 1));
		}
	}
		if($block->getId() == 3){
		if(!$event->isCancelled()){
		$drops = array(Item::get(0, 0, 1));
		$event->setDrops($drops);
		$player->getInventory()->addItem(Item::get(3, 0, 1));
		}
	}
		if($block->getId() == 49){
		if(!$event->isCancelled()){
		$drops = array(Item::get(0, 0, 1));
		$event->setDrops($drops);
		$player->getInventory()->addItem(Item::get(49, 0, 1));
		}
	}
		if($block->getId() == 4){
		if(!$event->isCancelled()){
		$drops = array(Item::get(0, 0, 1));
		$event->setDrops($drops);
		if($s == 0){
		if(!$event->isCancelled()){
		if($player->getInventory()->canAddItem(Item::get(4, 0, 1))){
		$player->getInventory()->addItem(Item::get(4, 0, 1));
		}
		else{
		$player->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(4, 0, 1));
		}
		}
		}
		}
	}
		if($block->getId() == 121){
		if(!$event->isCancelled()){
		$drops = array(Item::get(0, 0, 1));
		$event->setDrops($drops);
		$player->getInventory()->addItem(Item::get(121, 0, 1));
		}
	}
	    if($block->getId() == 17){
		if(!$event->isCancelled()){
		$damage = $block->getDamage();
		$drops = array(Item::get(0, 0, 1));
		$event->setDrops($drops);
		$player->getInventory()->addItem(Item::get(17, $damage, 1));
		}
		}
}
}
public function trueAntyPickup(InventoryPickupItemEvent $event){
				$sprawdz = new Config($this->getDataFolder() . "/pickup.yml", Config::YAML);
				$sprawdzs = $sprawdz->get($event->getInventory()->getHolder()->getName());
				if($sprawdzs == 0){
					$event->setCancelled();
				}
	}

	  
		 public function trueTNTOver45Block(BlockPlaceEvent $event){
			 $block = $event->getBlock();
			 $player = $event->getPlayer();
			 $y = $block->getY();
			 if($y > 45 && $block->getId() == 46){
			 $event->setCancelled();
			 $player->sendMessage("§f• §8[§eSV4U§8] §7TNT można stawiać tylko poniżej Y: §345§7! §f•");
			 }
		 }

		public function trueBlockDiamondArmorCraft(CraftItemEvent $event){
		 $item = $event->getRecipe()->getResult();
		 $player = $event->getPlayer();
		 if($item->getId() == 310 && !$player->hasPermission("craft.item.sety") or $item->getId() == 311 && !$player->hasPermission("craft.item") or $item->getId() == 312 && !$player->hasPermission("craft.item") or $item->getId() == 313 && !$player->hasPermission("craft.item") or $item->getId() == 276 && !$player->hasPermission("craft.item")){ //id armoru diamentowego
			 $event->setCancelled(true);
			 $player->sendPopup("§f• §6[§eSV4U§6] §7Nie mozesz craftowac tego itemu na czas 4 godzin od startu serwera! §f•");
	  }
		}
		public function trueBlockKoxCraft(CraftItemEvent $event){
		 $item = $event->getRecipe()->getResult();
		 $player = $event->getPlayer();
		 if($item->getId() == 23 && !$player->hasPermission("craft.item.kox") or $item->getId() == 23 && !$player->hasPermission("craft.item.kox") or $item->getId() == 23 && !$player->hasPermission("craft.item.kox") or $item->getId() == 23 && !$player->hasPermission("craft.item.kox") or $item->getId() == 23 && !$player->hasPermission("craft.item.kox")){ //id armoru diamentowego
			 $event->setCancelled(true);
			 $player->sendPopup("§f• §6[§eSV4U§6] §7Nie mozesz craftowac tego itemu! xD! §f•");
	  }
		}
		public function onRefilEat(PlayerItemConsumeEvent $event) {
			$i = $event->getItem();
            if($i->getId() == 322) {
			$player = $event->getPlayer();
            $effect = Effect::getEffect(1);
			$effect->setDuration(720);
            $effect->setAmplifier(1);
			$player->addEffect($effect);
			$effect2 = Effect::getEffect(5);
			$effect2->setDuration(500);
			$player->addEffect($effect2);
			$effect3 = Effect::getEffect(10);
			$effect3->setDuration(500);
			$effect3->setAmplifier(2);
			$player->addEffect($effect3);
      }
            }
		public function onPlayerInteractEvent(PlayerInteractEvent $event) {
        if($event->getBlock()->getId() == 19) {
            $x = rand(0, $this->getConfig()->getNested("ctRandomTeleport.max-x"));
            $y = 150;
            $z = rand(0, $this->getConfig()->getNested("ctRandomTeleport.max-z"));
            $event->getPlayer()->sendMessage("Teleportowanie w losowe miejsce...");
            $this->players[$event->getPlayer()->getName()] = true;
            $event->getPlayer()->teleport(new Position($x, $y, $z, $event->getPlayer()->getLevel()));
            $event->getPlayer()->addEffect(Effect::getEffect(9)->setAmplifier(2)->setDuration(20*10));
            $event->getPlayer()->addEffect(Effect::getEffect(1)->setAmplifier(3)->setDuration(20*15));
        }
    }
	public function onEntityDamageEvent(EntityDamageEvent $event) {
        if($event->getCause() == EntityDamageEvent::CAUSE_FALL) {
            if(isset($this->players[$event->getEntity()->getName()])) {
                $event->setCancelled(true);
                unset($this->players[$event->getEntity()->getName()]);
            }
        }
    }
	public function onBreak(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$gracz = $event->getPlayer()->getName();
		if($event->getBlock()->getId() == 129){
			if(!($event->isCancelled())){
			 switch(mt_rand(1,9)){
         case 1:
         $player->sendPopup("§eBiblioteczka x16");
         $player->getInventory()->addItem(Item::get(47, 0, 16));
         $player->addExperience(1);
         				$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$player->getInventory()->setItemInHand($item);
        $level = $this->getServer()->getDefaultLevel();
		$x = $event->getBlock()->getFloorX();
		$y = $event->getBlock()->getFloorY();
		$z = $event->getBlock()->getFloorZ();
        $center = new Vector3($x, $y, $z);
        $radius = 1; 
        $count = 100;
        $particle = new LavaParticle($center);
        for($yaw = 3, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 30, $y += 1 / 25){
        $x = -sin($yaw) + $center->x;
        $z = cos($yaw) + $center->z;
        $particle->setComponents($x, $y, $z);
        $level->addParticle($particle);
		}
		$level = $player->getLevel();
		$level->addSound(new BlazeShootSound($player));
		$event->setCancelled();
         break;
         case 2:
         $player->sendPopup("§eZelazo x10(");
         $player->getInventory()->addItem(Item::get(265, 0, 10));
         $player->addExperience(1);
                  				$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$player->getInventory()->setItemInHand($item);
                 $level = $this->getServer()->getDefaultLevel();
		$x = $event->getBlock()->getFloorX();
		$y = $event->getBlock()->getFloorY();
		$z = $event->getBlock()->getFloorZ();
        $center = new Vector3($x, $y, $z);
        $radius = 1; 
        $count = 100;
        $particle = new LavaParticle($center);
        for($yaw = 3, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 30, $y += 1 / 25){
        $x = -sin($yaw) + $center->x;
        $z = cos($yaw) + $center->z;
        $particle->setComponents($x, $y, $z);
        $level->addParticle($particle);
		}
				$level = $player->getLevel();
		$level->addSound(new BlazeShootSound($player));
		$event->setCancelled();
         break;
         case 3:
         $player->sendPopup("§eZlto x10");
         $player->getInventory()->addItem(Item::get(266, 0, 10));
         $player->addExperience(1);
                  				$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$player->getInventory()->setItemInHand($item);
                 $level = $this->getServer()->getDefaultLevel();
		$x = $event->getBlock()->getFloorX();
		$y = $event->getBlock()->getFloorY();
		$z = $event->getBlock()->getFloorZ();
        $center = new Vector3($x, $y, $z);
        $radius = 1; 
        $count = 100;
        $particle = new LavaParticle($center);
        for($yaw = 3, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 30, $y += 1 / 25){
        $x = -sin($yaw) + $center->x;
        $z = cos($yaw) + $center->z;
        $particle->setComponents($x, $y, $z);
        $level->addParticle($particle);
		}
				$level = $player->getLevel();
		$level->addSound(new BlazeShootSound($player));
		$event->setCancelled();
         break;
         case 4:
         $player->sendPopup("§eCobbleX x1");
         $player->getInventory()->addItem(Item::get(129, 0, 1));
         $player->addExperience(1);
                  				$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$player->getInventory()->setItemInHand($item);
                 $level = $this->getServer()->getDefaultLevel();
		$x = $event->getBlock()->getFloorX();
		$y = $event->getBlock()->getFloorY();
		$z = $event->getBlock()->getFloorZ();
        $center = new Vector3($x, $y, $z);
        $radius = 1; 
        $count = 100;
        $particle = new LavaParticle($center);
        for($yaw = 3, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 30, $y += 1 / 25){
        $x = -sin($yaw) + $center->x;
        $z = cos($yaw) + $center->z;
        $particle->setComponents($x, $y, $z);
        $level->addParticle($particle);
		}
				$level = $player->getLevel();
		$level->addSound(new BlazeShootSound($player));
		$event->setCancelled();
         break;
         case 5:
         $player->sendMessage("§eDiamenty x12");
         $player->getInventory()->addItem(Item::get(264, 0, 12));
         $player->addExperience(1);
                  				$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$player->getInventory()->setItemInHand($item);
                 $level = $this->getServer()->getDefaultLevel();
		$x = $event->getBlock()->getFloorX();
		$y = $event->getBlock()->getFloorY();
		$z = $event->getBlock()->getFloorZ();
        $center = new Vector3($x, $y, $z);
        $radius = 1; 
        $count = 100;
        $particle = new LavaParticle($center);
        for($yaw = 3, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 30, $y += 1 / 25){
        $x = -sin($yaw) + $center->x;
        $z = cos($yaw) + $center->z;
        $particle->setComponents($x, $y, $z);
        $level->addParticle($particle);
		}
				$level = $player->getLevel();
		$level->addSound(new BlazeShootSound($player));
		$event->setCancelled();
         break;
         case 6:
         $player->sendPopup("§ePerla x1");
         $player->getInventory()->addItem(Item::get(332, 0, 1));
         $player->addExperience(1);
                  				$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$player->getInventory()->setItemInHand($item);
                 $level = $this->getServer()->getDefaultLevel();
		$x = $event->getBlock()->getFloorX();
		$y = $event->getBlock()->getFloorY();
		$z = $event->getBlock()->getFloorZ();
        $center = new Vector3($x, $y, $z);
        $radius = 1; 
        $count = 100;
        $particle = new LavaParticle($center);
        for($yaw = 3, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 30, $y += 1 / 25){
        $x = -sin($yaw) + $center->x;
        $z = cos($yaw) + $center->z;
        $particle->setComponents($x, $y, $z);
        $level->addParticle($particle);
		}
				$level = $player->getLevel();
		$level->addSound(new BlazeShootSound($player));
		$event->setCancelled();
         break;
         case 7:
         $player->sendMessage("§2Trafiles na przedmioty: §2Jablka x12");
         $player->getInventory()->addItem(Item::get(260, 0, 12));
         $player->addExperience(1);
                  				$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$player->getInventory()->setItemInHand($item);
                 $level = $this->getServer()->getDefaultLevel();
		$x = $event->getBlock()->getFloorX();
		$y = $event->getBlock()->getFloorY();
		$z = $event->getBlock()->getFloorZ();
        $center = new Vector3($x, $y, $z);
        $radius = 1; 
        $count = 100;
        $particle = new LavaParticle($center);
        for($yaw = 3, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 30, $y += 1 / 25){
        $x = -sin($yaw) + $center->x;
        $z = cos($yaw) + $center->z;
        $particle->setComponents($x, $y, $z);
        $level->addParticle($particle);
		}
				$level = $player->getLevel();
		$level->addSound(new BlazeShootSound($player));
		$event->setCancelled();
         break;
         case 8:
         $player->sendPopup("§eZelazo x8");
         $player->getInventory()->addItem(Item::get(265, 0, 8));
         $player->addExperience(1);
                  				$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$player->getInventory()->setItemInHand($item);
                 $level = $this->getServer()->getDefaultLevel();
		$x = $event->getBlock()->getFloorX();
		$y = $event->getBlock()->getFloorY();
		$z = $event->getBlock()->getFloorZ();
        $center = new Vector3($x, $y, $z);
        $radius = 1; 
        $count = 100;
        $particle = new LavaParticle($center);
        for($yaw = 3, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 30, $y += 1 / 25){
        $x = -sin($yaw) + $center->x;
        $z = cos($yaw) + $center->z;
        $particle->setComponents($x, $y, $z);
        $level->addParticle($particle);
		}
				$level = $player->getLevel();
		$level->addSound(new BlazeShootSound($player));
		$event->setCancelled();
         break;
         case 9:
         $player->sendMessage("§eEmerald x10");
         $player->getInventory()->addItem(Item::get(366, 0, 10));
         $player->addExperience(1);
                  				$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$player->getInventory()->setItemInHand($item);
                 $level = $this->getServer()->getDefaultLevel();
		$x = $event->getBlock()->getFloorX();
		$y = $event->getBlock()->getFloorY();
		$z = $event->getBlock()->getFloorZ();
        $center = new Vector3($x, $y, $z);
        $radius = 1; 
        $count = 100;
        $particle = new LavaParticle($center);
        for($yaw = 3, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 30, $y += 1 / 25){
        $x = -sin($yaw) + $center->x;
        $z = cos($yaw) + $center->z;
        $particle->setComponents($x, $y, $z);
        $level->addParticle($particle);
		}
				$level = $player->getLevel();
		$level->addSound(new BlazeShootSound($player));
		$event->setCancelled();
         break;
}
	}
}
	}
		}
