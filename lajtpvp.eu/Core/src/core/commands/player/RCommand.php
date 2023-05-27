<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\Main;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\world\sound\PopSound;

class RCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("r", "", false, false);

        $parameters = [
            0 => [
                $this->commandParameter("wiadomosc", AvailableCommandsPacket::ARG_TYPE_MESSAGE, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(empty($args)){
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["wiadomosc"]]));
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($sender->getName());
        $message = implode(" ", $args);

        if(!$user->getLastPrivateMessage()) {
            $sender->sendMessage(MessageUtil::format("Nikt do ciebie nie wyslal zadnej wiadomosci!"));
            return;
        }

        if(!($selectedPlayer = $sender->getServer()->getPlayerExact($user->getLastPrivateMessage()))) {
            $sender->sendMessage(MessageUtil::format("Gracz ktory wyslal do ciebie wiadomosc wyszedl z serwera!"));
            return;
        }

        if(!PermissionUtil::has($sender, Settings::$PERMISSION_TAG."ignore.bypass")) {
            if(($user = Main::getInstance()->getUserManager()->getUser($selectedPlayer->getName()))) {
                if($user->getIgnoreManager()->isIgnoring($sender->getName())) {
                    $sender->sendMessage(MessageUtil::format("Ten gracz wyciszyl wiadomosci od ciebie!"));
                    return;
                }
            }
        }

        $sender->sendMessage(MessageUtil::format("§2§eJa§r§7 - §6".$selectedPlayer->getName()."§r§7: ".$message));
        $selectedPlayer->sendMessage(MessageUtil::format("§2§e".$sender->getName()."§r§7: ".$message));

        $selectedPlayer->getWorld()->addSound($selectedPlayer->getPosition(), new PopSound(), [$selectedPlayer]);
    }
}