<?php

declare(strict_types=1);

namespace core\items;

use pocketmine\item\EnderPearl;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

class ProjectileEnderPearl extends EnderPearl{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::ENDER_PEARL, 0), "Ender Pearl");
    }

    public function getMaxStackSize() : int{
        return 16;
    }

    public function getProjectileEntityType() : string{
        return "ThrownEnderpearl";
    }

    public function getThrowForce() : float{
        return 2;
    }

    public function getCooldownTicks() : int{
        return 0;
    }
}