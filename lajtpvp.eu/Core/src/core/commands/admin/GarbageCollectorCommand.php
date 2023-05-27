<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\entity\object\ItemEntity;

class GarbageCollectorCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("garbagecollector", "", true, true, ["clearlag", "gc"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        $chunksCollected = 0;
        $entitiesCollected = 0;
        $itemEntities = 0;

        foreach($sender->getServer()->getWorldManager()->getWorlds() as $level){
            $diff = [count($level->getLoadedChunks()), count($level->getEntities())];
            $level->doChunkGarbageCollection();
            $level->unloadChunks(true);
            $chunksCollected += $diff[0] - count($level->getLoadedChunks());
            $entitiesCollected += $diff[1] - count($level->getEntities());
            $level->clearCache(true);

            foreach($level->getEntities() as $entity) {
                if($entity instanceof ItemEntity) {
                    $entity->close();
                    $itemEntities++;
                }
            }
        }

        $sender->sendMessage(MessageUtil::formatLines([
            "Wyczyszczone chunki§8: §e".number_format($chunksCollected),
            "Wyczyszczone entity§8: §e".number_format($entitiesCollected),
            "Usuniete przedmioty z ziemi§8: §e".number_format($itemEntities),
        ]));
    }
}