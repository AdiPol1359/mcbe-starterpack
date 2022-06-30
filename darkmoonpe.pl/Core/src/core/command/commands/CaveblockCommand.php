<?php

namespace core\command\commands;

use core\command\BaseCommand;
use pocketmine\command\CommandSender;
use core\form\forms\caveblock\{
    CaveblockMain,
    HasCave,
    ManageCave};

use core\caveblock\CaveManager;

class CaveblockCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("caveblock", "CaveBlock Command", false, false, "Komenda caveblock sluzy do zarzadzania jaskinia, jej permisjami, czlonkami itd.", ["cb", "cblock", "caveb", "cave"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(CaveManager::hasCave($player)) {
            if(CaveManager::isInCave($player)) {
                if(CaveManager::getCaveByTag($caveName = CaveManager::getCave($player)->getName())->isMember($player->getName())) {
                    $player->sendForm(new ManageCave($player, CaveManager::getCaveByTag($caveName)));
                    return;
                }
            }
            $player->sendForm(new HasCave());
        }else
            $player->sendForm(new CaveblockMain());
    }
}