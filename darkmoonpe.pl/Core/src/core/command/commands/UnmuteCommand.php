<?php

namespace core\command\commands;

use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

use core\{
    command\BaseCommand,
    manager\managers\SoundManager,
    manager\managers\MuteManager,
    util\utils\MessageUtil};

class UnmuteCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("unmute", "Unmute Command", true, true, "Komenda unmute sluzy do odciszania gracza", ['odmutuj']);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(empty($args) || !isset($args[0])) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["nick"]]));
            return;
        }

        $target = $this->selectPlayer($player, $args, 0, true, false);

        if(!MuteManager::isMuted($target)) {
            $player->sendMessage(MessageUtil::format("Ten gracz nie jest §9zmutowany§7!"));
            return;
        }

        MuteManager::unMute($target);
        $p = $this->getServer()->getPlayer($target);
        if($p != null) {
            $p->sendMessage(MessageUtil::format("Zostales odmutowany przez administratora o nicku §9{$player->getName()}§7!"));
            SoundManager::addSound($p, $p->asVector3(), "mob.villager.idle");
        }

        $player->sendMessage(MessageUtil::format("Poprawnie odmutowales gracza o nicku §9§l".$target."§7!"));
    }
}