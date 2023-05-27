<?php

declare(strict_types=1);

namespace core\managers\warp;

use core\Main;
use core\utils\VectorUtil;
use JetBrains\PhpStorm\Pure;
use pocketmine\world\Position;

class WarpManager {
    
    /** @var Warp[] */
    private array $warps = [];
    
    public function __construct(private Main $plugin) {}

    public function loadWarps() : void {
        foreach($this->plugin->getProvider()->getQueryResult("SELECT * FROM warp", true) as $row) {
            $this->warps[base64_encode($row["name"])] = new Warp($row["name"], VectorUtil::getPositionFromData($row["position"]));
        }
    }

    public function save() : void {
        foreach($this->plugin->getProvider()->getQueryResult("SELECT * FROM warp", true) as $row) {
            if(!$this->getWarp($row["name"])) {
                $this->plugin->getProvider()->executeQuery("DELETE FROM warp WHERE name = '" . $row["name"] . "'");
            }
        }

        foreach($this->warps as $warpName => $warp) {
            if(!empty($db = ($this->plugin->getProvider()->getQueryResult("SELECT * FROM warp WHERE name = '" . $warp->getName() . "'", true)))) {
                if($db["position"] !== $warp->getPosition()->__toString()) {
                    $this->plugin->getProvider()->executeQuery("DELETE FROM warp WHERE name = '" . $warp->getName() . "'");
                }
            }

            if(empty($this->plugin->getProvider()->getQueryResult("SELECT * FROM warp WHERE name = '" . $warp->getName() . "'", true))) {
                $this->plugin->getProvider()->executeQuery("INSERT INTO warp (name, position) VALUES ('" . $warp->getName() . "', '" . $warp->getPosition()->__toString() . "')");
            }
        }
    }
    
    #[Pure] public function getWarp(string $warpName) : ?Warp {
        return $this->warps[base64_encode($warpName)] ?? null;
    }

    public function setWarp(string $warpName, Position $position) : void {
        $this->warps[base64_encode($warpName)] = new Warp($warpName, $position);
    }

    public function deleteWarp(string $warpName) : void {
        if(isset($this->warps[base64_encode($warpName)])) {
            unset($this->warps[base64_encode($warpName)]);
        }
    }

    public function getWarps() : array {
        return $this->warps;
    }
}