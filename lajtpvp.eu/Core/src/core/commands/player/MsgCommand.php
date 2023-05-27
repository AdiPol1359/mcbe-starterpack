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
use pocketmine\player\Player;
use pocketmine\world\sound\PopSound;

class MsgCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("msg", "", false, false);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("wiadomosc", AvailableCommandsPacket::ARG_TYPE_MESSAGE, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(empty($args)){
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["nick"], ["wiadomosc"]]));
            return;
        }

        if(!($selected = $sender->getServer()->getPlayerByPrefix(array_shift($args)))) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest offline!"));
            return;
        }

        if($selected->getName() === $sender->getName()){
            $sender->sendMessage(MessageUtil::format("Nie mozesz wyslac wiadomosci do samego siebie!"));
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($sender->getName());
        $selectedUser = Main::getInstance()->getUserManager()->getUser($selected->getName());

        if(!$selectedUser || !$user) {
            $sender->sendMessage(MessageUtil::format("Gracz nie jest jeszcze zarejestrowany!"));
            return;
        }

        if(!PermissionUtil::has($sender, Settings::$PERMISSION_TAG."ignore.bypass")) {
            if(($user = Main::getInstance()->getUserManager()->getUser($selected->getName()))) {
                if($user->getIgnoreManager()->isIgnoring($sender->getName())) {
                    $sender->sendMessage(MessageUtil::format("Ten gracz wyciszyl wiadomosci od ciebie!"));
                    return;
                }
            }
        }

        $message = implode(" ", $args);

        $selected->sendMessage(MessageUtil::format("§2§e".$sender->getName()."§r§7: ".$message));
        $sender->sendMessage(MessageUtil::format("§2§eJa§r§7 - §6".$selected->getName()."§r§7: ".$message));

        $user->setLastPrivateMessage($selected->getName());
        $selectedUser->setLastPrivateMessage($sender->getName());

        $selected->getWorld()->addSound($selected->getPosition(), new PopSound(), [$selected]);
        $sender->getWorld()->addSound($sender->getPosition(), new PopSound(), [$sender]);
    }
}