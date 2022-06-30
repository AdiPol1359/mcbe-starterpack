<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

use core\Main;
use pocketmine\math\Vector3;
use core\manager\managers\BanManager;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class VerificationCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("sprawdzanie", "Sprawdzanie Command", true, false, "Komenda sprawdzanie sluzy do sprawdzania gracza", ["spr"]);

        $parameters = [
            0 => [
                $this->commandParameter("verifyPlayer", AvailableCommandsPacket::ARG_TYPE_STRING, false, "verifyPlayer", ["sprawdz", "czysty", "ban"]),
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ],

            1 => [
                $this->commandParameter("verifyOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "verifyOptions", ["ustaw"]),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        $nick = $player->getName();

        $argsCommand = ["czysty", "sprawdz", "ban", "ustaw"];

        if(empty($args) || !in_array($args[0], $argsCommand)) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["sprawdz", "czysty", "ban", "ustaw"], ["nick"]]));
            return;
        }

        isset($args[1]) ? $targetName = $args[1] : $targetName = "";
        is_null($this->getServer()->getPlayer($targetName)) ? $target = null : $target = $this->getServer()->getPlayer($targetName);

        if($target === null && isset($args[1])) {
            $player->sendMessage(MessageUtil::format("Ten gracz jest §l§9OFFLINE"));
            return;
        }

        if($args[0] == "sprawdz") {

            if(isset(Main::$sprawdzanie[$targetName])) {

                $player->sendMessage(MessageUtil::format("Sprawdzasz juz ta osobe!"));
                return;

            }

            if($player->hasPermission(ConfigUtil::PERMISSION_TAG . "sprawdzanie")) {

                $player->sendMessage(MessageUtil::format("Nie mozesz sprawdzic tej osoby poniewaz jest §9administratorem!"));
                return;

            }

            if($targetName == $nick) {

                $player->sendMessage(MessageUtil::format("Nie mozesz sie sprawdzic!"));
                return;

            }

            Main::$sprawdzanie[$targetName] = true;

            foreach($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
                if($onlinePlayer->getLevel()->getName() !== ConfigUtil::LOBBY_WORLD)
                    $onlinePlayer->sendMessage(MessageUtil::formatLines(["Gracz o nicku §l§9" . $targetName . "\n" . "§r§7Jest sprawdzany"]));
            }

            $array = Main::getCfg()->getNested("Kordy");

            if($array == null) return;

            $x = $array["x"];
            $y = $array["y"];
            $z = $array["z"];

            $player->teleport(new Vector3($x, $y, $z));
            $target->teleport(new Vector3($x, $y, $z));
            $target->sendMessage(MessageUtil::format("§9Jestes Sprawdzany§7, mozesz korzystac tylko z: §9/r§7, §9/msg§7!"));
        }

        if($args[0] == "czysty") {

            if(!isset(Main::$sprawdzanie[$targetName])) {

                $player->sendMessage(MessageUtil::format("Ten gracz nie jest sprawdzany!"));
                return;

            }

            unset(Main::$sprawdzanie[$targetName]);

            $level = $player->getLevel();

            $player->teleport($level->getSafeSpawn());
            $target->teleport($level->getSafeSpawn());

            foreach($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
                if($onlinePlayer->getLevel()->getName() !== ConfigUtil::LOBBY_WORLD)
                    $onlinePlayer->sendMessage(MessageUtil::formatLines(["Gracz o nicku §l§9" . $targetName . "\n" . "§r§7Okazal sie byc czysty"]));
            }

        }

        if($args[0] == "ban" && isset($args[1])) {

            if(!isset(Main::$sprawdzanie[$targetName])) {
                $player->sendMessage(MessageUtil::format("Ten gracz nie jest sprawdzany!"));
                return;
            }

            unset(Main::$sprawdzanie[$targetName]);

            $level = $player->getLevel();

            $player->teleport($level->getSafeSpawn());

            $p = $this->getServer()->getPlayer($args[1]);

            $pname = $p->getName();

            if($p && !$p == null) {

                $czas = 432000;

                BanManager::setBan($pname, $player->getName(), $czas, " cheaty");

                $x = mt_rand(1, 1000);
                $y = 9999;
                $z = mt_rand(1, 1000);

                $p->teleport(new Vector3($x, $y, $z));

                foreach($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
                    if($onlinePlayer->getLevel()->getName() !== ConfigUtil::LOBBY_WORLD)
                        $onlinePlayer->sendMessage(MessageUtil::formatLines(["Gracz o nicku §l§9" . $targetName . "\n" . "§r§7Zostal zbanowany za cheaty na 7 dni"]));
                }

            } else
                $player->sendMessage(MessageUtil::format("Ten gracz jest §9offline§7!"));
        }

        if($args[0] == "ustaw" && !isset($args[1])) {

            if($player->isOp()) {

                $x = $player->getX();
                $y = $player->getY();
                $z = $player->getZ();

                Main::getCfg()->set("Kordy", [
                    "x" => $x,
                    "y" => $y,
                    "z" => $z
                ]);

                Main::getCfg()->save();

                $player->sendMessage(MessageUtil::format("§9Poprawnie §7ustawiono kordy sprawdzarki!"));
            } else
                $player->sendMessage(MessageUtil::format("Tylko §9administrator §7z op'em moze ustawic sprawdzarke!"));
        }
    }
}