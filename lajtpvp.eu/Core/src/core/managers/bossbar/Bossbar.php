<?php

namespace core\managers\bossbar;

use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataTypes;
use pocketmine\player\Player;

class Bossbar extends Vector3 {

    /** @var EntityMetadataCollection */
    private EntityMetadataCollection $networkProperties;

    protected int $entityId;
    protected float $healthPercent;
    protected array $viewers = [];

    public function __construct(protected string $title = "", float $hp = 1.0, protected string $subTitle = "", protected int $color = BossbarColor::COLOR_PINK) {
        parent::__construct(0, 0, 0);
        $this->networkProperties = new EntityMetadataCollection();

        $this->entityId = Entity::nextRuntimeId();
        $this->setHealthPercent($hp, false);
    }

    public function getTitle() : string {
        return $this->title;
    }

    public function getSubTitle() : string{
        return $this->subTitle;
    }

    #[Pure] public function getFullTitle(): string
    {
        $text = $this->title;
        if (!empty($this->subTitle))
            $text .= "\n\n" . $this->subTitle;

        return mb_convert_encoding($text, 'UTF-8');
    }

    public function setTitle(string $title, bool $update = true) {
        $this->title = $title;

        if($update) {
            $this->updateForAll();
        }
    }

    public function setSubTitle(string $subTitle, bool $update = true) : void{
        $this->subTitle = $subTitle;

        if($update)
            $this->updateForAll();
    }

    public function updateForAll() : void {
        foreach($this->viewers as $player)
            $this->updateFor($player);
    }

    public function updateFor(Player $player) {
        $this->sendBossEventPacket($player, BossEventPacket::TYPE_HEALTH_PERCENT);
        $this->sendBossEventPacket($player, BossEventPacket::TYPE_TITLE);
    }

    protected function sendBossEventPacket(Player $player, int $eventType) : void {
        $pk = new BossEventPacket();
        $pk->bossActorUniqueId = $this->entityId;
        $pk->eventType = $eventType;

        switch($eventType) {
            case BossEventPacket::TYPE_SHOW:
                $pk->title = $this->getFullTitle();
                $pk->healthPercent = $this->healthPercent;
                $pk->color = $this->color;
                $pk->overlay = 0;
                $pk->unknownShort = 0;
                break;
            case BossEventPacket::TYPE_REGISTER_PLAYER:
            case BossEventPacket::TYPE_UNREGISTER_PLAYER:
                $pk->playerActorUniqueId = $player->getId();
                break;
            case BossEventPacket::TYPE_TITLE:
                $pk->title = $this->getFullTitle();
                break;
            case BossEventPacket::TYPE_HEALTH_PERCENT:
                $pk->healthPercent = $this->healthPercent;
                break;
        }

        $player->getNetworkSession()->sendDataPacket($pk);
    }

    public function getHealthPercent() : float {
        return $this->healthPercent;
    }

    public function setHealthPercent(float $hp, bool $update = true) {
        $this->healthPercent = max(0, min(1.0, $hp));

        if($update) {
            $this->updateForAll();
        }
    }

    public function showTo(Player $player, bool $isViewer = true) {
        $this->networkProperties->setLong(EntityMetadataProperties::FLAGS,
            EntityMetadataTypes::LONG,
            ((1 << EntityMetadataFlags::INVISIBLE) | (1 << EntityMetadataFlags::IMMOBILE))
        );

        $this->networkProperties->setString(EntityMetadataProperties::NAMETAG, $this->getFullTitle());

        $pk = AddActorPacket::create(
            $this->entityId,
            $this->entityId,
            "minecraft:slime",
            $this,
            Vector3::zero(),
            0,
            0,
            0,
            [],
            $this->networkProperties->getAll(),
            []
        );

//        $ar = [
//            EntityMetadataProperties::FLAGS => [
//                EntityMetadataTypes::LONG,
//                ((1 << EntityMetadataFlags::INVISIBLE) | (1 << EntityMetadataFlags::IMMOBILE))
//            ],
//            EntityMetadataProperties::NAMETAG => [
//                EntityMetadataTypes::STRING,
//                $this->getFullTitle()
//            ]
//        ]

        $player->getNetworkSession()->sendDataPacket($pk);
        $this->sendBossEventPacket($player, BossEventPacket::TYPE_SHOW);

        if($isViewer)
            $this->viewers[spl_object_id($player)] = $player;

        BossbarManager::setBossbar($player, $this);
    }

    public function hideFrom(Player $player) {
        $this->sendBossEventPacket($player, BossEventPacket::TYPE_HIDE);

        $pk2 = new RemoveActorPacket();
        $pk2->actorUniqueId = $this->entityId;

        $player->getNetworkSession()->sendDataPacket($pk2);

        if(isset($this->viewers[spl_object_id($player)]))
            unset($this->viewers[spl_object_id($player)]);

        BossbarManager::unsetBossbar($player);
    }

    public function getViewers() : array {
        return $this->viewers;
    }

    public function setColor(int $color) : void {
        $this->color = $color;
    }

    public function getEntityId() : int {
        return $this->entityId;
    }
}