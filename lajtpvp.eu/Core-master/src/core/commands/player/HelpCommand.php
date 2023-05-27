<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class HelpCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("pomoc", "", false, true, ["?"]);

        $parameters = [
            0 => [
                $this->commandParameter("helpOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "helpOptions", ["ogolne", "gildie"])
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["ogolne", "gildie"]]));
            return;
        }

        switch($args[0]) {
            case "ogolne":

                $data = [];

                foreach(Settings::$HELP_GENERAL_COMMANDS as $commandName => $commandDescription)
                    $data[] = "§8/§e".$commandName." §8- §7".$commandDescription;

                $sender->sendMessage(MessageUtil::formatLines($data, "POMOC OGOLNA"));
                break;

            case "gildie":

                $data = [];

                foreach(Settings::$HELP_GUILD_COMMANDS as $commandName => $commandDescription)
                    $data[] = "§8/§e".$commandName." §8- §7".$commandDescription;

                $sender->sendMessage(MessageUtil::formatLines($data, "POMOC GILDIE"));
                break;

            default:
                $sender->sendMessage(MessageUtil::format("Nieznana kategoria pomocy!"));
                break;
        }
    }
}