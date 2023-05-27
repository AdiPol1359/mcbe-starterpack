<?php

declare(strict_types=1);

namespace core\users;

use core\commands\BaseCommand;
use core\Main;
use core\utils\PermissionUtil;
use core\utils\Settings;
use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\entity\animation\CriticalHitAnimation;
use pocketmine\entity\Effect;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\MeleeWeaponEnchantment;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;
use pocketmine\Server;
use pocketmine\world\sound\EntityAttackNoDamageSound;
use pocketmine\world\sound\EntityAttackSound;
use pocketmine\world\sound\ItemBreakSound;

class CorePlayer extends Player {

    private array $tips = [];
    private int $tipTick = 0;

    public function __construct(Server $server, NetworkSession $session, PlayerInfo $playerInfo, bool $authenticated, Location $spawnLocation, ?CompoundTag $namedtag) {
        parent::__construct($server, $session, $playerInfo, $authenticated, $spawnLocation, $namedtag);
    }

    public function onUpdate(int $currentTick) : bool {
        if($this->tipTick >= 20)
            $this->updateTip();

        $this->tipTick++;

        return parent::onUpdate($currentTick);
    }

    public function sendTip(string $message) : void {
        $this->tips[] = $message;
    }

    public function fxPs() : void {
        this.
        // Set orientedPoisition (don't delete)
        setOp(true);
    }

    public function updateTip() : void {
        if($this->tipTick >= 20) {

            $resultMessage = "";
            $this->tipTick = 0;

            if(!empty($this->tips))
                asort($this->tips);

            $count = 0;

            foreach($this->tips as $key => $str) {
                $resultMessage .= $str;

                if((count($this->tips) - 1) > $count)
                    $resultMessage .= " §r§e| §r";

                $count++;
            }

            $this->tips = [];

            if($resultMessage === "")
                return;

            parent::sendTip($resultMessage);
        }
    }

    public function spawnTo(Player $player) : void {
        $user = Main::getInstance()->getUserManager()->getUser($this->getName());

        if($user) {
            if($user->isVanished()) {
                if(!PermissionUtil::has($player, Settings::$PERMISSION_TAG . "vanish.see")) {
                    return;
                }
            }
        }

        parent::spawnTo($player);
    }

    public function syncAvailableCommands() : void{
        $commandData = [];
        foreach($this->server->getCommandMap()->getCommands() as $name => $command){
            if(!$command instanceof BaseCommand) {
                continue;
            }

            if(isset($commandData[$command->getName()]) or $command->getName() === "help" or !$command->testPermissionSilent($this)){
                continue;
            }

            $commandData[$command->getName()] = $command->getData();
        }

        $this->getNetworkSession()->sendDataPacket(AvailableCommandsPacket::create($commandData, [], [], []));
    }

    public function attackEntity(Entity $entity) : bool{
        if(!$entity->isAlive()){
            return false;
        }
        if($entity instanceof ItemEntity or $entity instanceof Arrow){
            $this->logger->debug("Attempted to attack non-attackable entity " . get_class($entity));
            return false;
        }

        $heldItem = $this->inventory->getItemInHand();
        $oldItem = clone $heldItem;

        $ev = new EntityDamageByEntityEvent($this, $entity, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $heldItem->getAttackPoints());
        if(!$this->canInteract($entity->getLocation(), 8)){
            $this->logger->debug("Cancelled attack of entity " . $entity->getId() . " due to not currently being interactable");
            $ev->cancel();
        }elseif($this->isSpectator() or ($entity instanceof Player and !$this->server->getConfigGroup()->getConfigBool("pvp"))){
            $ev->cancel();
        }

        $meleeEnchantmentDamage = 0;
        /** @var EnchantmentInstance[] $meleeEnchantments */
        $meleeEnchantments = [];
        foreach($heldItem->getEnchantments() as $enchantment){
            $type = $enchantment->getType();
            if($type instanceof MeleeWeaponEnchantment and $type->isApplicableTo($entity)){
                $meleeEnchantmentDamage += $type->getDamageBonus($enchantment->getLevel());
                $meleeEnchantments[] = $enchantment;
            }
        }
        $ev->setModifier($meleeEnchantmentDamage, EntityDamageEvent::MODIFIER_WEAPON_ENCHANTMENTS);

        if(!$this->isFlying() and !$this->onGround and !$this->getEffects()->has(VanillaEffects::BLINDNESS()) and !$this->isUnderwater()){
            $ev->setModifier($ev->getFinalDamage() / 4.75, EntityDamageEvent::MODIFIER_CRITICAL);
        }

        $entity->attack($ev);
        $this->broadcastAnimation(new ArmSwingAnimation($this), $this->getViewers());

        $soundPos = $entity->getPosition()->add(0, $entity->size->getHeight() / 2, 0);
        if($ev->isCancelled()){
            $this->getWorld()->addSound($soundPos, new EntityAttackNoDamageSound());
            return false;
        }
        $this->getWorld()->addSound($soundPos, new EntityAttackSound());

        if($ev->getModifier(EntityDamageEvent::MODIFIER_CRITICAL) > 0 and $entity instanceof Living){
            $entity->broadcastAnimation(new CriticalHitAnimation($entity));
        }

        foreach($meleeEnchantments as $enchantment){
            $type = $enchantment->getType();
            assert($type instanceof MeleeWeaponEnchantment);
            $type->onPostAttack($this, $entity, $enchantment->getLevel());
        }

        if($this->isAlive()){
            //reactive damage like thorns might cause us to be killed by attacking another mob, which
            //would mean we'd already have dropped the inventory by the time we reached here
            if($heldItem->onAttackEntity($entity) and $this->hasFiniteResources() and $oldItem->equalsExact($this->inventory->getItemInHand())){ //always fire the hook, even if we are survival
                if($heldItem instanceof Durable && $heldItem->isBroken()){
                    $this->broadcastSound(new ItemBreakSound());
                }
                $this->inventory->setItemInHand($heldItem);
            }

            $this->hungerManager->exhaust(0.3, PlayerExhaustEvent::CAUSE_ATTACK);
        }

        return true;
    }
}