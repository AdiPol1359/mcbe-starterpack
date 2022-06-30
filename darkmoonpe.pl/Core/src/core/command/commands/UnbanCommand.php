<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

use core\manager\managers\{
    BanManager
};
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class UnbanCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("unban", "Unban Command", true, true, "Komenda unban sluzy do odbanowywania gracza", ["odbanuj"]);

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

        if(!BanManager::isBanned($target)) {
            $player->sendMessage(MessageUtil::format("Ten gracz nie jest §9zbanowany§7!"));
            return;
        }

        foreach($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
            if($onlinePlayer->getLevel()->getName() !== ConfigUtil::LOBBY_WORLD)
                $onlinePlayer->sendMessage(MessageUtil::formatLines(["Gracz o nicku §9§l".$target, "§r§7zostal odbanowany przez §9§l{$player->getName()}"]));
        }

        $player->sendMessage(MessageUtil::format("Poprawnie odbanowano gracza o nicku §9§l".$target."§r§7!"));
        BanManager::unBan($target);
    }
}