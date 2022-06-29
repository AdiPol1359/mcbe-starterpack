<?php

namespace core\manager\managers\bossbar;

use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\Player;

class Bossbar extends Vector3 {

    protected int $entityId;
    protected string $title;
    protected string $subTitle;
    protected float $healthPercent;
    protected array $viewers = [];

    public function __construct(string $title = "", float $hp = 1.0, string $subTitle = "") {
        parent::__construct();

        $this->entityId = Entity::$entityCount++;
        $this->title = $title;
        $this->subTitle = $subTitle;
        $this->setHealthPercent($hp, false);
    }

    public function getTitle() : string {
        return $this->title;
    }

    public function getSubTitle() : string{
        return $this->subTitle;
    }

    public function getFullTitle(): string
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
        $pk->bossEid = $this->entityId;
        $pk->eventType = $eventType;

        switch($eventType) {
            case BossEventPacket::TYPE_SHOW:
                $pk->title = $this->getFullTitle();
                $pk->healthPercent = $this->healthPercent;
                $pk->color = 0;
                $pk->overlay = 0;
                $pk->unknownShort = 0;
                break;
            case BossEventPacket::TYPE_REGISTER_PLAYER:
            case BossEventPacket::TYPE_UNREGISTER_PLAYER:
                $pk->playerEid = $player->getId();
                break;
            case BossEventPacket::TYPE_TITLE:
                $pk->title = $this->getFullTitle();
                break;
            case BossEventPacket::TYPE_HEALTH_PERCENT:
                $pk->healthPercent = $this->healthPercent;
                break;
        }

        $player->sendDataPacket($pk);
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
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->entityId;
        $pk->type = AddActorPacket::LEGACY_ID_MAP_BC[EntityIds::SLIME];
        $pk->metadata = [
            Entity::DATA_FLAGS => [
                Entity::DATA_TYPE_LONG,
                ((1 << Entity::DATA_FLAG_INVISIBLE) | (1 << Entity::DATA_FLAG_IMMOBILE))
            ],
            Entity::DATA_NAMETAG => [
                Entity::DATA_TYPE_STRING,
                $this->getFullTitle()
            ]
        ];
        $pk->position = $this;

        $player->sendDataPacket($pk);
        $this->sendBossEventPacket($player, BossEventPacket::TYPE_SHOW);

        if($isViewer)
            $this->viewers[spl_object_id($player)] = $player;

        BossbarManager::setBossbar($player, $this);
    }

    public function hideFrom(Player $player) {
        $this->sendBossEventPacket($player, BossEventPacket::TYPE_HIDE);

        $pk2 = new RemoveActorPacket();
        $pk2->entityUniqueId = $this->entityId;

        $player->sendDataPacket($pk2);

        if(isset($this->viewers[spl_object_id($player)])) {
            unset($this->viewers[spl_object_id($player)]);
        }

        BossbarManager::unsetBossbar($player);
    }

    public function getViewers() : array {
        return $this->viewers;
    }

    public function getEntityId() : int {
        return $this->entityId;
    }
}