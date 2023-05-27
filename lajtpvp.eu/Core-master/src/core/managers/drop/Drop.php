<?php

declare(strict_types=1);

namespace core\managers\drop;

class Drop {

    public function __construct(
        private int $dropId,

        private string $dropName,
        private string $name,

        private string $color,

        private float $chance,

        private bool $default,

        private int $exp,
        private int $slot,

        private bool $fortune,
        private bool $turbo,

        private string $message,

        private array $deposit,
        private array $bonuses,

        private array $tool,

        private array $amount,
        private array $drop
    ) {}

    public function getDropId() : int {
        return $this->dropId;
    }

    public function getDropName() : string {
        return $this->dropName;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getColor() : string {
        return $this->color;
    }

    public function getChance() : float {
        return $this->chance;
    }

    public function getExpDrop() : int {
        return $this->exp;
    }

    public function getSlot() : int {
        return $this->slot;
    }

    public function isDefault() : bool {
        return $this->default;
    }

    public function isFortune() : bool {
        return $this->fortune;
    }

    public function isTurbo() : bool {
        return $this->turbo;
    }

    public function getMessage() : string {
        return $this->message;
    }

    public function getDeposit() : array {
        return $this->deposit;
    }

    public function getBonuses() : array {
        return $this->bonuses;
    }

    public function getTools() : array {
        return $this->tool;
    }

    public function getAmount() : array {
        return $this->amount;
    }

    public function getDrop() : array {
        return $this->drop;
    }
}