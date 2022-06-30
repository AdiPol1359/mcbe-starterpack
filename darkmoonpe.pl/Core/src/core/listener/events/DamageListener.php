<?php

namespace core\listener\events;

use core\caveblock\CaveManager;
use core\entity\entities\custom\CaveSpawn;
use core\entity\entities\mobs\Villager;
use core\form\forms\caveblock\ManageCave;
use core\form\forms\quest\MainQuestForm;
use core\listener\BaseListener;
use core\Main;
use core\manager\managers\bossbar\BossbarManager;
use core\manager\managers\CpsManager;
use core\manager\managers\MobStackerManager;
use core\manager\managers\quest\QuestManager;
use core\manager\managers\SettingsManager;
use core\manager\managers\SoundManager;
use core\manager\managers\StatsManager;
use core\manager\managers\terrain\TerrainManager;
use core\manager\managers\TradeManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\entity\Animal;
use pocketmine\entity\Attribute;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;

class DamageListener extends BaseListener {

    private array $tradeRequest = [];

    /**
     * @param EntityDamageEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function entityDamageCave(EntityDamageEvent $e) : void {
        if($e->getEntity() instanceof Villager || $e->getEntity() instanceof CaveSpawn)
            $e->setCancelled(true);
    }

    /**
     * @param EntityDamageEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function onEntityDamage(EntityDamageEvent $e) : void {
        if(!$e instanceof EntityDamageByEntityEvent)
            return;

        if($e->getCause() !== EntityDamageEvent::CAUSE_ENTITY_ATTACK)
            return;

        $entity = $e->getEntity();
        $damager = $e->getDamager();

        if(!$damager instanceof Player)
            return;

        if($entity instanceof Villager) {
            $damager->sendForm(new MainQuestForm($damager));
            $e->setCancelled(true);
            return;
        }

        if($entity instanceof CaveSpawn) {
            if(CaveManager::getCaveByTag($entity->getCaveTag())->isMember($damager->getName())) {
                $damager->sendForm(new ManageCave($damager, $entity->getCaveTag()));
            }
            $e->setCancelled(true);
        }
    }

    public function entityInCave(EntityDamageEvent $e) : void {

        $entity = $e->getEntity();

        if(!$entity instanceof Player)
            return;

        if(!CaveManager::isInCave($entity))
            return;

        $cave = CaveManager::getCave($entity);

        if(!$cave->isMember($entity->getName())) {
            $e->setCancelled(true);
            return;
        }
    }

    public function visitCave(EntityDamageEvent $e) : void {

        if(!$e instanceof EntityDamageByEntityEvent)
            return;

        $damager = $e->getDamager();

        if(!$damager instanceof Player)
            return;

        if($damager->isOp())
            return;

        if(!CaveManager::isInCave($damager))
            return;

        $cave = CaveManager::getCave($damager);

        if(!$cave->isMember($damager->getName())) {
            $e->setCancelled(true);
            return;
        }
    }

    public function FriendlyFireInCave(EntityDamageEvent $e) : void {
        if(!$e instanceof EntityDamageByEntityEvent)
            return;

        $entity = $e->getEntity();
        $damager = $e->getDamager();

        if(!$entity instanceof Player || !$damager instanceof Player)
            return;

        if($damager->isOp())
            return;

        if(!CaveManager::isInCave($damager) || !CaveManager::isInCave($entity))
            return;

        $cave = CaveManager::getCave($damager);

        if(!$cave->isMember($damager->getName()) || !$cave->isMember($entity->getName())) {
            $e->setCancelled(true);
            return;
        }

        if($cave === null)
            return;

        $permission = $cave->getCaveSetting("f_fire");

        if(!$permission) {
            $damager->sendMessage(MessageUtil::format("Friendly fire jest wylaczony w tej jaskini!"));
            $e->setCancelled(true);
        }
    }

    public function lobbyDamageBlock(EntityDamageEvent $e) {
        if($e->getEntity()->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
            $e->setCancelled(true);
    }

    public function StackDamageListener(EntityDamageEvent $e) : void {
        $entity = $e->getEntity();

        if(!$entity instanceof Living || $entity instanceof Player || $entity instanceof CaveSpawn || $entity instanceof Villager || $entity instanceof ItemEntity || $entity instanceof ExperienceOrb || !$entity instanceof Animal)
            return;

        if($e->getFinalDamage() < $entity->getHealth())
            return;

        if($e instanceof EntityDamageByEntityEvent) {
            $damager = $e->getDamager();

            if(!$damager instanceof Player)
                return;

            $itemInHand = $damager->getInventory()->getItemInHand();

            if($itemInHand->hasEnchantment(Enchantment::FIRE_ASPECT))
                $entity->setOnFire(20 * 4 * $itemInHand->getEnchantmentLevel(Enchantment::FIRE_ASPECT));

            $stack = new MobStackerManager($entity);

            if($stack->getStackAmount() > 1) {
                $userManager = UserManager::getUser($damager->getName());
                if($userManager->isSelectedQuest()) {
                    if(!$userManager->hasMadeQuest()) {
                        $quest = $userManager->getSelectedQuest();
                        if($quest->getType() === "KILL")
                            $userManager->addToStatus();

                        if(BossbarManager::getBossbar($damager) != null)
                            QuestManager::update($damager);
                    }
                }
            }
        }

        $mobstacker = new MobStackerManager($entity);
        if($mobstacker->removeStack())
            $e->setCancelled(true);
    }

    public function blockHit(EntityDamageEvent $e) : void {
        if($e instanceof EntityDamageByEntityEvent) {
            $damager = $e->getDamager();
            if(!$damager instanceof Player)
                return;

            if(isset(CpsManager::$blockAttack[$damager->getName()]))
                $e->setCancelled(true);
        }
    }

    public function pvpSystem(EntityDamageEvent $e) : void {
        if(!$e instanceof EntityDamageByEntityEvent)
            return;

        $entity = $e->getEntity();

        if(!$entity instanceof Player)
            return;

        if(!$entity->isSurvival() or !$entity->isAdventure())
            return;

        $motionEntity = $entity->getMotion();
        $x = $motionEntity->getX();
        $z = $motionEntity->getZ();
        $base = 0.315;

        $f = sqrt($x * $x + $z * $z);

        if($f <= 0)
            return;

        if(mt_rand() / mt_getrandmax() > $entity->getAttributeMap()->getAttribute(Attribute::KNOCKBACK_RESISTANCE)->getValue()) {
            $f = 1 / $f;

            $motion = clone $motionEntity;

            $motion->x /= 2;
            $motion->z /= 2;
            $motion->x += $x * $f * $base;
            $motion->z += $z * $f * $base;
            $motion->y = 0.37;

            $entity->setMotion($motion);
        }
    }

    public function tradeOnDamage(EntityDamageEvent $e) : void {
        if(!$e instanceof EntityDamageByEntityEvent)
            return;

        $entity = $e->getEntity();
        $damager = $e->getDamager();

        if(!$entity instanceof Player || !$damager instanceof Player)
            return;

        if(!$entity->isSneaking() || !$damager->isSneaking() || $entity->getName() === $damager->getName())
            return;

        if(!TradeManager::checkTrade($damager)) {
            if(!in_array($damager->getName(), $this->tradeRequest)) {

                TradeManager::sendTrade($damager, $entity);
                $damager->sendMessage(MessageUtil::format("Poprawnie wyslales prosbe o wymiane do gracza §l§9" . $entity->getName() . "§r§7!"));

                $entityUser = UserManager::getUser($entity->getName());

                if($entityUser->isSettingEnabled(SettingsManager::TRADE_REQUEST))
                    $entity->sendMessage(MessageUtil::formatLines(["Otrzymalees prosbe o wymiane od gracza §l§9" . $damager->getName() . "§r§7!", "Aby ja zaakceptowac kucnij i uderz tego gracza"]));

                $this->tradeRequest[] = $damager->getName();

                Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($damager) : void {
                    if(($key = array_search($damager->getName(), $this->tradeRequest)) !== false)
                        unset($this->tradeRequest[$key]);
                }), 20 * 5);
            }
            return;
        }

        if(!TradeManager::checkTrade($damager)) {
            $damager->sendMessage(MessageUtil::format("Nie masz prosby o wymiane od tego gracza!"));
            return;
        }

        TradeManager::acceptTrade($damager);
    }

    /**
     * @param EntityDamageEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function projectDamage(EntityDamageEvent $e) : void {

        $entity = $e->getEntity();
        $entityPosition = $entity->asPosition()->round();

        $entityTarrain = TerrainManager::getPriorityTerrain(Position::fromObject($entityPosition, $entity->getLevel()));

        if($e instanceof EntityDamageByEntityEvent) {

            $damager = $e->getDamager();

            if(!$damager)
                return;

            $damagerPosition = null;

            if($damager instanceof Player)
                $damagerPosition = $damager->asVector3()->round();
            else {
                if($damager->getOwningEntity())
                    $damagerPosition = $damager->getOwningEntity()->asVector3()->round();
                else
                    return;
            }

            $damagerTerrain = TerrainManager::getPriorityTerrain(Position::fromObject($damagerPosition, $entity->getLevel()));

            if($entityTarrain !== null) {
                if(!$entityTarrain->isSettingEnabled("fighting")) {
                    if(!$damager->isOp()) {
                        $e->setCancelled(true);
                        if($damager instanceof Player)
                            $damager->sendTip("§cAtakowanie na tym terenie jest zablokowane!");
                    }
                }
            }

            if($damagerTerrain !== null) {
                if(!$damagerTerrain->isSettingEnabled("fighting")) {
                    if(!$damager->isOp()) {
                        $e->setCancelled(true);
                        if($damager instanceof Player)
                            $damager->sendTip("§cAtakowanie na tym terenie jest zablokowane!");
                    }
                }
            }

        } else {
            if($entityTarrain !== null) {
                if(!$entityTarrain->isSettingEnabled("damage"))
                    $e->setCancelled(true);
            }
        }
    }

    /**
     * @param EntityDamageEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function setAntyLogout(EntityDamageEvent $e) : void {

        if($e->isCancelled())
            return;

        $entity = $e->getEntity();
        $damagerPlayer = null;

        if(!$entity instanceof Player)
            return;

        if($entity->getLevel()->getName() !== ConfigUtil::PVP_WORLD)
            return;

        if($e instanceof EntityDamageByEntityEvent) {
            $damager = $e->getDamager();

            if($damager === $entity)
                return;

            $damagerPlayer = $damager;

            if(!$damager instanceof Player)
                $damager->getOwningEntity() instanceof Player ? $damagerPlayer = $damager->getOwningEntity() : $damagerPlayer = null;

            if(!$damagerPlayer)
                return;

            foreach([$entity, $damager] as $figher) {
                if(!isset(Main::$antylogout[$figher->getName()])) {
                    Main::$antylogout[$figher->getName()] = [
                        "lastAttacker" => $damager->getName(),
                        "time" => (time() + ConfigUtil::ANTYLOGOUT_TIME),
                        "assists" => [$damager->getName() => $e->getFinalDamage()]
                    ];

                    $figher->sendMessage(MessageUtil::format("Rozpoczeto walke!"));
                } else {

                    if(!array_key_exists($damager->getName(), Main::$antylogout[$figher->getName()]["assists"]))
                        Main::$antylogout[$figher->getName()]["assists"][$damager->getName()] = $e->getFinalDamage();
                    else
                        Main::$antylogout[$figher->getName()]["assists"][$damager->getName()] += $e->getFinalDamage();

                    Main::$antylogout[$figher->getName()]["time"] = (time() + ConfigUtil::ANTYLOGOUT_TIME);
                }
            }
        }

        if(isset(Main::$antylogout[$entity->getName()])) {
            if($e->getFinalDamage() >= $entity->getHealth()) {

                $damagerName = "";

                if($damagerPlayer && $damagerPlayer instanceof Player)
                    $damagerName = $damagerPlayer->getName();

                if(!$damagerPlayer) {
                    $damagerName = Main::$antylogout[$entity->getName()]["lastAttacker"];

                    if(!Main::$antylogout[$entity->getName()]["lastAttacker"])
                        return;
                }

                $assistPlayerData = ["nick" => "", "damage" => 0.0];

                foreach(Main::$antylogout[$entity->getName()]["assists"] as $nick => $damage) {

                    if($nick === $damagerName || $nick === $entity->getName())
                        continue;

                    if($assistPlayerData["nick"] === "") {
                        $assistPlayerData["nick"] = $nick;
                        $assistPlayerData["damage"] = $damage;
                    }else{
                        if($damage > $assistPlayerData["damage"]) {
                            $assistPlayerData["nick"] = $nick;
                            $assistPlayerData["damage"] = $damage;
                        }
                    }
                }

                $damager = self::getServer()->getPlayerExact($damagerName);

                UserManager::getUser($assistPlayerData["nick"]) === "" ? $assistUser = null : $assistUser = UserManager::getUser($assistPlayerData["nick"]);

                $assistUser !== null ? $assistPlayer = self::getServer()->getPlayerExact($assistUser->getName()) : $assistPlayer = null;

                $entityUser = UserManager::getUser($entity->getName());
                $damagerUser = UserManager::getUser($damagerName);

                if(!$entityUser || !$damagerUser)
                    return;

                $assistFormat = "";

                if($assistPlayerData["nick"] !== "")
                    $assistFormat = "§7 z pomoca §9".$assistPlayerData["nick"];

                if(isset(Main::$antylogout[$entity->getName()]))
                    unset(Main::$antylogout[$entity->getName()]);

                $entity->addTitle("§l§cSMIERC","§r§8(§7" . $damagerName . "§8)", 20, 40, 20);

                $addMoney = (ConfigUtil::KILL_MONEY + (0.14 * (($ks = $damagerUser->getStat(StatsManager::KILL_STREAK)) >= 10 ? 10 : $ks)));

                if($damagerUser->hasKilled($entity->getName(), $entity->getAddress()))
                    $addMoney = 0;
                else
                    $damagerUser->addToStat(StatsManager::KILL_STREAK, 1);

                if($damager) {
                    SoundManager::addSound($damager, $damager->asPosition(), "ambient.weather.lightning.impact", 1);
                    $damager->addTitle("§l§aZABOJSTWO", "§r§7" . $entity->getName() . " §8(§a+".$addMoney."§7zl§8)", 20, 40, 20);
                }

                $damagerUser->addPlayerMoney($addMoney);
                $damagerUser->addKilledPlayer($entity->getName(), $entity->getAddress());
                $damagerUser->addToStat(StatsManager::KILLS, 1);

                if($assistPlayer) {
                    SoundManager::addSound($damager, $damager->asPosition(), "ambient.weather.lightning.impact", 1);
                    $assistPlayer->addTitle("§l§eASYSTA", "§r§8(§7" . $entity->getName() . "§8)", 20, 40, 20);
                }

                if($assistUser)
                    $assistUser->addToStat(StatsManager::ASSISTS, 1);

                $entityUser->addToStat(StatsManager::DEATHS, 1);
                $entityUser->setStat(StatsManager::KILL_STREAK, 0);

                $pk = new AddActorPacket();
                $pk->type = "minecraft:lightning_bolt";
                $pk->entityRuntimeId = Entity::$entityCount++;
                $pk->position = $entity->asVector3();

                $entity->getServer()->broadcastPacket($entity->getLevel()->getPlayers(), $pk);

                SoundManager::addSound($entity, $entity->asPosition(), "ambient.weather.lightning.impact", 1);

                foreach(self::getServer()->getOnlinePlayers() as $onlinePlayer) {

                    if($onlinePlayer->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
                        continue;

                    $onlinePlayer->sendMessage(MessageUtil::format("§9" . $entity->getName() . " §7zostal zabity przez §9" . $damagerName . " §8(§9" . $addMoney . "§7zl§8)" . $assistFormat));
                }
            }
        }
    }
}