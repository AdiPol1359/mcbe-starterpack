<?php

namespace Gildie;

use Gildie\fakeinventory\SkarbiecInventory;
use Gildie\guild\GuildManager;
use Gildie\task\SetHeartTask;
use Gildie\task\UpdateSkarbiecTask;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\event\Listener;

use pocketmine\item\Item;
use pocketmine\item\ItemBlock;
use pocketmine\Player;

use pocketmine\block\Block;

use pocketmine\entity\Entity;

use pocketmine\event\player\{PlayerChatEvent, PlayerCommandPreprocessEvent, PlayerInteractEvent, PlayerMoveEvent};

use pocketmine\event\block\{
    BlockBreakEvent, BlockPlaceEvent
};

use pocketmine\event\entity\{
    EntityDamageEvent, EntityDamageByEntityEvent, EntityExplodeEvent
};

use pocketmine\event\inventory\InventoryTransactionEvent;
use Gildie\fakeinventory\FakeInventoryAPI;
use Gildie\bossbar\{
	Bossbar, BossbarManager
};
use Gildie\Main;
use pocketmine\Server;
use Core\api\NameTagsAPI;

class EventListener implements Listener
{

    private $guildTerrain = [];
    private $primedExplode = [];

    public function onBreak(BlockBreakEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();
        $x = $block->getFloorX();
        $y = $block->getFloorY();
        $z = $block->getFloorZ();

        $guildManager = Main::getInstance()->getGuildManager();

        if ($player->hasPermission("nicecraft.guilds.op")) return;

        if ($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if(!$guildManager->isInOwnPlot($player, $block)) {
                $e->setCancelled(true);
                $player->sendMessage(Main::format("Ten teren jest zajety przez gildie!"));
            } else {
                if(in_array($block->getId(), [Block::CHEST, Block::FURNACE, Block::BEACON]))
                    return;

                $core_api = $player->getServer()->getPluginManager()->getPlugin("Core");
                if($core_api != null) {
                    $array = $core_api->getDb()->query("SELECT * FROM stoniarki WHERE x = '$x' AND y = '$y' AND z = '$z'")->fetchArray(SQLITE3_ASSOC);
                    if(!empty($array))
                        return;
                }

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_BLOCKS_BREAK)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do niszczenia blokow");
                }
            }
        }
    }

    public function ProtectGuildPlace(BlockPlaceEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();
        $item = $e->getItem();

        $guildManager = Main::getInstance()->getGuildManager();

        if($player->hasPermission("nicecraft.guilds.op")) return;

        if($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if(!$guildManager->isInOwnPlot($player, $block)) {
                $e->setCancelled(true);
                $player->sendMessage(Main::format("Ten teren jest zajety przez gildie!"));
            } else {

                $guild = $guildManager->getPlayerGuild($player->getName());

                if(isset($this->primedExplode[$guild->getTag()])) {
                    $time = time() - $this->primedExplode[$guild->getTag()];

                    if($time < 30) {
                        $e->setCancelled(true);
                        $player->sendMessage(Main::format("Na terenie Twojej gildii wybuchlo §4TNT§7, nie mozesz budowac jeszcze przez §4".(30-$time)." §7sekund!"));
                    } else
                        unset($this->primedExplode[$guild->getTag()]);
                }

                if(in_array($block->getId(), [Block::TNT, Block::CHEST, Block::FURNACE, Block::BEACON]))
                    return;

                if(strpos($item->getName(), "Generator Kamienia"))
                    return;

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_BLOCKS_PLACE)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do stawiania blokow");
                }
            }
        }
    }

    public function WaterAndLava(PlayerInteractEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();
        $item = $player->getInventory()->getItemInHand();

        $guildManager = Main::getInstance()->getGuildManager();

        if($player->hasPermission("nicecraft.guilds.op")) return;

        if($item->getId() !== Item::BUCKET)
            return;

        if($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if(!$guildManager->isInOwnPlot($player, $block))
                $e->setCancelled(true);
        }
    }

    public function Permission_TNTPlace(BlockPlaceEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        $guildManager = Main::getInstance()->getGuildManager();

        if ($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if($guildManager->isInOwnPlot($player, $block)) {
                if($block->getId() != Block::TNT)
                    return;

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_TNT_PLACE)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do stawiania TNT");
                }
            }
        }
    }

    public function Permission_ChestPlace(BlockPlaceEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        $guildManager = Main::getInstance()->getGuildManager();

        if ($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if($guildManager->isInOwnPlot($player, $block)) {
                if($block->getId() != Block::CHEST)
                    return;

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_CHEST_PLACE_BREAK)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do stawiania skrzynek");
                }
            }
        }
    }

    public function Permission_ChestBreak(BlockBreakEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        $guildManager = Main::getInstance()->getGuildManager();

        if ($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if($guildManager->isInOwnPlot($player, $block)) {
                if($block->getId() != Block::CHEST)
                    return;

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_CHEST_PLACE_BREAK)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do niszczenia skrzynek");
                }
            }
        }
    }

    public function Permission_ChestOpen(PlayerInteractEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        $guildManager = Main::getInstance()->getGuildManager();

        if ($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if($guildManager->isInOwnPlot($player, $block)) {
                if($block->getId() != Block::CHEST)
                    return;

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_CHEST_OPEN)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do otwierania skrzynek");
                }
            }
        }
    }

    public function Permission_FurnacePlace(BlockPlaceEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        $guildManager = Main::getInstance()->getGuildManager();

        if ($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if($guildManager->isInOwnPlot($player, $block)) {
                if($block->getId() != Block::FURNACE)
                    return;

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_FURNACE_PLACE_BREAK)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do stawiania piecy");
                }
            }
        }
    }

    public function Permission_FurnaceBreak(BlockBreakEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        $guildManager = Main::getInstance()->getGuildManager();

        if ($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if($guildManager->isInOwnPlot($player, $block)) {
                if($block->getId() != Block::FURNACE)
                    return;

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_FURNACE_PLACE_BREAK)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do niszczenia piecy");
                }
            }
        }
    }

    public function Permission_FurnaceOpen(PlayerInteractEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        $guildManager = Main::getInstance()->getGuildManager();

        if ($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if($guildManager->isInOwnPlot($player, $block)) {
                if($block->getId() != Block::FURNACE)
                    return;

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_FURNACE_OPEN)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do otwierania piecy");
                }
            }
        }
    }

    public function tpaccept(PlayerCommandPreprocessEvent $e) {
        $player = $e->getPlayer();

        $guildManager = Main::getInstance()->getGuildManager();

        if($e->getMessage()[0] == "/") {
            $cmd = explode(" ", $e->getMessage())[0];

            if($cmd !== "/tpaccept")
                return;

            if ($guildManager->isPlot($player->getFloorX(), $player->getFloorZ())) {
                if ($guildManager->isInOwnPlot($player, $player)) {

                    if (!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_TPACCEPT)) {
                        $e->setCancelled(true);
                        $player->sendMessage("§8§l>§r §7Nie masz permisji do akceptowania teleportacji innych na terenie gildii");
                    }
                }
            }
        }
    }

    public function setHomes(PlayerCommandPreprocessEvent $e) {
        $player = $e->getPlayer();

        $guildManager = Main::getInstance()->getGuildManager();

        if($e->getMessage()[0] == "/") {
            $cmd = explode(" ", $e->getMessage())[0];

            if($cmd !== "/sethome")
                return;

            if($guildManager->isPlot($player->getFloorX(), $player->getFloorZ())) {
                if(!$guildManager->isInOwnPlot($player, $player)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie mozesz zakladac home'ów na terenie cudzej gildii!");
                }
            }
        }
    }

    public function Permission_Lava(PlayerInteractEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();
        $item = $player->getInventory()->getItemInHand();

        $guildManager = Main::getInstance()->getGuildManager();

        if ($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if($guildManager->isInOwnPlot($player, $block)) {
                if(!($item->getId() == 325 && $item->getDamage() == 10))
                    return;

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_LAVA)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do wylewania lawy");
                }
            }
        }
    }

    public function Permission_Water(PlayerInteractEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();
        $item = $player->getInventory()->getItemInHand();

        $guildManager = Main::getInstance()->getGuildManager();

        if ($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if($guildManager->isInOwnPlot($player, $block)) {
                if(!($item->getId() == 325 && $item->getDamage() == 8))
                    return;

                if(in_array($block->getId(), [Block::LEVER, Block::WOODEN_BUTTON, Block::STONE_BUTTON]))
                    return;

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_WATER)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do wylewania wody");
                }
            }
        }
    }

    public function Permission_Interact(PlayerInteractEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();
        $item = $player->getInventory()->getItemInHand();

        $guildManager = Main::getInstance()->getGuildManager();

        if ($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if($guildManager->isInOwnPlot($player, $block)) {

                if(!in_array($block->getId(), [Block::LEVER, Block::WOODEN_BUTTON, Block::STONE_BUTTON]))
                    return;

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_INTERACT)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do intreakcji");
                }
            }
        }
    }

    public function Permission_BeaconPlace(BlockPlaceEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        $guildManager = Main::getInstance()->getGuildManager();

        if ($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if($guildManager->isInOwnPlot($player, $block)) {
                if($block->getId() != Block::BEACON)
                    return;

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_BEACON_PLACE_BREAK)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do stawiania beaconow");
                }
            }
        }
    }

    public function Permission_BeaconBreak(BlockBreakEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        $guildManager = Main::getInstance()->getGuildManager();

        if ($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if($guildManager->isInOwnPlot($player, $block)) {
                if($block->getId() != Block::BEACON)
                    return;

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_BEACON_PLACE_BREAK)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do niszczenia beaconow");
                }
            }
        }
    }

    public function Permission_BeaconOpen(PlayerInteractEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        $guildManager = Main::getInstance()->getGuildManager();

        if ($guildManager->isPlot($block->getFloorX(), $block->getFloorZ())) {
            if($guildManager->isInOwnPlot($player, $block)) {
                if($block->getId() != Block::BEACON)
                    return;

                if(!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_BEACON_OPEN)) {
                    $e->setCancelled(true);
                    $player->sendMessage("§8§l>§r §7Nie masz permisji do otwierania beaconow");
                }
            }
        }
    }

    public function onEntityDamage(EntityDamageEvent $e)
    {
        if ($e instanceof EntityDamageByEntityEvent) {
            $entity = $e->getEntity();
            $damager = $e->getDamager();

            $guildManager = Main::getInstance()->getGuildManager();

            if ($entity instanceof Player && $damager instanceof Player && $entity->getName() !== $damager->getName()) {
                if ($guildManager->isInGuild($entity->getName()) && $guildManager->isInGuild($damager->getName())) {
                    if ($guildManager->isInSameGuild($entity, $damager)) {
                        if (!$guildManager->getPlayerGuild($damager->getName())->isGuildPvP())
                            $e->setCancelled(true);
                    } elseif ($guildManager->getPlayerGuild($entity->getName())->hasAllianceWith($guildManager->getPlayerGuild($damager->getName()))) {
                        if (!$guildManager->getPlayerGuild($damager->getName())->isAlliancesPvP())
                            $e->setCancelled(true);
                    }
                }
            }
        }
    }

    public function HeartProtect(BlockBreakEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        $guildManager = Main::getInstance()->getGuildManager();

        if($guildManager->isHeart($block)) {
            $e->setCancelled(true);

            $player->sendMessage(Main::format("Nie mozesz zniszczyc serca gildii!"));
        }
    }

    public function HeartExplodeProtect(EntityExplodeEvent $e) {
        foreach($e->getBlockList() as $block) {
            if(Main::getInstance()->getGuildManager()->isHeart($block))
                Main::getInstance()->getScheduler()->scheduleDelayedTask(new SetHeartTask($block), 5);
        }
    }

    public function BlockBlocksPlaceOnPrimeExplode(ExplosionPrimeEvent $e) {
        $entity = $e->getEntity();

        $guildManager = Main::getInstance()->getGuildManager();

        if($guildManager->isPlot($entity->getFloorX(), $entity->getFloorZ())) {
            $guild = $guildManager->getGuildFromPos($entity->getFloorX(), $entity->getFloorZ());
            $this->primedExplode[$guild->getTag()] = time();

            foreach($guild->getPlayers() as $nick) {
                $player = Server::getInstance()->getPlayerExact($nick);

                if($player)
                    $player->sendMessage(Main::format("Na terenie Twojej gildii wybuchlo §4TNT§7, nie mozesz budowac jeszcze przez §430 §7sekund!"));
            }
        }
    }


    public function onConquer(PlayerInteractEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        $guildManager = Main::getInstance()->getGuildManager();

        if($guildManager->isHeart($block)) {
            if(!$guildManager->isInGuild($player->getName())) {
                $player->sendMessage(Main::format("Musisz byc w gildii aby podbic inna gildie!"));
                return;
            }

            $guild = $guildManager->getGuildFromHeart($block);
            $pGuild = $guildManager->getPlayerGuild($player->getName());

            if($pGuild === $guild) {
                if($e->getAction() == $e::RIGHT_CLICK_BLOCK && $e->getItem() instanceof ItemBlock)
                    return;

                $e->setCancelled(true);
                $player->sendMessage(Main::format("Nie mozesz podbic swojej gildii!"));
            } else {
                if(!$guild->canConquer()) {
                    $conquerTime = strtotime($guild->getConquerDate()) - time();

                    $conquerH = floor($conquerTime / 3600);
                    $conquerM = floor(($conquerTime / 60) % 60);
                    $conquerS = $conquerTime % 60;

                    $player->sendMessage("§7Ta gildie mozna podbic za: §4$conquerH §7godzin, §4$conquerM §7minut i §4$conquerS §7sekund");
                } else {
                    $guild->setLifes($guild->getLifes() - 1);

                    if($guild->getLifes() <= 0) {
                        $player->getServer()->broadcastMessage(Main::format("Gildia §l§4{$pGuild->getTag()} §r§7odebrala ostatnie serce gildii §l§4{$guild->getTag()}"));
                        $guild->remove($player->getLevel());
                    } else {
                        $player->getServer()->broadcastMessage(Main::format("Gildia §l§4{$pGuild->getTag()} §r§7odebrala §41 §7serce gildii §l§4{$guild->getTag()}"));

                        $date = date_create(date("H:i:s", time()));
                        date_add($date,date_interval_create_from_date_string("1 days"));
                        $guild->setConquerDate(date_format($date,"d.m.Y H:i:s"));
                    }
                }
            }
        }
    }

    public function guildChat(PlayerChatEvent $e) {
        $player = $e->getPlayer();
        $msg = $e->getMessage();

        $guildManager = Main::getInstance()->getGuildManager();

        if($guildManager->isInGuild($player->getName())) {
        	  if(!isset($msg[1]))
        	   return;
        	   
            if($msg[0] == "!" && $msg[1] != "!") {
                $msg = substr($msg, 1);
                $e->setCancelled(true);
                $guildManager->getPlayerGuild($player->getName())->messageToMembers("§4[GILDIA] {$player->getName()}: $msg");
            }
        }
    }

    public function allaincesChat(PlayerChatEvent $e) {
        $player = $e->getPlayer();
        $msg = $e->getMessage();

        $guildManager = Main::getInstance()->getGuildManager();

        if($guildManager->isInGuild($player->getName())) {
        	  if(!isset($msg[2]))
        	   return;
        	  
            if($msg[0] == "!" && $msg[1] == "!") {
                $e->setCancelled(true);

                $guild = $guildManager->getPlayerGuild($player->getName());

                $msg = substr($msg, 2);
                $message = "§6[{$guild->getTag()}] {$player->getName()}: $msg";

                $guild->messageToMembers($message);

                foreach($guild->getAlliances() as $tag) {
                    $guildManager->getGuildByTag($tag)->messageToMembers($message);
                }
            }
        }
    }

    public function needHelp(PlayerChatEvent $e) {
        $player = $e->getPlayer();
        $msg = $e->getMessage();

        $guildManager = Main::getInstance()->getGuildManager();

        if($guildManager->isInGuild($player->getName())) {
            if($msg == "#") {
                $e->setCancelled(true);
                $guildManager->getPlayerGuild($player->getName())->messageToMembers(Main::formatLines([
                    "Gracz §l§4{$player->getName()} §r§7potrzebuje pomocy!",
                    "Jego kordy to X: §l§4{$player->getFloorX()} §r§7Y: §l§4{$player->getFloorY()} §r§7Z: §l§4{$player->getFloorZ()}"
                ]));
            }
        }
    }

    public function BazaTpMoveCancel(PlayerMoveEvent $e) {
        $player = $e->getPlayer();
        $nick = $player->getName();

        if(isset(Main::$bazaTask[$nick])) {
            if(!($e->getTo()->floor()->equals($e->getFrom()->floor()))) {
                Main::$bazaTask[$nick]->cancel();
                unset(Main::$bazaTask[$nick]);
                $player->sendMessage("§8§l>§r §7Teleportacja do bazy gildii zostala przerwana!");
                $player->removeEffect(9);
            }
        }
    }

    public function BossbarAndMessage(PlayerMoveEvent $e) {
        if($e->getFrom()->floor()->equals($e->getTo()->floor()))
            return;

        $player = $e->getPlayer();
        $x = $player->getFloorX();
        $z = $player->getFloorZ();
        $nick = $player->getName();

        $ownTitle = "§4Jestes na terenie swojej gildii §7[§e{TAG}§7]";
        $enemyTitle = "§4Teren gildii §7[§e{TAG}§7] - §e{NAME}";

        $bossbar = BossbarManager::getBossbar($player);
        $guildManager = Main::getInstance()->getGuildManager();

        if($guildManager->isPlot($x, $z)) {
            if($bossbar == null) {
                $bossbar = new Bossbar("");
                $bossbar->showTo($player);
            }

            $guild = $guildManager->getGuildFromPos($x, $z);
            $tag = $guild->getTag();
            $name = $guild->getName();

            if($guildManager->isInOwnPlot($player, $player->asVector3())) {
                $title = str_replace("{TAG}", $tag, $ownTitle);
                if($bossbar->getTitle() != $title)
                    $bossbar->setTitle($title);

                if(!isset($this->guildTerrain[$nick])) {
                    $this->guildTerrain[$nick] = [$tag, $name];
                    $player->sendMessage("§8§l>§r §7Wkroczyles na teren swojej gildii");
                }
            } else {
                $title = str_replace("{TAG}", $tag, $enemyTitle);
                $title = str_replace("{NAME}", $name, $title);
                if($bossbar->getTitle() != $title)
                    $bossbar->setTitle($title);

                if(!isset($this->guildTerrain[$nick])) {
                    $this->guildTerrain[$nick] = [$tag, $name];
                    $player->sendMessage("§8§l>§r §7Wkroczyles na teren gildii §8[§4{$tag}§8] §8- §4{$name}");
                    $player->addTitle("§7Wkroczyles na teren\ngildii §l§4{$tag}");

                    if($player->hasPermission("nicecraft.guilds.op"))
                        return;

                    foreach($guild->getPlayers() as $nick) {
                        $p = $player->getServer()->getPlayerExact($nick);

                        if($p)
                            $p->sendMessage("§8§l>§r §7Gracz §4{$player->getName()} §7wkroczyl na teren Twojej gildii!");
                    }
                }
            }
        } else {
            if($bossbar != null)
                $bossbar->hideFrom($player);

            if(isset($this->guildTerrain[$nick])) {
                $tag = $this->guildTerrain[$nick][0];
                $name = $this->guildTerrain[$nick][1];
                unset($this->guildTerrain[$nick]);

                $guild = $guildManager->getPlayerGuild($nick);

                if($guild != null && $guild->getTag() == $tag)
                    $player->sendMessage("§8§l>§r §7Opusciles teren swojej gildii");
                else
                    $player->sendMessage("§8§l>§r §7Opusciles teren gildii §8[§4{$tag}§8] §8- §4{$name}");
            }
        }
    }

    public function FakeInventoryTransaction(InventoryTransactionEvent $e){
        $trans = $e->getTransaction()->getActions();
        $invs = $e->getTransaction()->getInventories();
        $player = $e->getTransaction()->getSource();
        $item = null;

        if(FakeInventoryAPI::isOpening($player)){
            $gui = FakeInventoryAPI::getInventory($player);

            if($gui instanceof SkarbiecInventory) {
                if(!$gui->getGuild()->canSkarbiecTransaction($player)) {
                    $e->setCancelled(true);
                    return;
                }
                $gui->getGuild()->setCanSkarbiecTransaction($player, false);
                Main::getInstance()->getScheduler()->scheduleDelayedTask(new UpdateSkarbiecTask($player, $gui), 1);
                return;
            }

            foreach($trans as $t) {
                foreach($invs as $inv) {
                    if($inv instanceof $gui) {
                        if($item == null && $t->getTargetItem()->getName() !== "Air")
                            $item = $t->getTargetItem();
                    }
                }
            }

            if($gui->cancelTransaction())
                $e->setCancelled(true);

            $gui->onTransaction($player, $item);
        }
    }
}