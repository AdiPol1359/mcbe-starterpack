<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\Main;
use core\manager\managers\AdminManager;
use core\manager\managers\particle\ParticleManager;
use core\manager\managers\pet\PetManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class VanishCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("vanish", "Vanish Command", true, false, "Komenda vanish sluzy do ukrywania postaci", ['v']);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        isset($args[0]) ? $targetName = $args[0] : $targetName = $player->getName();
        is_null($this->getServer()->getPlayer($targetName)) ? $target = null : $target = $this->getServer()->getPlayer($targetName);

        if($target === null) {
            $player->sendMessage(MessageUtil::format("Ten gracz jest §l§9OFFLINE"));
            return;
        }

        if(!in_array($targetName, Main::$vanish)) {
            Main::$vanish[] = $targetName;

            foreach($this->getServer()->getOnlinePlayers() as $pl) {
                if(!$pl->hasPermission(ConfigUtil::PERMISSION_TAG . "vanish.see"))
                    $pl->hidePlayer($target);
            }

            if(($pets = PetManager::getSpecifyPlayerPets($target->getName())) !== null) {
                foreach($pets as $playerPet)
                    $playerPet->getEntity()->close();
            }

            if(($particles = ParticleManager::getPlayerParticles($target->getName())) !== null) {
                foreach($particles as $playerParticle)
                    $playerParticle->removePlayer($player->getName());
            }

            if($target !== $player) {
                AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7wlaczyl vanisha §l§9".$target->getName()."§r§7!"), [$player->getName()]);
                $target->sendMessage(MessageUtil::format("Administrator o nicku §l§9" . $player->getName() . " §r§7wlaczyl ci vanisha!"));
                $player->sendMessage(MessageUtil::format("Pomyslnie wlaczyles vanisha dla gracza §9§l{$target->getName()}"));
            } else {
                AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7wlaczyl sobie vanisha"), [$player->getName()]);
                $player->sendMessage(MessageUtil::format("Wlaczyles vanisha!"));
            }
        } else {
            $key = array_search($targetName, Main::$vanish);
            unset(Main::$vanish[$key]);

            $target->spawnToAll();

            if($target !== $player)
                $player->sendMessage("§8» §7Pomyslnie wylaczono vanish graczu §9{$targetName}§7!");

            foreach($this->getServer()->getOnlinePlayers() as $pl)
                $pl->showPlayer($target);

            if($target !== $player) {
                AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7wylaczyl vanisha §l§9".$target->getName()."§r§7!"), [$player->getName()]);
                $target->sendMessage(MessageUtil::format("Administrator o nicku §l§9" . $player->getName() . " §r§7wylaczyl ci vanisha!"));
                $player->sendMessage(MessageUtil::format("Pomyslnie wylaczyles vanisha dla gracza §9§l{$target->getName()}"));
            } else {
                AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7wylaczyl sobie vanisha"), [$player->getName()]);
                $player->sendMessage(MessageUtil::format("Wylaczyles vanisha!"));
            }
        }
    }
}