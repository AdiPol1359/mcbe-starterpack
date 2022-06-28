<?php

namespace Core;

use Core\api\NameTagsAPI;
use Core\api\ParticlesyAPI;
use Core\api\ProtectAPI;
use Core\bossbar\Bossbar;
use Core\Main;
use Gildie\guild\GuildManager;
use Core\task\BugowanieTask;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\Listener;
use pocketmine\entity\Effect;
use pocketmine\Player;

use pocketmine\Server;

use pocketmine\entity\{Entity, Living, projectile\EnderPearl};

use pocketmine\event\player\{PlayerCreationEvent,
    PlayerMoveEvent,
    PlayerJoinEvent,
    PlayerQuitEvent,
    PlayerCommandPreprocessEvent,
    PlayerDeathEvent,
    PlayerInteractEvent,
    PlayerChatEvent,
    PlayerPreLoginEvent,
    PlayerRespawnEvent,
    PlayerItemConsumeEvent,
    PlayerExhaustEvent,
    PlayerAnimationEvent};


use pocketmine\event\entity\{
	EntityDamageEvent, EntityDamageByEntityEvent, ProjectileLaunchEvent, ProjectileHitEvent, EntityLevelChangeEvent
};

use pocketmine\event\server\DataPacketReceiveEvent;

use pocketmine\event\inventory\InventoryPickupItemEvent;

use pocketmine\event\block\{
	BlockPlaceEvent, BlockBreakEvent
};

use pocketmine\event\inventory\CraftItemEvent;

use pocketmine\item\{Arrow, Item, Tool, Armor, Sword, ChainBoots, DiamondBoots, GoldBoots, IronBoots, LeatherBoots};
use Core\item\Bow;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;

use pocketmine\block\{
	Block, Stair, Air
};

use pocketmine\math\Vector3;

use pocketmine\level\{
	Location, Position
};

use pocketmine\level\particle\{
    ExplodeParticle, FlameParticle
};

use pocketmine\network\mcpe\protocol\{
	AddActorPacket, PlaySoundPacket
};

use Core\form\{
	Form, DropForm, EnchantSwordForm, EnchantToolsForm, EnchantArmorForm, EnchantBowForm
};

use Core\task\{SetNameTagDeviceTask, SetNameTagTask, StoniarkaTask};

use Core\inventory\{
	EnderchestInventory, PreprocessEnderchestInventory
};
use pocketmine\network\mcpe\protocol\{GameRulesChangedPacket, LevelSoundEventPacket, LoginPacket, PlayerActionPacket};
use Core\api\CpsAPI;
use Core\entity\Villager;

use Core\api\LobbyAPI;
use Core\inventory\VillagerInventory;

use Core\bossbar\BossbarManager;
use Core\CorePlayer;

class EventListener implements Listener
{

    private $isInAir = [];
    private $isOnStair = [];

    /*
    public function bugowanie(BlockPlaceEvent $e) {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        if($e->isCancelled()) {
            $player->sendMessage("Bugowanie");

            if($block->equals($player->floor()->add(0, -1)))
                $player->resetFallDistance();

            Main::getInstance()->getScheduler()->scheduleRepeatingTask(new BugowanieTask($player, $e->getBlock()), 1);
        }
    }*/

    public function registerPlayerClass(PlayerCreationEvent $e)
    {
        $e->setPlayerClass(CorePlayer::class);
    }

    public function setNameTagOnJoin(PlayerJoinEvent $e)
    {
        Main::getInstance()->getScheduler()->scheduleDelayedTask(new SetNameTagTask($e->getPlayer()), 10);
    }

    public function strengthDamage(EntityDamageEvent $e)
    {
        if ($e instanceof EntityDamageByEntityEvent) {
            $damager = $e->getDamager();

            if (!$damager instanceof Living)
                return;

            if ($damager->hasEffect(Effect::STRENGTH)) {
                $level = $damager->getEffect(Effect::STRENGTH)->getEffectLevel();
                $damage = $e->getBaseDamage() * ($level * 0.39);
                $e->setModifier($damage, EntityDamageEvent::MODIFIER_STRENGTH);
            }
        }
    }

    public function stairsDamage(PlayerMoveEvent $e)
    {
        if ($e->getFrom()->floor()->equals($e->getTo()))
            return;

        $player = $e->getPlayer();
        $nick = $player->getName();

        $lastPos = $e->getFrom();

        $blocks = [];

        $isOnStair = (isset($this->isOnStair[$nick]) ? $this->isOnStair[$nick] : false);

        $blocks[0] = $player->getLevel()->getBlock($player->add(0, -1));
        $blocks[1] = $player->getLevel()->getBlock($player->add(0, -2));
        $blocks[2] = $player->getLevel()->getBlock($lastPos->add(0, -1));
        $blocks[3] = $player->getLevel()->getBlock($lastPos->add(0, -2));

        if ($isOnStair)
            $player->resetFallDistance();

        if ($blocks[2] instanceof Air && $blocks[3] instanceof Air)
            $this->isInAir[$nick] = true;
        else
            $this->isInAir[$nick] = false;

        if ($blocks[0] instanceof Stair || ($blocks[0] instanceof Air && $blocks[1] instanceof Stair) && !$this->isInAir[$nick])
            $this->isOnStair[$nick] = true;
        else
            $this->isOnStair[$nick] = false;
    }

    public function AntyLogoutSpawnKnockback(PlayerMoveEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        $terrainName = ProtectAPI::getTerrainNameFromPos($player);

        if (isset(Main::$antylogoutPlayers[$nick])) {
            if ($terrainName == "spawn" || $terrainName == "spawn-fly") {
                $x = $player->getFloorX() - $player->getLevel()->getSafeSpawn()->getFloorX();
                $z = $player->getFloorZ() - $player->getLevel()->getSafeSpawn()->getFloorZ();

                $player->knockBack($player, 0, $x, $z, 0.5);
            }
        }
    }

    public function saveEnderChestOnQuit(PlayerQuitEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $e->getPlayer()->getName();

        if (isset(Main::$ec[$nick]))
            Main::$ec[$nick]->onClose($player);
    }

    public function armorClickAir(PlayerInteractEvent $e)
    {
        $player = $e->getPlayer();
        $action = $e->getAction();
        $item = $player->getInventory()->getItemInHand();

        if ($action == $e::RIGHT_CLICK_AIR || $action == $e::RIGHT_CLICK_AIR) {
            if ($item instanceof Armor) {
                $id = $item->getId();

                $slot = -1;

                if (in_array($id, [Item::LEATHER_HELMET, Item::TURTLE_HELMET, Item::CHAIN_HELMET, Item::DIAMOND_HELMET, Item::GOLD_HELMET, Item::IRON_HELMET]))
                    $slot = 0;

                if (in_array($id, [Item::LEATHER_CHESTPLATE, Item::CHAIN_CHESTPLATE, Item::DIAMOND_CHESTPLATE, Item::GOLD_CHESTPLATE, Item::IRON_CHESTPLATE]))
                    $slot = 1;

                if (in_array($id, [Item::LEATHER_LEGGINGS, Item::CHAIN_LEGGINGS, Item::DIAMOND_LEGGINGS, Item::GOLD_LEGGINGS, Item::IRON_LEGGINGS]))
                    $slot = 2;

                if (in_array($id, [Item::LEATHER_BOOTS, Item::CHAIN_BOOTS, Item::DIAMOND_BOOTS, Item::GOLD_BOOTS, Item::IRON_BOOTS]))
                    $slot = 3;

                if ($slot == -1)
                    return;

                if ($player->getArmorInventory()->getItem($slot)->isNull()) {
                    $player->getArmorInventory()->setItem($slot, $item);
                    $player->getInventory()->setItemInHand(Item::get(Item::AIR));
                }
            }
        }
    }

    public function blockHitsCPS(EntityDamageEvent $e)
    {
        if ($e instanceof EntityDamageByEntityEvent) {
            $damager = $e->getDamager();

            if (!$damager instanceof Player)
                return;

            if (isset(CpsAPI::$blocks[$damager->getName()]))
                $e->setCancelled(true);
        }
    }

    public function setDefaultCpsData(PlayerJoinEvent $e)
    {
        CpsAPI::setDefaultData($e->getPlayer());
    }

    public function clickDetection(DataPacketReceiveEvent $e)
    {
        $packet = $e->getPacket();
        $player = $e->getPlayer();

        if ($packet instanceof LevelSoundEventPacket) {
            if ($packet->sound == $packet::SOUND_ATTACK_NODAMAGE || $packet->sound == $packet::SOUND_ATTACK_STRONG) {
                $e->setCancelled(true);
                CpsAPI::addClick($player);
            }
        }
    }

    public function spawnFly(PlayerMoveEvent $e)
    {
        $player = $e->getPlayer();

        if ($e->getTo()->equals($e->getFrom()))
            return;

        if ($player->isOp() || !$player->hasPermission("PolishHard.spawn.fly"))
            return;

        $terrainName = ProtectAPI::getTerrainNameFromPos($player);

        if ($terrainName == "spawn-fly")
            $player->setAllowFlight(true);
        else {
            $player->setAllowFlight(false);
            $player->setFlying(false);
        }
    }

    public function terrainBlockBreak(BlockBreakEvent $e)
    {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        if (!ProtectAPI::canBreak($player, $block))
            $e->setCancelled(true);
    }

    public function terrainBlockPlace(BlockPlaceEvent $e)
    {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        if (!ProtectAPI::canPlace($player, $block))
            $e->setCancelled(true);
    }

    public function terrainInteract(PlayerInteractEvent $e)
    {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        if (!ProtectAPI::canInteract($player, $block))
            $e->setCancelled(true);
    }

    public function terrainWhiteBlocks(BlockBreakEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        $block = $e->getBlock();

        if (isset(Main::$setWhiteBlock[$nick])) {
            $e->setCancelled(true);
            $terrainName = Main::$setWhiteBlock[$nick];
            ProtectAPI::addWhiteBlock($terrainName, $block);
            $player->sendMessage("§8§l>§r §7Dodano ten blok do listy bialych blokow");
        } elseif (isset(Main::$removeWhiteBlock[$nick])) {
            $e->setCancelled(true);
            $terrainName = Main::$removeWhiteBlock[$nick];
            ProtectAPI::removeWhiteBlock($terrainName, $block);
            $player->sendMessage("§8§l>§r §7Usunieto ten blok z listy bialych blokow");
        }
    }

    public function terrainEntity(EntityDamageEvent $e)
    {
        $entity = $e->getEntity();

        if ($e instanceof EntityDamageByEntityEvent)
            return;

        if (!ProtectAPI::canDamage($entity))
            $e->setCancelled(true);
    }

    public function terrainEntityByEntity(EntityDamageEvent $e)
    {
        $entity = $e->getEntity();

        if ($e instanceof EntityDamageByEntityEvent) {
            $damager = $e->getDamager();
            if (!ProtectAPI::canDamageEntity($entity, $damager))
                $e->setCancelled(true);
        }
    }

    public function protectPositionsChoose(BlockBreakEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        $block = $e->getBlock();

        if (isset(ProtectAPI::$data[$nick])) {
            $e->setCancelled(true);
            if (!isset(ProtectAPI::$data[$nick][0])) {
                ProtectAPI::$data[$nick][0] = $block->asVector3();
                $player->sendMessage("§8§l>§r §7Wybierz §42 §7pozycje");
            } elseif (!isset(ProtectAPI::$data[$nick][1])) {
                ProtectAPI::$data[$nick][1] = $block->asVector3();
                $player->sendMessage("§8§l>§r §7Napisz na chacie nazwe terenu");
            }
        }
    }

    public function protectCreateTerrain(PlayerChatEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        $terrainName = $e->getMessage();

        if (isset(ProtectAPI::$data[$nick])) {
            if (isset(ProtectAPI::$data[$nick][0]) && isset(ProtectAPI::$data[$nick][1])) {
                $e->setCancelled(true);
                if (ProtectAPI::isTerrainExists($terrainName)) {
                    $player->sendMessage("§8§l>§r §7Teren o takiej nazwie juz istnieje, uzyj innej nazwy");
                    return;
                }

                ProtectAPI::createTerrain($terrainName, ProtectAPI::$data[$nick]);
                $player->sendMessage("§8§l>§r §7Teren o nazwie §4{$terrainName} §7zostal utworzony");
                unset(ProtectAPI::$data[$nick]);
            }
        }
    }

    public function setNameTagDevice(DataPacketReceiveEvent $e)
    {
        $packet = $e->getPacket();

        if ($packet instanceof LoginPacket) {
            $player = $e->getPlayer();

            $device = NameTagsAPI::DEVICE_NONE;

            switch ($packet->clientData["DeviceOS"]) {
                case 1:
                case 2:
                    $device = NameTagsAPI::DEVICE_MOBILE;
                    break;

                case 7:
                    $device = NameTagsAPI::DEVICE_PC;
                    break;
            }

            Main::getInstance()->getScheduler()->scheduleDelayedTask(new SetNameTagDeviceTask($player, $device), 20);
        }
    }

    public function DymnaSciezkaParticle(PlayerMoveEvent $e)
    {
        $player = $e->getPlayer();

        if (ParticlesyAPI::hasParticleEnable($player, ParticlesyAPI::PARTICLE_ROAD_CLOUD)) {
            for ($i = 0; $i <= 3; $i++) {
                $add_x = (mt_rand(-1, 1) / 10);
                $add_z = (mt_rand(-1, 1) / 10);
                $player->getLevel()->addParticle(new ExplodeParticle($player->add($add_x, 0, $add_z)));
                $add_x = (mt_rand(-1, 1) / 10);
                $add_z = (mt_rand(-1, 1) / 10);
                $player->getLevel()->addParticle(new ExplodeParticle($player->add($add_x, 0, $add_z)));
            }
        }
    }

    public function PlomiennaSciezkaParticle(PlayerMoveEvent $e)
    {
        $player = $e->getPlayer();

        if (ParticlesyAPI::hasParticleEnable($player, ParticlesyAPI::PARTICLE_ROAD_FIRE)) {
            for ($i = 0; $i <= 10; $i++) {
                $add_x = (mt_rand(-3, 3) / 10);
                $add_z = (mt_rand(-3, 3) / 10);
                $player->getLevel()->addParticle(new FlameParticle($player->add($add_x, 0.1, $add_z)));
            }
        }
    }

    public function border(PlayerMoveEvent $e)
    {
        $player = $e->getPlayer();
        $x = $player->getFloorX();
        $z = $player->getFloorZ();

        $border = floor(Main::BORDER / 2);

        if (abs($x) >= ($border - 10)) {
            $distance = 10 - (abs($x) - ($border - 10));
            $player->sendTip("§7Do konca borderu: §4" . $distance);
        }

        if (abs($z) >= ($border - 10)) {
            $distance = 10 - (abs($z) - ($border - 10));
            $player->sendTip("§7Do konca borderu: §4" . $distance);
        }

        if ($x >= $border)
            $player->knockBack($player, 0, -2, 0, 0.5);

        if ($x <= -$border)
            $player->knockBack($player, 0, 2, 0, 0.5);

        if ($z >= $border)
            $player->knockBack($player, 0, 0, -2, 0.5);

        if ($z <= -$border)
            $player->knockBack($player, 0, 0, 2, 0.5);
    }


    public function removeLobbyBossbarOnQuit(PlayerQuitEvent $e)
    {
        $player = $e->getPlayer();

        if (BossbarManager::getBossbar($player) != null)
            BossbarManager::getBossbar($player)->hideFrom($player);
    }

    public function teleportToLobby(PlayerJoinEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        if (!LobbyAPI::isLobbyEnabled() || (LobbyAPI::isLobbyEnabled() && LobbyAPI::isInLobby($nick))) {
            $defLevel = $player->getServer()->getDefaultLevel();

            if ($player->getLevel()->getName() != $defLevel->getName())
                $player->teleport($defLevel->getSafeSpawn());

            return;
        }

        $player->teleport($player->getServer()->getLevelByName("lobby")->getSafeSpawn());

        $player->setHealth($player->getMaxHealth());
        $player->setFood($player->getMaxFood());
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();

        foreach ($player->getServer()->getOnlinePlayers() as $p) {
            $player->hidePlayer($p);
            $p->hidePlayer($player);
        }
    }

    public function lobbyChatBlock(PlayerChatEvent $e)
    {
        if ($e->getPlayer()->getLevel()->getName() == "lobby")
            $e->setCancelled(true);
    }

    public function lobbyCommandsBlock(PlayerCommandPreprocessEvent $e)
    {
        if ($e->getPlayer()->getLevel()->getName() == "lobby")
            $e->setCancelled(true);
    }

    public function lobbyDamageBlock(EntityDamageEvent $e)
    {
        if ($e->getEntity()->getLevel()->getName() == "lobby")
            $e->setCancelled(true);
    }

    public function lobbyMoveBlock(PlayerMoveEvent $e)
    {
        if ($e->getPlayer()->getLevel()->getName() == "lobby")
            $e->setCancelled(true);
    }

    public function lobbyExhaustBlock(PlayerExhaustEvent $e)
    {
        if ($e->getPlayer()->getLevel()->getName() == "lobby")
            $e->setCancelled(true);
    }

    public function DepozytOnJoin(PlayerJoinEvent $e)
    {
        $nick = $e->getPlayer()->getName();

        $db = Main::getInstance()->getDb();

        if (empty($db->query("SELECT * FROM depozyt WHERE nick = '$nick'")->fetchArray()))
            $db->query("INSERT INTO depozyt (nick, koxy, refy, perly) VALUES ('$nick', '0', '0', '0')");
    }

    public function DepozytOnMove(PlayerMoveEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        $db = Main::getInstance()->getDb();

        if ($e->getTo()->equals($e->getFrom()))
            return;

        $terrainName = ProtectAPI::getTerrainNameFromPos($player);

        if ($terrainName == "spawn" || $terrainName == "spawn-fly")
            return;

        $koxy = 0;
        $refy = 0;
        $perly = 0;

        foreach ($player->getInventory()->getContents() as $item) {
            if ($item->getId() == 466)
                $koxy += $item->getCount();

            if ($item->getId() == 322)
                $refy += $item->getCount();

            if ($item->getId() == 368)
                $perly += $item->getCount();
        }

        if ($koxy > Main::LIMIT_KOXY) {
            $rKoxy = $koxy - Main::LIMIT_KOXY;

            $player->getInventory()->removeItem(Item::get(466, 0, $rKoxy));

            $db->query("UPDATE depozyt SET koxy = koxy + '$rKoxy' WHERE nick = '$nick'");

            $player->sendMessage(Main::formatLines(["Twoj nadmiar koxow zostal przeniesiony do schowka!", "Stan depozytu mozesz sprawdzic pod komenda §4/depozyt§7!"]));
        }

        if ($refy > Main::LIMIT_REFY) {
            $rRefy = $refy - Main::LIMIT_REFY;

            $player->getInventory()->removeItem(Item::get(322, 0, $rRefy));

            $db->query("UPDATE depozyt SET refy = refy + '$rRefy' WHERE nick = '$nick'");

            $player->sendMessage(Main::formatLines(["Twoj nadmiar refow zostal przeniesiony do schowka!", "Stan depozytu mozesz sprawdzic pod komenda §4/depozyt§7!"]));
        }

        if ($perly > Main::LIMIT_PERLY) {
            $rPerly = $perly - Main::LIMIT_PERLY;

            $player->getInventory()->removeItem(Item::get(368, 0, $rPerly));

            $db->query("UPDATE depozyt SET perly = perly + '$rPerly' WHERE nick = '$nick'");

            $player->sendMessage(Main::formatLines(["Twoj nadmiar perel zostal przeniesiony do schowka!", "Stan depozytu mozesz sprawdzic pod komenda §4/depozyt§7!"]));
        }
    }

    public function StoniarkaPostaw(BlockPlaceEvent $e)
    {
        $player = $e->getPlayer();

        $item = $player->getInventory()->getItemInHand();

        if ($item->getId() == 1 && !$e->isCancelled()) {

            $x = $e->getBlock()->getFloorX();
            $y = $e->getBlock()->getFloorY();
            $z = $e->getBlock()->getFloorZ();

            switch ($item->getCustomName()) {
                case "§r§7Generator Kamienia§4 0.5s":
                    $time = 0.5;
                    break;

                case "§r§7Generator Kamienia§4 1.5s":
                    $time = 1.5;
                    break;

                case "§r§7Generator Kamienia§4 3s":
                    $time = 3;
                    break;

                default:
                    return;
            }

            $g_api = $player->getServer()->getPluginManager()->getPlugin("Gildie");

            if ($g_api != null) {
                $guildManager = $g_api->getGuildManager();

                if ($guildManager->isPlot($x, $z)) {
                    if ($guildManager->isInOwnPlot($player, $e->getBlock())) {
                        if (!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_STONIARKI)) {
                            $e->setCancelled(true);
                            $player->sendMessage("§8§l>§r §7Nie masz permisji do stawiania stoniarek");
                            return;
                        }
                    }
                }
            }

            Main::getInstance()->getDb()->query("INSERT INTO stoniarki (x, y, z, time) VALUES ('$x', '$y', '$z', '$time')");

            $player->sendMessage(Main::formatLines(["Postawiles §4{$time} §7sekundowy generator kamienia!", "Mozesz go zniszczyc §ezlotym §7kilofem!"]));
        }
    }

    /**
     * @param BlockBreakEvent $e
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function StoniarkaZniszcz(BlockBreakEvent $e)
    {
        $player = $e->getPlayer();

        $item = $player->getInventory()->getItemInHand();

        $x = $e->getBlock()->getFloorX();
        $y = $e->getBlock()->getFloorY();
        $z = $e->getBlock()->getFloorZ();

        $array = Main::getInstance()->getDb()->query("SELECT * FROM stoniarki WHERE x = '$x' AND y = '$y' AND z = '$z'")->fetchArray(SQLITE3_ASSOC);

        $g_api = $player->getServer()->getPluginManager()->getPlugin("Gildie");

        if (!empty($array) && !$e->isCancelled()) {
            if ($item->getId() == 285) {

                if ($g_api != null) {
                    $guildManager = $g_api->getGuildManager();

                    if ($guildManager->isPlot($x, $z)) {
                        if ($guildManager->isInOwnPlot($player, $e->getBlock())) {
                            if (!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_STONIARKI_DESTROY)) {
                                $e->setCancelled(true);
                                $player->sendMessage("§8§l>§r §7Nie masz permisji do niszczenia stoniarek zlotym kilofem");
                                return;
                            }
                        }
                    }
                }

                $item = Item::get(1);

                $item->setCustomName("§r§7Generator Kamienia§4 " . $array['time'] . " sekundy");
                $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));
                $item->setLore([
                    "jakies",
                    "linjiki"
                ]);

                $player->getLevel()->dropItem($e->getBlock()->asVector3(), $item);

                Main::getInstance()->getDb()->query("DELETE FROM stoniarki WHERE x = '$x' AND y = '$y' AND z = '$z'");

                $player->sendMessage("§8§l>§r §7Pomyslnie zniszczyles stoniarke!");
                return;
            } else {
                if ($g_api != null) {
                    $guildManager = $g_api->getGuildManager();

                    if ($guildManager->isPlot($x, $z)) {
                        if ($guildManager->isInOwnPlot($player, $e->getBlock())) {
                            if (!$guildManager->hasPermission($player->getName(), GuildManager::PERMISSION_STONIARKI)) {
                                $e->setCancelled(true);
                                $player->sendMessage("§8§l>§r §7Nie masz permisji do niszczenia stoniarek");
                                return;
                            }
                        }
                    }
                }
            }
            Main::getInstance()->getScheduler()->scheduleDelayedTask(new StoniarkaTask($player->getLevel(), $e->getBlock()->asVector3()), $array['time'] * 20);
        }
    }

    public function DropOnJoin(PlayerJoinEvent $e)
    {
        $nick = $e->getPlayer()->getName();

        $db = Main::getInstance()->getDb();

        if (empty($db->query("SELECT * FROM 'drop' WHERE nick = '$nick'")->fetchArray()))
            $db->query("INSERT INTO 'drop' (nick, diamenty, zloto, emeraldy, zelazo, wegiel, redstone, bookshelfy, obsydian, perly, slimeball, jablko, nicie, tnt, cobblestone) VALUES ('$nick', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on')");
    }

    /**
     * @param BlockBreakEvent $e
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function Drop(BlockBreakEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        if ($e->getBlock()->getId() == 1 && $e->getBlock()->getDamage() == 0 && !$e->isCancelled()) {

            $player->addXp(1000, false);

            $e->setDrops([Item::get(0)]);

            $api = Main::getInstance()->getDropAPI();

            if ($api->isEnable($nick, "cobblestone"))
                $player->getInventory()->addItem(Item::get(4));

            if (mt_rand(1, 100) < Main::getInstance()->getDropAPI()->getChance($player, 5))
                if ($api->isEnable($nick, "diamenty"))
                    $player->getInventory()->addItem(Item::get(Item::DIAMOND, 0, 1));

            if (mt_rand(1, 100) < Main::getInstance()->getDropAPI()->getChance($player, 5))
                if ($api->isEnable($nick, "emeraldy"))
                    $player->getInventory()->addItem(Item::get(Item::EMERALD, 0, 1));

            if (mt_rand(1, 100) < Main::getInstance()->getDropAPI()->getChance($player, 10))
                if ($api->isEnable($nick, "zloto"))
                    $player->getInventory()->addItem(Item::get(ITEM::GOLD_INGOT, 0, 1));

            if (mt_rand(1, 100) < Main::getInstance()->getDropAPI()->getChance($player, 10))
                if ($api->isEnable($nick, "zelazo"))
                    $player->getInventory()->addItem(Item::get(Item::IRON_INGOT, 0, 1));

            if (mt_rand(1, 100) < Main::getInstance()->getDropAPI()->getChance($player, 10))
                if ($api->isEnable($nick, "slimeball"))
                    $player->getInventory()->addItem(Item::get(Item::SLIME_BALL, 0, 1));

            if (mt_rand(1, 100) < Main::getInstance()->getDropAPI()->getChance($player, 10))
                if ($api->isEnable($nick, "redstone"))
                    $player->getInventory()->addItem(Item::get(Item::REDSTONE, 0, 1));

            if (mt_rand(1, 100) < Main::getInstance()->getDropAPI()->getChance($player, 10))
                if ($api->isEnable($nick, "wegiel"))
                    $player->getInventory()->addItem(Item::get(Item::COAL, 0, 1));

            if (mt_rand(1, 100) < Main::getInstance()->getDropAPI()->getChance($player, 10))
                if ($api->isEnable($nick, "bookshelfy"))
                    $player->getInventory()->addItem(Item::get(Item::BOOKSHELF, 0, 1));

            if (mt_rand(1, 100) < Main::getInstance()->getDropAPI()->getChance($player, 10))
                if ($api->isEnable($nick, "jablko"))
                    $player->getInventory()->addItem(Item::get(Item::APPLE, 0, 1));

            if (mt_rand(1, 100) < Main::getInstance()->getDropAPI()->getChance($player, 10))
                if ($api->isEnable($nick, "obsydian"))
                    $player->getInventory()->addItem(Item::get(Item::OBSIDIAN, 0, 1));

            if (mt_rand(1, 100) < Main::getInstance()->getDropAPI()->getChance($player, 10))
                if ($api->isEnable($nick, "nicie"))
                    $player->getInventory()->addItem(Item::get(Item::STRING, 0, 1));

            if (mt_rand(1, 100) < Main::getInstance()->getDropAPI()->getChance($player, 4))
                if ($api->isEnable($nick, "tnt"))
                    $player->getInventory()->addItem(Item::get(Item::TNT, 0, 1));

            if (mt_rand(1, 100) < Main::getInstance()->getDropAPI()->getChance($player, 21))
                if ($api->isEnable($nick, "perly"))
                    $player->getInventory()->addItem(Item::get(Item::ENDER_PEARL, 0, 1));

        }
    }

    /**
     * @param BlockPlaceEvent $e
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function BoyFarmer(BlockPlaceEvent $e)
    {
        $player = $e->getPlayer();

        $item = $player->getInventory()->getItemInHand();

        $x = $e->getBlock()->getFloorX();
        $z = $e->getBlock()->getFloorZ();

        if ($item->getId() == 49 && $item->getCustomName() == "§r§l§9BoyFarmer" && $item->hasEnchantment(17) && !$e->isCancelled()) {

            $e->setCancelled(true);

//			$g_api = $player->getServer()->getPluginManager()->getPlugin("Gildie");
//
//			if($g_api != null) {
//			    if(!$g_api->getGuildManager()->isInOwnPlot($player, $e->getBlock()) && !$player->hasPermission("PolishHard.guilds.opp")) {
//			        $player->sendMessage(Main::format("BoyFarmery mozesz stawiac tylko na terenie swojej gildii!"));
//			        return;
//                }
//            }

            $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));

            $player->sendMessage("§8§l>§r §7Pomyslnie postawiono §4BoyFarmera§7!");

            for ($i = $e->getBlock()->getFloorY(); $i > 0; $i--) {
                if ($player->getLevel()->getBlock(new Vector3($x, $i, $z))->getId() == 0)
                    $player->getLevel()->setBlock(new Vector3($x, $i, $z), Block::get(49));
                else
                    break;
            }
        }
    }

    /**
     * @param BlockPlaceEvent $e
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function SandFarmer(BlockPlaceEvent $e)
    {
        $player = $e->getPlayer();

        $item = $player->getInventory()->getItemInHand();

        $x = $e->getBlock()->getFloorX();
        $z = $e->getBlock()->getFloorZ();

        if ($item->getId() == 12 && $item->getCustomName() == "§r§l§9SandFarmer" && $item->hasEnchantment(17) && !$e->isCancelled()) {

            $e->setCancelled(true);

//            $g_api = $player->getServer()->getPluginManager()->getPlugin("Gildie");
//
//            if($g_api != null) {
//                if(!$g_api->getGuildManager()->isInOwnPlot($player, $e->getBlock()) && !$player->hasPermission("PolishHard.guilds.op")) {
//                    $player->sendMessage(Main::format("SandFarmery mozesz stawiac tylko na terenie swojej gildii!"));
//                    return;
//                }
//            }

            $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));

            $player->sendMessage("§8§l>§r §7Pomyslnie postawiono §4SandFarmera§7!");

            for ($i = $e->getBlock()->getFloorY(); $i > 0; $i--) {
                if ($player->getLevel()->getBlock(new Vector3($x, $i, $z))->getId() == 0)
                    $player->getLevel()->setBlock(new Vector3($x, $i, $z), Block::get(24));
                else
                    break;
            }

            for ($i = $e->getBlock()->getFloorY(); $i > 0; $i--) {
                if ($player->getLevel()->getBlock(new Vector3($x, $i, $z))->getId() == 24)
                    $player->getLevel()->setBlock(new Vector3($x, $i, $z), Block::get(12));
                else
                    break;
            }
        }
    }

    /**
     * @param BlockPlaceEvent $e
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function KopaczFosy(BlockPlaceEvent $e)
    {
        $player = $e->getPlayer();

        $item = $player->getInventory()->getItemInHand();

        $x = $e->getBlock()->getFloorX();
        $z = $e->getBlock()->getFloorZ();

        if ($item->getId() == 1 && $item->getCustomName() == "§r§l§9Kopacz Fosy" && $item->hasEnchantment(17) && !$e->isCancelled()) {

            $e->setCancelled(true);

//            $g_api = $player->getServer()->getPluginManager()->getPlugin("Gildie");
//
//            if($g_api != null) {
//                if(!$g_api->getGuildManager()->isInOwnPlot($player, $e->getBlock()) && !$player->hasPermission("PolishHard.guilds.op")) {
//                    $player->sendMessage(Main::format("Kopacze Fosy mozesz stawiac tylko na terenie swojej gildii!"));
//                    return;
//                }
//            }

            $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));

            $player->sendMessage("§8§l>§r §7Pomyslnie postawiono §4Kopacza Fosy§7!");

            $guild = Server::getInstance()->getPluginManager()->getPlugin("Gildie");
            $guildManager = $guild->getGuildManager();

            for ($i = $e->getBlock()->getFloorY(); $i > 0; $i--) {
                if ($player->getLevel()->getBlock(new Vector3($x, $i, $z))->getId() == 247)
                    continue;

                if (!$guildManager->isHeart($player->getLevel()->getBlock(new Vector3($x, $i, $z))))
                    $player->getLevel()->setBlock(new Vector3($x, $i, $z), Block::get(0));
                else
                    break;

                if ($player->getLevel()->getBlock(new Vector3($x, $i, $z))->getId() != 7)
                    $player->getLevel()->setBlock(new Vector3($x, $i, $z), Block::get(0));
                else
                    break;
            }
        }
    }

    public function UnknownCommandMessage(PlayerCommandPreprocessEvent $e)
    {
        if ($e->getMessage()[0] == "/") {
            $player = $e->getPlayer();

            $cmd = explode(" ", $e->getMessage())[0];

            $cmap = Server::getInstance()->getCommandMap();

            if ($cmap->getCommand(substr($cmd, 1)) == null) {
                $e->setCancelled(true);

                $player->sendMessage(Main::formatLines(["Komenda §4$cmd §7nie istnieje!", "Wpisz §4/pomoc§7, aby zobaczyc dostepne dla Ciebie komendy!"]));
            }
        }
    }

    public function JoinMessage(PlayerJoinEvent $e)
    {
        $e->setJoinMessage("");

        $player = $e->getPlayer();

        if (!LobbyAPI::isLobbyEnabled() || (LobbyAPI::isLobbyEnabled() && LobbyAPI::isInLobby($player->getName())))
            $player->sendMessage(Main::formatLines(["§7Witaj na serwerze §l§4PolishHard§7.EU", "Nasz discord: §4https://discord.gg/BZjhg7DZXq", "Nasza strona WWW: §4www.PolishHard.EU"]));
    }

    public function sendJoinMessage(EntityLevelChangeEvent $e)
    {
        $entity = $e->getEntity();

        if ($entity instanceof Player && $e->getTarget() == Server::getInstance()->getDefaultLevel()) {
            $entity->sendMessage(Main::formatLines(["Witaj na serwerze §4§lPolishHard§7.EU§r§7!", "Aktualna liczba graczy online: §4" . count($entity->getServer()->getOnlinePlayers()), "Nasz discord: §4https://discord.gg/BZjhg7DZXq", "Nasza strona WWW: §4www.PolishHard.EU"]));
        }
    }

    public function QuitMessage(PlayerQuitEvent $e)
    {
        $e->setQuitMessage("");
    }

    public function SpawnMoveCancel(PlayerMoveEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        if (isset(Main::$spawnTask[$nick])) {
            if (!($e->getTo()->floor()->equals($e->getFrom()->floor()))) {

                Main::$spawnTask[$nick]->cancel();

                unset(Main::$spawnTask[$nick]);

                $player->sendMessage("§8§l>§r §7Teleportacja na spawn zostala przerwana!");

                $player->removeEffect(9);
            }
        }
    }

    public function PointsOnJoin(PlayerJoinEvent $e)
    {
        $nick = $e->getPlayer()->getName();

        $api = Main::getInstance()->getPointsAPI();

        if (!$api->isInDatabase($nick))
            $api->setDefault($nick);
    }

    /**
     * @param EnityDamageEvent $e
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function setAntyLogout(EntityDamageEvent $e)
    {
        if ($e instanceof EntityDamageByEntityEvent) {
            $entity = $e->getEntity();
            $damager = $e->getDamager();

            if ($entity instanceof Player && $damager instanceof Player) {

                if ($entity->getName() == $damager->getName())
                    return;

                if (!isset(Main::$assists[$entity->getName()]))
                    Main::$assists[$entity->getName()] = [];

                // ASSIST
                if (isset(Main::$lastDamager[$entity->getName()]) && Main::$lastDamager[$entity->getName()]->getName() != $damager->getName()) {
                    if (!in_array(Main::$lastDamager[$entity->getName()]->getName(), Main::$assists[$entity->getName()])) {
                        if (count(Main::$assists[$entity->getName()]) >= 3) {
                            unset(Main::$assists[$entity->getName()][0]);

                            $newArray = [];

                            foreach (Main::$assists[$entity->getName()] as $player)
                                $newArray[] = $player;

                            Main::$assists[$entity->getName()] = $newArray;
                        }

                        Main::$assists[$entity->getName()][] = Main::$lastDamager[$entity->getName()]->getName();
                    }
                }

                // USUWA DAMAGERA Z ASYST JEZELI W NICH JEST
                if (in_array($damager->getName(), Main::$assists[$entity->getName()])) {
                    unset(Main::$assists[$entity->getName()][array_search($damager->getName(), Main::$assists[$entity->getName()])]);
                    $newArray = [];

                    foreach (Main::$assists[$entity->getName()] as $player)
                        $newArray[] = $player;

                    Main::$assists[$entity->getName()] = $newArray;
                }

                Main::$lastDamager[$entity->getName()] = $damager;
                Main::$lastDamager[$damager->getName()] = $entity;

                foreach ([$entity, $damager] as $player)
                    Main::$antylogoutPlayers[$player->getName()] = time();
            }
        }
    }

    public function AntyLogoutBlokadaKomend(PlayerCommandPreprocessEvent $e)
    {
        $player = $e->getPlayer();

        $cmd = explode(" ", $e->getMessage())[0];

        if (isset(Main::$antylogoutPlayers[$player->getName()])) {
            if (in_array($cmd, Main::ANTYLOGOUT_KOMENDY) && !$player->hasPermission("PolishHard.antylogout.commands")) {
                $e->setCancelled(true);
                $player->sendMessage(Main::format("Nie mozesz uzyc tej komendy podczas walki!"));
            }
        }
    }

    public function AntyLogoutQuit(PlayerQuitEvent $e)
    {
        if (isset(Main::$antylogoutPlayers[$e->getPlayer()->getName()]))
            $e->getPlayer()->kill();
    }

    public function AntyLogoutDeath(PlayerDeathEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        $e->setDeathMessage("");

        if (isset(Main::$antylogoutPlayers[$nick])) {
            unset(Main::$antylogoutPlayers[$nick]);

            $killer = Main::$lastDamager[$nick];

            if (!$killer->isConnected()) {
                foreach ($player->getArmorInventory()->getContents() as $item)
                    $player->getLevel()->dropItem($player->asVector3(), $item);

                foreach ($player->getInventory()->getContents() as $item)
                    $player->getLevel()->dropItem($player->asVector3(), $item);

                return;
            }

            $e->setKeepInventory(true);

            foreach ($player->getArmorInventory()->getContents() as $item) {
                if ($killer->getInventory()->canAddItem($item))
                    $killer->getInventory()->addItem($item);
                else
                    $killer->getLevel()->dropItem($killer->asVector3(), $item);
            }

            foreach ($player->getInventory()->getContents() as $item) {
                if ($killer->getInventory()->canAddItem($item))
                    $killer->getInventory()->addItem($item);
                else
                    $killer->getLevel()->dropItem($killer->asVector3(), $item);
            }

            $player->getInventory()->clearAll();
            $player->getArmorInventory()->clearAll();

            $api = Main::getInstance()->getPointsAPI();

            $g_api = $player->getServer()->getPluginManager()->getPlugin("Gildie");

            $pkt_k = $api->getPoints($killer->getName());
            $pkt_d = $api->getPoints($nick);

            $pkt = $pkt_d / $pkt_k;

            $assists_pkt = [];

            if (isset(Main::$assists[$nick])) {
                foreach (Main::$assists[$nick] as $assist_nick) {
                    $pkt_a = floor(($pkt_d / $api->getPoints($assist_nick)) * 10);
                    $assists_pkt[$assist_nick] = $pkt_a;
                }
            }

            $k_pkt = floor($pkt * 25);
            $d_pkt = floor($pkt * 18);

            if (isset(Main::$last[$killer->getName()]) && Main::$last[$killer->getName()] == $player->getName()) {
                $k_pkt = 0;
                $d_pkt = 0;
            }

            $api->addPoints($killer->getName(), $k_pkt);
            $api->removePoints($player->getName(), $d_pkt);

            Main::$last[$killer->getName()] = $player->getName();

            $assists_format = "§7z pomoca: ";

            foreach ($assists_pkt as $assist_nick => $assist_pkt) {
                $format = "§7{$assist_nick} §8[§4+{$assist_pkt}§8]§7, ";
                if ($g_api != null && $g_api->getGuildManager()->isInGuild($assist_nick)) {
                    $g = $g_api->getGuildManager()->getPlayerGuild($assist_nick);
                    $format = "§8[§4{$g->getTag()}§8] " . $format;
                }
                $assists_format .= $format;
            }

            $assists_format = substr($assists_format, 0, strlen($assists_format) - 2);

            Main::$assists[$nick] = [];

            $killer_format = "§7{$killer->getName()}";
            $death_format = "§7{$nick}";

            if ($g_api != null) {
                if ($g_api->getGuildManager()->isInGuild($killer->getName())) {
                    $g = $g_api->getGuildManager()->getPlayerGuild($killer->getName());
                    $killer_format = "§8[§4{$g->getTag()}§8] " . $killer_format;
                }

                if ($g_api->getGuildManager()->isInGuild($nick)) {
                    $g = $g_api->getGuildManager()->getPlayerGuild($nick);
                    $death_format = "§8[§4{$g->getTag()}§8] " . $death_format;
                }
            }

            foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                $p->sendMessage("§7Gracz {$death_format} §8[§4-{$d_pkt}§8] §7zostal zabity przez {$killer_format} §8[§4+{$k_pkt}§8]" . (count($assists_pkt) == 0 ? "" : " " . $assists_format));

            Main::getInstance()->getStatsAPI()->addKill($killer->getName());
        }
    }

    /**
     * @param BlockPlaceEvent $e
     *
     * @priority LOWEST
     * @ignoreCancelled false
     */
    public function PremiumCase(BlockPlaceEvent $e)
    {
        $player = $e->getPlayer();

        $item = $player->getInventory()->getItemInHand();

        if ($item->getId() == 146 && $item->getCustomName() == "§r§l§9PremiumCase" && $item->hasEnchantment(17)) {
            $e->setCancelled(true);

            if (Main::getInstance()->startEdycji()) {
                $player->sendMessage(Main::format("PremiumCase mozna otwierac po godzinie §5" . Main::getInstance()->getStartEdycjiTime()));
                return;
            }

            if (isset(Main::$antylogoutPlayers[$player->getName()])) {
                $player->sendMessage("§8§l❱§r §7Nie mozna otwierac PremiumCase podczas PvP!");
                return;
            }

            $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));

            /*
             *
             * -Kilof  6/3/3 [1] - (1%)
-Klata diaxowa 4/3 [1] - (5%)                  TAK
-Hełm diaxowy 4/3 [1] - (5%)                   TAK
-Spodnie diaxowe 4/3 [1] - (5%)                TAK
-Buty diaxowe 4/3 [1] - (5%)                   TAK
-Koxy [2] - (5%)                               TAK
-Refy [6] - (5%)                               TAK
-Bejkon [1] - (0.1%)                               TAK
-TnT [10] - (4%)                               TAK
-Miecz 5/2 z ogniem [1] - (5%)                               TAK
-Miecz 5/2 z odrzutem [1] - (5%)                               TAK
-Rzucak [1] - (1%)                               TAK
-Cx [4] - (6%)
-Bloki diax [4] - (7%)                               TAK
-Bloki złota [4] - (7%)                               TAK
-Bloki emeradow [4] - (7%)                               TAK
-bojfarmery [6] - (5%)
-kopaczefos [6] - (5%)
-sandfarmery [6] - (5%)
-łuk 5/2/1 [1] - (3%)                               TAK

             */
            $diamond = round(rand(0, 10000) / 100, 2) < 7.0;
            $gold = round(rand(0, 10000) / 100, 2) < 7.0;
            $emerald = round(rand(0, 10000) / 100, 2) < 7.0;

            $koxy = round(rand(0, 10000) / 100, 2) < 5.0;
            $refy = round(rand(0, 10000) / 100, 2) < 5.0;

            $miecz52 = round(rand(0, 10000) / 100, 2) < 5;
            $knock = round(rand(0, 10000) / 100, 2) < 5;
            $luk = round(rand(0, 10000) / 100, 2) < 3;
            $helm = round(rand(0, 10000) / 100, 2) < 5;
            $klata = round(rand(0, 10000) / 100, 2) < 5;
            $spodnie = round(rand(0, 10000) / 100, 2) < 5;
            $buty = round(rand(0, 10000) / 100, 2) < 5;
            $cx = round(rand(0, 10000) / 100, 2) < 6;

            $boyFarmer = round(rand(0, 10000) / 100, 2) < 5;
            $sandFarmer = round(rand(0, 10000) / 100, 2) < 5;
            $kopaczFos = round(rand(0, 10000) / 100, 2) < 5;

            $tnt = round(rand(0, 10000) / 100, 2) < 4;
            $beacon = round(rand(0, 10000) / 100, 2) < 0.1;
            $kilof633 = round(rand(0, 10000) / 100, 2) < 1;
            $rzucak = round(rand(0, 10000) / 100, 2) < 1;

            switch (true) {

                case $cx:

                    $item = Item::get(48);
                    $item->setCustomName("§r§l§4CobbleX");
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));

                    $itemFormat = "§eCobblex x6";
                    break;

                case $boyFarmer:

                    $item = Item::get(49);
                    $item->setCustomName("§r§l§9BoyFarmer");
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));

                    $itemFormat = "§eBoyFarmer x6";
                    break;

                case $sandFarmer:

                    $item = Item::get(12);
                    $item->setCustomName("§r§l§9SandFarmer");
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));

                    $itemFormat = "§eSandFarmer x6";
                    break;

                case $kopaczFos:

                    $item = Item::get(1);
                    $item->setCustomName("§r§l§9Kopacz Fosy");
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));

                    $itemFormat = "§eKopacz fos x6";

                    break;

                case $gold:
                    $item = Item::get(Item::GOLD_BLOCK, 0, 4);

                    $itemFormat = "§eBloki zlota x4";
                    break;

                case $diamond:
                    $item = Item::get(Item::DIAMOND_BLOCK, 0, 4);

                    $itemFormat = "§eBloki diamentow x32";
                    break;

                case $emerald:
                    $item = Item::get(Item::EMERALD_BLOCK, 0, 4);

                    $itemFormat = "§eBloki emeraldow x32";
                    break;

                case $koxy:
                    $item = Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 2);

                    $itemFormat = "§eKoxy x2";
                    break;

                case $refy:
                    $item = Item::get(Item::GOLDEN_APPLE, 0, 4);

                    $itemFormat = "§eRefy x2";
                    break;

                case $miecz52:
                    $item = Item::get(Item::DIAMOND_SWORD);

                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 5));
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::FIRE_ASPECT), 2));

                    $itemFormat = "§eMiecz 5/2";
                    break;

                case $knock:
                    $item = Item::get(Item::DIAMOND_SWORD);

                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 5));
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::KNOCKBACK), 2));

                    $itemFormat = "§eMiecz Knock 5/2";
                    break;

                case $beacon:
                    $item = Item::get(Item::BEACON);

                    $itemFormat = "§eBEACON";
                    break;

                case $kilof633:
                    $item = Item::get(Item::DIAMOND_PICKAXE);

                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 6));
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 3));
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::FORTUNE), 3));

                    $itemFormat = "§eKilof 6/3/3";
                    break;

                case $tnt:
                    $item = Item::get(Item::TNT, 0, 10);

                    $itemFormat = "§eTNT x10";
                    break;

                case $rzucak:

                    $item = Item::get(46);
                    $item->setCustomName("§r§l§4Rzucane TNT");
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));

                    $itemFormat = "§eRZUCAK";

                    break;

                case $luk:
                    $item = Item::get(Item::BOW);

                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::POWER), 5));
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PUNCH), 2));
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::INFINITY), 1));

                    $itemFormat = "§eLuk punch 5/2/1";
                    break;

                case $helm:
                    $item = Item::get(Item::DIAMOND_HELMET);

                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 4));
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 3));

                    $itemFormat = "§eHelm 4/3";
                    break;
                case $klata:
                    $item = Item::get(Item::DIAMOND_CHESTPLATE);

                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 4));
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 3));

                    $itemFormat = "§eKlata 4/3";
                    break;
                case $spodnie:
                    $item = Item::get(Item::DIAMOND_LEGGINGS);

                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 4));
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 3));

                    $itemFormat = "§eSpodnie 4/3";
                    break;
                case $buty:
                    $item = Item::get(Item::DIAMOND_BOOTS);

                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 4));
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 3));

                    $itemFormat = "§eButy 4/3";
                    break;

                default:
                    $itemFormat = "§eNIC";
                    $item = null;
                    break;
            }

            if($item !== null) {
                if($player->getInventory()->canAddItem($item))
                    $player->getInventory()->addItem($item);
                else
                    $player->getLevel()->dropItem($e->getBlock()->asVector3(), $item);
            }

            foreach ($player->getServer()->getDefaultLevel()->getPlayers() as $p) {
                $array = Main::getInstance()->getDb()->query("SELECT * FROM 'case' WHERE nick = '{$p->getName()}'")->fetchArray();

                if (empty($array))
                    $p->sendMessage("§8§l❱§r §7Gracz §6{$player->getName()} §7otworzyl §ePremiumCase §7i wylosowal $itemFormat");
            }
        }
    }

    /**
     * @param BlockBreakEvent $e
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function CobbleX(BlockBreakEvent $e)
    {
        $player = $e->getPlayer();

        $block = $e->getBlock();

        if ($block->getId() == 48 && !$e->isCancelled()) {
            $pos = $block->asVector3();

            $e->setDrops([Item::get(0)]);

            $item = null;

            /*switch (mt_rand(1, 13)) {
                case 1:
                    $item = Item::get(438, 33, 1);
                    foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                        $p->sendMessage("§8§l>§r §7Gracz §6{$player->getName()} §7otworzyl §eCobbleX §7i wylosowal §epotke Strength II§7! §8(§fx1§8)");
                    break;*/

            $refy = round(rand(0, 10000) / 100, 2) < 2;
            $bookshelf = round(rand(0, 10000) / 100, 2) < 10;
            $apple = round(rand(0, 10000) / 100, 2) < 10;
            $gold = round(rand(0, 10000) / 100, 2) < 6;
            $emerald = round(rand(0, 10000) / 100, 2) < 5;
            $diamond = round(rand(0, 10000) / 100, 2) < 5;
            $iron = round(rand(0, 10000) / 100, 2) < 6;
            $cx = round(rand(0, 10000) / 100, 2) < 2;
            $perly = round(rand(0, 10000) / 100, 2) < 6;
            $enchant = round(rand(0, 10000) / 100, 2) < 8;
            $kowadlo = round(rand(0, 10000) / 100, 2) < 8;
            $silka = round(rand(0, 10000) / 100, 2) < 2;
            $drewno = round(rand(0, 10000) / 100, 2) < 10;

            switch (true) {

                case $cx:

                    $item = Item::get(48);
                    $item->setCustomName("§r§l§4CobbleX");
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));

                    foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                        $p->sendMessage("§8§l>§r §7Gracz §6{$player->getName()} §7otworzyl §eCobbleX §7i wylosowal §eCobblex§7! §8(§fx1§8)");

                    break;

                case $refy:

                    $item = Item::get(Item::GOLDEN_APPLE);

                    foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                        $p->sendMessage("§8§l>§r §7Gracz §6{$player->getName()} §7otworzyl §eCobbleX §7i wylosowal §eRefy§7! §8(§fx1§8)");

                    break;
                case $bookshelf:

                    $item = Item::get(Item::BOOKSHELF, 0, 6);

                    foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                        $p->sendMessage("§8§l>§r §7Gracz §6{$player->getName()} §7otworzyl §eCobbleX §7i wylosowal §eBiblioteczke§7! §8(§fx6§8)");

                    break;
                case $apple:

                    $item = Item::get(Item::APPLE, 0, 4);

                    foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                        $p->sendMessage("§8§l>§r §7Gracz §6{$player->getName()} §7otworzyl §eCobbleX §7i wylosowal §eJablko§7! §8(§fx4§8)");

                    break;
                case $gold:

                    $item = Item::get(Item::GOLD_INGOT, 0, 14);

                    foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                        $p->sendMessage("§8§l>§r §7Gracz §6{$player->getName()} §7otworzyl §eCobbleX §7i wylosowal §eZloto§7! §8(§fx14§8)");

                    break;
                case $emerald:

                    $item = Item::get(Item::EMERALD, 0, 14);

                    foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                        $p->sendMessage("§8§l>§r §7Gracz §6{$player->getName()} §7otworzyl §eCobbleX §7i wylosowal §eEmeraldy§7! §8(§fx14§8)");

                    break;
                case $diamond:

                    $item = Item::get(Item::DIAMOND, 0, 14);

                    foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                        $p->sendMessage("§8§l>§r §7Gracz §6{$player->getName()} §7otworzyl §eCobbleX §7i wylosowal §eDiamenty§7! §8(§fx14§8)");

                    break;
                case $iron:

                    $item = Item::get(Item::IRON_INGOT, 0, 14);

                    foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                        $p->sendMessage("§8§l>§r §7Gracz §6{$player->getName()} §7otworzyl §eCobbleX §7i wylosowal §eZelazo§7! §8(§fx14§8)");

                    break;
                case $perly:

                    $item = Item::get(Item::ENDER_PEARL, 0, 4);

                    foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                        $p->sendMessage("§8§l>§r §7Gracz §6{$player->getName()} §7otworzyl §eCobbleX §7i wylosowal §ePerly§7! §8(§fx4§8)");

                    break;
                case $enchant:

                    $item = Item::get(Item::ENCHANTING_TABLE);

                    foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                        $p->sendMessage("§8§l>§r §7Gracz §6{$player->getName()} §7otworzyl §eCobbleX §7i wylosowal §eEnchant§7! §8(§fx1§8)");

                    break;
                case $kowadlo:

                    $item = Item::get(Item::ANVIL);

                    foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                        $p->sendMessage("§8§l>§r §7Gracz §6{$player->getName()} §7otworzyl §eCobbleX §7i wylosowal §eKowadlo§7! §8(§fx1§8)");

                    break;
                case $silka:

                    $item = Item::get(Item::POTION, 31);

                    foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                        $p->sendMessage("§8§l>§r §7Gracz §6{$player->getName()} §7otworzyl §eCobbleX §7i wylosowal §epotke Strength I§7! §8(§fx1§8)");

                    break;
                case $drewno:

                    $item = Item::get(Item::LOG, 0, 16);

                    foreach (Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
                        $p->sendMessage("§8§l>§r §7Gracz §6{$player->getName()} §7otworzyl §eCobbleX §7i wylosowal §eDrewno§7! §8(§fx16§8)");

                    break;
            }

            $player->getLevel()->dropItem($pos, $item ?? Item::get(0));
        }
    }

    public function LosoweTP(PlayerInteractEvent $e)
    {
        if ($e->getBlock()->getId() == 19) {

            $e->setCancelled(true);

            $x = mt_rand(Main::MIN_TP, Main::MAX_TP);
            $z = mt_rand(Main::MIN_TP, Main::MAX_TP);
            $y = $e->getPlayer()->getLevel()->getHighestBlockAt($x, $z) + 1;

            $e->getPlayer()->sendMessage(Main::format("Teleportowanie w losowe kordy: X: §4$x §7Y: §4$y §7Z: §4$z"));

            $e->getPlayer()->teleport(new Vector3($x, $y, $z));
        }
    }

    public function GrupoweTP(PlayerInteractEvent $e)
    {
        $player = $e->getPlayer();

        $block = $e->getBlock();

        if ($block->getId() == 25) {
            $players = [];

            if ($player->distance($block) > 3)
                return;

            foreach ($player->getServer()->getDefaultLevel()->getPlayers() as $p) {
                if ($p->distance($block) <= 3)
                    $players[] = $p;
            }

            if (count($players) < 2) {
                foreach ($players as $p)
                    $p->sendMessage("§8§l>§r §7Brakuje jeszcze §41 §7gracza!");
                return;
            }

            $x = mt_rand(Main::MIN_TP, Main::MAX_TP);
            $z = mt_rand(Main::MIN_TP, Main::MAX_TP);
            $y = $e->getPlayer()->getLevel()->getHighestBlockAt($x, $z) + 1;

            foreach ($players as $p) {
                $p->teleport(new Vector3($x, $y, $z));

                $p->sendMessage(Main::format("Zostales przeteleportowany na kordy: X: §4$x §7Y: §4$y §7Z: §4$z"));
            }
        }
    }

    /**
     * @param PlayerChatEvent $e
     *
     * @priority HIGH
     * @ignoreCancelled true
     */
    public function ChatOnOff(PlayerChatEvent $e)
    {
        $player = $e->getPlayer();

        if (!Main::$chatOn && !$player->hasPermission("PolishHard.chat")) {
            $e->setCancelled(true);

            $player->sendMessage(Main::format("Chat jest obecnie §cwylaczony§7!"));
        }
    }

    /**
     * @param PlayerChatEvent $e
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function AntySpam(PlayerChatEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        if ($player->hasPermission("PolishHard.chat.spam")) return;

        isset(Main::$lastChatMsg[$nick]) ? $time = Main::$lastChatMsg[$nick] : $time = 0;

        if (time() - $time < 10) {
            $e->setCancelled(true);

            $player->sendMessage(Main::format("Nastepna wiadomosc mozesz napisac za §4" . (10 - (time() - $time)) . " §7sekund!"));
        } else
            Main::$lastChatMsg[$nick] = time();
    }


    public function mute(PlayerChatEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        $api = Main::getInstance()->getMuteAPI();

        if ($api->isMuted($nick)) {
            $e->setCancelled(true);
            $player->sendMessage($api->getMuteMessage($player));
        }
    }

    /**
     * @param PlayerCommandPreprocessEvent $e
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function CooldownKomendy(PlayerCommandPreprocessEvent $e)
    {
        if (!($e->getMessage()[0] == "/")) return;

        $player = $e->getPlayer();
        $nick = $player->getName();

        if ($player->hasPermission("PolishHard.command.ignorecooldown")) return;

        isset(Main::$lastCmd[$nick]) ? $time = Main::$lastCmd[$nick] : $time = 0;

        if (time() - $time < 5) {
            $e->setCancelled(true);

            $player->sendMessage("§8§l>§r §7Nastepna komende mozesz uzyc za §4" . (5 - (time() - $time)) . " §7sekund!");
        } else
            Main::$lastCmd[$nick] = time();
    }

    public function BrakPermisji(PlayerCommandPreprocessEvent $e)
    {
        $player = $e->getPlayer();

        $cmd = explode(" ", $e->getMessage())[0];

        $cmd = Server::getInstance()->getCommandMap()->getCommand(substr($cmd, 1));

        if ($cmd !== null && $cmd->getPermission() != null && !$cmd->getPermission() == "PolishHard.command.see" && !$player->hasPermission($cmd->getPermission())) {
            $e->setCancelled(true);

            $player->sendMessage(Main::formatLines(["Nie posiadasz §4permisji§7, aby uzyc tej komendy! §8(§4{$cmd->getPermission()}§8)", "Wpisz §4/pomoc §7aby zobaczyc dostepne dla Ciebie komendy!"]));
        }
    }

    /**
     * @param BlockPlaceEvent $e
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function Rzucak(BlockPlaceEvent $e)
    {
        $player = $e->getPlayer();

        $item = $player->getInventory()->getItemInHand();

        if ($item->getId() == 46 && $item->getCustomName() == "§r§l§4Rzucane TNT" && $item->hasEnchantment(17)) {
            $e->setCancelled(true);

            $item->setCount(1);

            $player->getInventory()->removeItem($item);

            $pos = $player->add(0, $player->getEyeHeight());
            $motion = $player->getDirectionVector()->multiply(0.6);

            $nbt = Entity::createBaseNBT($pos, $motion);

            $entity = Entity::createEntity("PrimedTNT", $player->getLevel(), $nbt);
            $entity->spawnToAll();
        }
    }

    public function BanKick(PlayerPreLoginEvent $e): void
    {
        $player = $e->getPlayer();

        $api = Main::getInstance()->getBanAPI();

        if ($api->isBanned($player->getName()) || $api->isIpBanned($player->getAddress())) {
            $e->setCancelled(true);

            $player->close("", $api->getBanMessage($player));
        }
    }

    public function TpOnRespawn(PlayerRespawnEvent $e)
    {
        $e->setRespawnPosition($e->getPlayer()->getLevel()->getSafeSpawn());
    }

    public function SprawdzanieKomendy(PlayerCommandPreprocessEvent $e)
    {
        if (!($e->getMessage()[0] == "/")) return;

        $player = $e->getPlayer();

        $cmd = explode(" ", $e->getMessage())[0];

        if (isset(Main::$spr[$player->getName()])) {
            if ($cmd != "/msg" && $cmd != "/r" && $cmd != "/przyznajesie") {
                $e->setCancelled(true);

                $player->sendMessage(Main::format("Jestes podczas sprawdzania, nie mozesz uzyc tej komendy!"));
            }
        }
    }

    public function SprawdzanieBanOnQuit(PlayerQuitEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        if (isset(Main::$spr[$nick])) {
            $api = Main::getInstance()->getBanAPI();

            $api->setBan($nick, "Cheaty", Main::$spr[$nick][1]);

            unset(Main::$spr[$nick]);

            $player->teleport($player->getLevel()->getSafeSpawn());

            Server::getInstance()->broadcastMessage(Main::format("Gracz §4$nick §7wylogowal sie podczas sprawdzania!"));
        }
    }

    public function clickVillager(EntityDamageEvent $e)
    {
        if ($e instanceof EntityDamageByEntityEvent) {
            $damager = $e->getDamager();
            $entity = $e->getEntity();

            if ($damager instanceof Player && $entity instanceof Villager) {
                $e->setCancelled(true);

                $nick = $damager->getName();

                if (isset(Main::$addVillagerRecipe[$nick])) {
                    if (!(array_key_exists(0, Main::$addVillagerRecipe[$nick]))) {
                        array_push(Main::$addVillagerRecipe[$nick], $entity);

                        $damager->sendMessage(Main::format("Aby dodac ten item kliknij na blok"));
                    }

                    return;
                }

                if (isset(Main::$removeVillager[$nick])) {
                    $entity->close();

                    $damager->sendMessage(Main::format("Villager zostal pomyslnie usuniety"));

                    unset(Main::$removeVillager[$nick]);
                    return;
                }

                if (isset(Main::$villagerName[$nick])) {
                    $name = Main::$villagerName[$nick];

                    $entity->setNameTag($name);
                    $entity->setCustomName($name);

                    unset(Main::$villagerName[$nick]);

                    $damager->sendMessage(Main::format("Pomyslnie zmieniono nazwe villagera na $name"));
                    return true;
                }

                if (isset(Main::$tpVillager[$nick])) {
                    if (Main::$tpVillager[$nick] == true) {
                        Main::$tpVillager[$nick] = $entity;

                        $damager->sendMessage(Main::format("Kliknij w miejsce w ktore chcesz przeteleportowac villagera"));
                    }
                    return;
                }

                if (isset(Main::$copyVillager[$nick])) {
                    if (Main::$copyVillager[$nick] == true) {
                        Main::$copyVillager[$nick] = $entity;

                        $damager->sendMessage(Main::format("Kliknij w miejsce w ktore chcesz wkleic villagera"));
                    }
                    return;
                }

                if (isset(Main::$removeVillagerRecipe[$nick])) {

                    $entity->removeRecipe(Main::$removeVillagerRecipe[$nick] - 1);

                    $damager->sendMessage(Main::format("Receptura zostala pomyslnie usunieta!"));

                    unset(Main::$removeVillagerRecipe[$nick]);
                    return;
                }

                $damager->addWindow(new VillagerInventory($entity));
            }
        }
    }

    public function InteractVillagerActions(PlayerInteractEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        $item = $player->getInventory()->getItemInHand();

        $action = $e->getAction();

        if (isset(Main::$addVillagerRecipe[$nick])) {
            if (!(array_key_exists(1, Main::$addVillagerRecipe[$nick]))) {

                Main::$addVillagerRecipe[$nick][1] = $item;

                $player->sendMessage(Main::format("Aby dodac ten item kliknij na blok, aby go pominac kliknij w ziemie bez itemu"));

            } elseif (!(array_key_exists(2, Main::$addVillagerRecipe[$nick]))) {
                Main::$addVillagerRecipe[$nick][2] = $item;

                $player->sendMessage(Main::format("Aby dodac ten item kliknij na blok"));
            } elseif (!(array_key_exists(3, Main::$addVillagerRecipe[$nick]))) {
                $villager = Main::$addVillagerRecipe[$nick][0];

                $villager->addRecipe(Main::$addVillagerRecipe[$nick][1], Main::$addVillagerRecipe[$nick][2], $item);

                $player->sendMessage(Main::format("Receptura zostala dodana pomyslnie!"));

                unset(Main::$addVillagerRecipe[$nick]);
            }
        }

        if (isset(Main::$tpVillager[$nick])) {
            if (Main::$tpVillager[$nick] !== true) {
                Main::$tpVillager[$nick]->teleport(Location::fromObject($e->getBlock()->asVector3()->add(0.5, 1, 0.5), $player->getLevel(), $player->getYaw()));

                $player->sendMessage(Main::format("Villager zostal przeteleportowany pomyslnie"));

                unset(Main::$tpVillager[$nick]);
            }
        }

        if (isset(Main::$copyVillager[$nick])) {
            if (Main::$copyVillager[$nick] !== true) {
                $entity = Entity::createEntity('Villager', $player->getLevel(), Main::$copyVillager[$nick]->namedtag);

                $pos = $e->getBlock()->asVector3()->add(0, 1);
                $entity->teleport($pos);

                $entity->spawnToAll();

                $player->sendMessage(Main::format("Villager zostal skopiowany pomyslnie"));

                unset(Main::$copyVillager[$nick]);
            }
        }
    }

    public function LightningStrikeOnDeath(PlayerDeathEvent $e)
    {

        $player = $e->getPlayer();

        $pk = new AddActorPacket();
        $pk->type = "minecraft:lightning_bolt";
        $pk->entityRuntimeId = Entity::$entityCount++;
        $pk->position = $player->asVector3();

        $player->getServer()->broadcastPacket($player->getLevel()->getPlayers(), $pk);

        $pk = new PlaySoundPacket();
        $pk->soundName = "ambient.weather.lightning.impact";
        $pk->x = $player->getX();
        $pk->y = $player->getY();
        $pk->z = $player->getZ();
        $pk->volume = 500;
        $pk->pitch = 1;

        $player->getServer()->broadcastPacket($player->getLevel()->getPlayers(), $pk);
    }

    public function craftingBeforeStartEdition(CraftItemEvent $e)
    {
        $player = $e->getPlayer();
        $items = $e->getOutputs();

        $blocked_items = [310, 311, 312, 313, 276];

        if (Main::getInstance()->startEdycji()) {
            foreach ($items as $item) {
                if (in_array($item->getId(), $blocked_items) && !$player->hasPermission("PolishHard.crafting.beforeedition")) {
                    $e->setCancelled(true);
                    $player->sendMessage(Main::format("Ten item mozna craftowac po godzinie §4" . Main::getInstance()->getStartEdycjiTime()));
                }
            }
        }
    }


    public function WhiteList(PlayerPreLoginEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        if (!$player->getServer()->isWhitelisted($nick)) {
            $e->setCancelled(true);
            $player->close("", Main::getInstance()->getWhitelistMessage());
        }
    }

    public function hidePlayerOnJoinVanish(PlayerJoinEvent $e)
    {
        $player = $e->getPlayer();

        if ($player->hasPermission("PolishHard.vanish.see")) return;

        foreach (Main::$vanish as $nick) {
            $p = $player->getServer()->getPlayer($nick);
            if ($p == null) continue;

            $player->hidePlayer($p);
        }
    }

    public function pickupItemsVanish(InventoryPickupItemEvent $e)
    {
        $player = $e->getInventory()->getHolder();

        if (in_array($player->getName(), Main::$vanish))
            $e->setCancelled(true);
    }

    public function god(EntityDamageEvent $e)
    {
        $entity = $e->getEntity();

        if ($entity instanceof Player)
            if (isset(Main::$god[$entity->getName()]))
                $e->setCancelled(true);
    }

    public function tpOnJoin(PlayerJoinEvent $e)
    {
        Main::$tp[$e->getPlayer()->getName()] = [];
    }

    public function tpOnQuit(PlayerQuitEvent $e)
    {
        $nick = $e->getPlayer()->getName();

        foreach (Main::$tp as $player => $players)
            foreach ($players as $p => $time)
                if ($p == $nick)
                    unset(Main::$tp[$player][$nick]);
    }

    public function tpMoveCancel(PlayerMoveEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        if (isset(Main::$tpTask[$nick])) {
            if (!($e->getTo()->floor()->equals($e->getFrom()->floor()))) {

                Main::$tpTask[$nick]->cancel();

                unset(Main::$tpTask[$nick]);

                $player->sendMessage("§8§l>§r §7Teleportacja zostala przerwana!");

                $player->removeEffect(9);
            }
        }
    }

    public function warpMoveCancel(PlayerMoveEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        if (isset(Main::$warpTask[$nick])) {
            if (!($e->getTo()->floor()->equals($e->getFrom()->floor()))) {
                Main::$warpTask[$nick]->cancel();
                unset(Main::$warpTask[$nick]);
                $player->sendMessage("§8§l>§r §7Teleportacja na warp zostala przerwana!");
                $player->removeEffect(9);
            }
        }
    }

    public function homeMoveCancel(PlayerMoveEvent $e)
    {
        $player = $e->getPlayer();
        $nick = $player->getName();

        if (isset(Main::$homeTask[$nick])) {
            if (!($e->getTo()->floor()->equals($e->getFrom()->floor()))) {
                Main::$homeTask[$nick]->cancel();
                unset(Main::$homeTask[$nick]);
                $player->sendMessage("§8§l>§r §7Teleportacja do domu zostala przerwana!");
                $player->removeEffect(9);
            }
        }
    }

    public function openEnchanting(PlayerInteractEvent $e)
    {
        $player = $e->getPlayer();
        $block = $e->getBlock();
        $item = $player->getInventory()->getItemInHand();

        if ($block->getId() == 116) {
            $e->setCancelled(true);

            $enchLvL = Main::getInstance()->getBookshelfsCount($block, $player->getLevel());

            switch (true) {
                case $item instanceof Sword:
                    $player->sendForm(new EnchantSwordForm($enchLvL));
                    break;

                case $item instanceof Bow:
                    $player->sendForm(new EnchantBowForm($enchLvL));
                    break;

                case $item instanceof Tool:
                    $player->sendForm(new EnchantToolsForm($enchLvL));
                    break;

                case $item instanceof Armor:
                    if ($item instanceof ChainBoots || $item instanceof DiamondBoots || $item instanceof GoldBoots || $item instanceof IronBoots || $item instanceof LeatherBoots)
                        $player->sendForm(new EnchantArmorForm($enchLvL, true));
                    else
                        $player->sendForm(new EnchantArmorForm($enchLvL));
                    break;

                case $item->getId() == 0:
                    $player->sendMessage("§8§l>§r §7Aby §4zenchantowac §7item trzymaj go w rece i §4kliknij §7na enchanting!");
                    break;

                default:
                    $player->sendMessage("§8§l>§r §7Nie mozesz §4zenchantowac §7tego itemu!");
            }
        }
    }

    public function statsOnJoin(PlayerJoinEvent $e)
    {
        $nick = $e->getPlayer()->getName();

        $api = Main::getInstance()->getStatsAPI();

        if (!$api->isInDatabase($nick))
            $api->setDefault($nick);
    }

    public function statsOnDeath(PlayerDeathEvent $e)
    {
        Main::getInstance()->getStatsAPI()->addDeath($e->getPlayer()->getName());
    }

    public function statsOnEat(PlayerItemConsumeEvent $e)
    {
        if ($e->isCancelled()) return;

        switch ($e->getItem()->getId()) {
            case 466:
                Main::getInstance()->getStatsAPI()->addKoxy($e->getPlayer()->getName());
                break;

            case 322:
                Main::getInstance()->getStatsAPI()->addRefy($e->getPlayer()->getName());
                break;
        }
    }

    public function statsOnProjectileLaunch(ProjectileLaunchEvent $e)
    {
        $entity = $e->getEntity();

        if ($entity instanceof EnderPearl) {
            $owner = $entity->getOwningEntity();

            if ($owner !== null && $owner instanceof Player)
                Main::getInstance()->getStatsAPI()->addPerly($owner->getName());
        }
    }

    /**
     * @param BlockBreakEvent $e
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function itemsToEq(BlockBreakEvent $e)
    {
        $player = $e->getPlayer();
        $drops = [];

        foreach ($e->getDrops() as $item) {
            if ($player->getInventory()->canAddItem($item))
                $player->getInventory()->addItem($item);
            else
                $drops[] = $item;
        }

        $e->setDrops($drops);
    }

    public function openEnderChest(PlayerInteractEvent $e)
    {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        if ($block->getId() == 130) {
            $e->setCancelled(true);

            if (isset(Main::$antylogoutPlayers[$player->getName()])) {
                $player->sendMessage(Main::format("Nie mozesz otwierac EnderChesta podczas walki!"));
                return;
            }

            $size = EnderchestInventory::SIZE_SMALL;

            if ($player->hasPermission("PolishHard.ec.large"))
                $size = EnderchestInventory::SIZE_LARGE;

            new PreprocessEnderchestInventory($player, $block, $size);
        }
    }

    public function knockAndAttackDelay(EntityDamageEvent $e)
    {
        if ($e instanceof EntityDamageByEntityEvent) {
            $cfg = Main::getInstance()->getConfig();

            /*if($cfg->exists("knockback"))
             $e->setKnockback((float) $cfg->get("knockback"));*/

            if ($cfg->exists("attackdelay"))
                $e->setAttackCooldown((float)$cfg->get("attackdelay"));
        }
    }

    public function fixFood(PlayerExhaustEvent $e) : void{
        if($e->getCause() == PlayerExhaustEvent::CAUSE_SPRINT_JUMPING or $e->getCause() == PlayerExhaustEvent::CAUSE_SPRINTING){
            $e->setAmount((($e->getAmount()) / 3));
        }

    }

    public function cordsOnJoin(PlayerJoinEvent $e) : void{
        $player = $e->getPlayer();

        $pk = new GameRulesChangedPacket();
        $pk->gameRules = ["showcoordinates" => [1, true]];

        $player->dataPacket($pk);
    }
}