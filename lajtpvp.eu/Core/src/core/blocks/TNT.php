<?php

declare(strict_types=1);

namespace core\blocks;

use core\entities\object\PrimedTNT;
use core\managers\TNTManager;
use core\utils\Settings;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier as BID;
use pocketmine\block\BlockLegacyIds as Ids;
use pocketmine\block\TNT as PMTnt;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\Random;

class TNT extends PMTnt {

    public function __construct() {
        parent::__construct(new BID(Ids::TNT, 0), "TNT", BlockBreakInfo::instant());
    }

    public function ignite(int $fuse = 100) : void {
        if(!Settings::$TNTHASENABLED)
            return;

        $position = $this->getPosition();
        $world = $position->getWorld();
        $mot = (new Random())->nextSignedFloat() * M_PI * 2;

        $this->getPosition()->getWorld()->setBlock($position, VanillaBlocks::AIR());

        $entityNbt = CompoundTag::create()->setShort("Fuse", $fuse);

        $location = Location::fromObject($position->add(0.5, 0, 0.5), $world, 0, 0);

        $tnt = (new PrimedTNT($location, $entityNbt));

        $tnt->setFuse($fuse);
        $tnt->setWorksUnderwater($this->worksUnderwater);
        $tnt->setMotion(new Vector3(-sin($mot) * 0.02, 0.2, -cos($mot) * 0.02));
    }
}