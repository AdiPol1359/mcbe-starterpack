<?php

namespace core\manager\managers\hazard\types\roulette;

class RoulettePlayer {

    private string $name;
    private array $bets;

    public function __construct(string $name, array $bets) {
        $this->name = $name;
        $this->bets = $bets;
    }

    public function getBets() : array {
        return $this->bets;
    }

    public function setBet(string $color, float $amount) : void {
        $this->bets[$color] = $amount;
    }

    public function addToBet(string $color, float $amount) : void {
        $this->bets[$color] += $amount;
    }

    public function getBetAmounts() : array {

        $bets = [];

        foreach($this->bets as $color => $amount) {
            if($amount > 0)
                $bets[$color] = (float) number_format($amount, 2, '.', '');
        }

        return $bets;
    }

    public function getBetAmount(string $color) : float {
        return $this->bets[$color] ?? 0;
    }
}