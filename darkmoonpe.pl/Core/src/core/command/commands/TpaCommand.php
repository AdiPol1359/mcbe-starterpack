<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\manager\managers\SoundManager;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

use core\Main;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class TpaCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("tpa", "Tpa Command", true, false, "Komenda tpa sluzy do wysylania prosb o teleportacje do danego gracza");

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(empty($args)) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["nick"]]));
            return;
        }

        $p = $this->getServer()->getPlayer($args[0]);

        if($p == null) {
            $player->sendMessage(MessageUtil::format("Ten gracz jest §l§9OFFLINE"));
            return;
        }

        if($p->getName() == $player->getName()) {
            $player->sendMessage(MessageUtil::format("Nie mozesz wyslac prosby o teleportacje do siebie"));
            return;
        }

        $nick = $player->getName();
        $teleportPlayerNick = $p->getName();

        if(isset(Main::$tp[$teleportPlayerNick][$nick])) {
            $player->sendMessage(MessageUtil::format("Wysylales juz prosbe o teleportacje do gracza §l§9{$teleportPlayerNick}"));
            return;
        }

        Main::$tp[$teleportPlayerNick][$nick] = time() + 30;
        $player->sendMessage(MessageUtil::format("Wyslano prosbe o teleportacje do gracza §l§9{$teleportPlayerNick}"));
        SoundManager::addSound($p, $p->asVector3(), "mob.villager.idle");
        $p->sendMessage(MessageUtil::formatLines(["Gracz §9§l$nick §r§7wyslal do Ciebie prosbe o teleportacje!", "Wpisz: §9§l/tpaccept§r§7, aby zaakceptowac", "Albo §9§l/tpdeny§r§7, aby odrzucic"]));
    }
}