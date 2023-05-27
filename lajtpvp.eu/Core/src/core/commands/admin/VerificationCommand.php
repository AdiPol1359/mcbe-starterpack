<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class VerificationCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("sprawdzanie", "", true, false,["spr"]);

        $parameters = [
            0 => [
                $this->commandParameter("verifyPlayer", AvailableCommandsPacket::ARG_TYPE_STRING, false, "verifyPlayer", ["sprawdz", "czysty"]),
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $nick = $sender->getName();

        $argsCommand = ["czysty", "sprawdz"];

        if(empty($args) || !in_array($args[0], $argsCommand)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["sprawdz", "czysty"], ["nick"]]));
            return;
        }

        isset($args[1]) ? $targetName = $args[1] : $targetName = "";
        is_null($sender->getServer()->getPlayerByPrefix($targetName)) ? $target = null : $target = $sender->getServer()->getPlayerByPrefix($targetName);

        if($target === null && isset($args[1])) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest §eOFFLINE"));
            return;
        }

        if($args[0] == "sprawdz") {

            if(isset(Settings::$VERIFY[$targetName])) {
                $sender->sendMessage(MessageUtil::format("Sprawdzasz juz ta osobe!"));
                return;
            }

            if(!PermissionUtil::has($sender, Settings::$PERMISSION_TAG."sprawdzanie")) {
                $sender->sendMessage(MessageUtil::format("Nie mozesz sprawdzic tej osoby poniewaz jest administratorem!"));
                return;
            }

            if($targetName === $nick) {
                $sender->sendMessage(MessageUtil::format("Nie mozesz sie sprawdzic!"));
                return;
            }



            Settings::$VERIFY[$targetName] = true;

            foreach($sender->getServer()->getOnlinePlayers() as $onlinePlayer) {
                if($onlinePlayer->getWorld()->getDisplayName() !== Settings::$LOBBY_WORLD)
                    $onlinePlayer->sendMessage(MessageUtil::formatLines(["Gracz o nicku §e" . $targetName, "§r§7Zostal wezwany do sprawdzania", "Przez administratora §e".$sender->getName()], "SPRAWDZANIE"));
            }

            $x = Settings::VERIFICATION_COORDINATES["x"];
            $y = Settings::VERIFICATION_COORDINATES["y"];
            $z = Settings::VERIFICATION_COORDINATES["z"];

            $sender->teleport(new Vector3($x, $y, $z));
            $target->teleport(new Vector3($x, $y, $z));
            $target->sendMessage(MessageUtil::format("Jestes Sprawdzany mozesz korzystac tylko z: §e/r§7, §e/msg§7!"));
        }

        if($args[0] == "czysty") {

            if(!isset(Settings::$VERIFY[$targetName])) {
                $sender->sendMessage(MessageUtil::format("Ten gracz nie jest sprawdzany!"));
                return;
            }

            unset(Settings::$VERIFY[$targetName]);

            $level = $sender->getWorld();

            $sender->teleport($level->getSafeSpawn());
            $target->teleport($level->getSafeSpawn());

            foreach($sender->getServer()->getOnlinePlayers() as $onlinePlayer) {
                if($onlinePlayer->getWorld()->getDisplayName() !== Settings::$LOBBY_WORLD)
                    $onlinePlayer->sendMessage(MessageUtil::formatLines(["Gracz o nicku §e" . $targetName . " §7okazal sie byc czysty"], "SPRAWDZANIE"));
            }

        }
    }
}
