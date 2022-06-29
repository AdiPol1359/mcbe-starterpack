<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\Main;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class IgnoreCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("ignore", "Ignore Command", false, true, "Komenda sluzy do blokowania wiadomosci od wybranego gracza");

        $parameters = [
            0 => [
                $this->commandParameter("ignoreOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "ignoreOptions", ["add", "remove"]),
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(empty($args) || !isset($args[1])){
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["add", "remove"], ["nick"]]));
            return;
        }

        $argument = $args;
        array_shift($argument);

        $argsPlayer = implode(" ", $argument);

        if($argsPlayer === $player->getName()){
            $player->sendMessage(MessageUtil::format("Nie mozesz zablokowac wiadomosci od samego siebie!"));
            return;
        }

        if(Main::getGroupManager()->userExists($argsPlayer)) {
            $group = Main::getGroupManager()->getPlayer($argsPlayer);
            if($group->hasPermission(ConfigUtil::PERMISSION_TAG."ignore")) {
                $player->sendMessage(MessageUtil::format("Nie mozesz wyciszyc administratora!"));
                return;
            }
        }

        if(empty(Main::$ignore[$player->getName()]))
            Main::$ignore[$player->getName()] = [];

        switch($args[0]){
            case "add":

                if(($key = array_search($argsPlayer, Main::$ignore[$player->getName()])) !== false){
                    $player->sendMessage(MessageUtil::format("Masz juz wyciszone wiadomosci od tego gracza!"));
                    return;
                }

                Main::$ignore[$player->getName()][] = $argsPlayer;
                $player->sendMessage(MessageUtil::format("Poprawnie zablokowales wiadomosci od gracza §l§9".$argsPlayer."§r§7!"));
                break;

            case "remove":

                if(($key = array_search($argsPlayer, Main::$ignore[$player->getName()])) === false){
                    $player->sendMessage(MessageUtil::format("Nie masz wyciszonego tego gracza!"));
                    return;
                }

                unset(Main::$ignore[$player->getName()][$key]);
                $player->sendMessage(MessageUtil::format("Poprawnie odciszyles wiadomosci od gracza §l§9".$argsPlayer."§r§7!"));
                break;

            default:
                $player->sendMessage($this->correctUse($this->getCommandLabel(), [["add", "remove"], ["nick"]]));
                break;
        }
    }
}