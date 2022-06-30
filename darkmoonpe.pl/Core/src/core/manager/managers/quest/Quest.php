<?php

namespace core\manager\managers\quest;

use pocketmine\utils\TextFormat;

class Quest {

    private ?float $id;
    private ?string $cleanName;
    private ?string $type;
    private ?string $name;
    private $itemId;
    private ?int $itemDamage;
    private ?float $maxTimes;
    private ?string $rewardName;
    private ?string $cleanRewardName;
    private ?string $rewardType;
    private ?int $rewardId;
    private ?int $rewardDamage;
    private ?float $rewardCount;

    public function __construct(int $id, string $name, string $type, $itemId, int $itemDamage, float $maxTimes, string $rewardName = "BRAK", ?string $rewardType = null, ?int $rewardId = null, ?int $rewardDamage = null, ?float $rewardCount = null) {
        $this->id = $id;
        $this->name = $name;
        $this->cleanName = TextFormat::clean($name);
        $this->type = $type;
        $this->itemId = $itemId;
        $this->itemDamage = $itemDamage;
        $this->maxTimes = $maxTimes;
        $this->rewardName = $rewardName;
        $this->cleanRewardName = TextFormat::clean($rewardName);
        $this->rewardType = $rewardType;
        $this->rewardId = $rewardId;
        $this->rewardDamage = $rewardDamage;
        $this->rewardCount = $rewardCount;
    }

    public function getId() : ?int {
        return $this->id;
    }

    public function getName() : ?string {
        return $this->name;
    }

    public function getCleanName() : ?string {
        return $this->cleanName;
    }

    public function getType() : ?string {
        return $this->type;
    }

    public function getItemId() {
        return $this->itemId;
    }

    public function getItemDamage() : ?int {
        return $this->itemDamage;
    }

    public function getMaxTimes() : ?float {
        return $this->maxTimes;
    }

    public function getRewardName() : ?string {
        return $this->rewardName;
    }

    public function getCleanRewardName() : ?string {
        return $this->cleanRewardName;
    }

    public function getRewardType() : ?string {
        return $this->rewardType;
    }

    public function getRewardId() : ?int {
        return $this->rewardId;
    }

    public function getRewardDamage() : ?int {
        return $this->rewardDamage;
    }

    public function getRewardCount() : ?float {
        return $this->rewardCount;
    }
}