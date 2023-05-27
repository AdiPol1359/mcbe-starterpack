<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\Main;
use core\utils\MessageUtil;
use core\utils\Settings;
use core\utils\TimeUtil;
use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class APlayerCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("aplayer", "", true, true, ["agracz", "ag", "ap"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function onCommand(CommandSender $sender, array $args) : void {

        $selected = $sender->getName();

        if(isset($args[0]))
            $selected = implode(" ", $args);

        $user = Main::getInstance()->getUserManager()->getUser($selected);

        if(!$user) {
            $sender->sendMessage(MessageUtil::format("Ten gracz nigdy nie gral na tym serwerze!"));
            return;
        }

        $statManager = $user->getStatManager();
        $incognitoManager = $user->getIncognitoManager();

        $lastPlayed = $sender->getServer()->getPlayerByPrefix($user->getName()) ? time() : $statManager->getStat(Settings::$STAT_LAST_JOIN_TIME);
        $timePlayed = (time() - ($sender->getServer()->getPlayerExact($user->getName()) ? $statManager->getStat(Settings::$STAT_LAST_JOIN_TIME) : 0));

        $incognito = !($incognitoName = $incognitoManager->getIncognitoData(Settings::$DATA_NAME)) ? "§cWYLACZONE" : "§aWLACZONE";

        $adress = "NIEZNANE";
        $selectedOS = -1;
        $dataPosition = $sender->getServer()->getOfflinePlayerData($selected)->getTag("Pos")->getValue();

        $position = new Vector3($dataPosition[0]->getValue(), $dataPosition[1]->getValue(), $dataPosition[2]->getValue());

        if(($selectedPlayer = $sender->getServer()->getPlayerByPrefix($selected))) {
            $selectedOS = $selectedPlayer->getPlayerInfo()->getExtraData()["DeviceOS"];
            $position = $selectedPlayer->getPosition();
            $adress = $selectedPlayer->getNetworkSession()->getIp();
        }

        $explodeAddress = explode(".", $adress);
        foreach($explodeAddress as $key => $str) {
            if(($key + 2) < count($explodeAddress))
                $explodeAddress[$key] = str_repeat("X", strlen($str));
        }

        $device = Settings::$DEVICE_IDS[$selectedOS] ?? Settings::$DEVICE_IDS[-1];

        $messages = [
            "§e§lOGOLNE INFORMACJE",
            "§7Status§8: §e".($selectedPlayer ? "§aONLINE" : "§cOFFLINE"),
            "§7Urzadzenie§8: §e".$device,
            "§7Id urzadzenia§8: §e".($selectedPlayer ? $selectedPlayer->getPlayerInfo()->getExtraData()["DeviceId"] : "BRAK DANYCH"),
            "§7Incognito§8: §e".$incognito.($incognitoName ? " §8[§e".$incognitoManager->getIncognitoData(Settings::$DATA_INCOGNITO_NAME)."§8]" : ""),
            "§7Address§8: §e".implode(".", $explodeAddress),
            "§7Pozycja§8: §eX§7/§eY§7/§eZ §8(§e".$position->getFloorX()."§7/§e".$position->getFloorY()."§7/§e".$position->getFloorZ()."§8)",
            "§7Ostatnio widziany: §e".date("d.m.Y H:i:s", $lastPlayed),
            "§7Spedzony czas: §e".TimeUtil::convertIntToStringTime($timePlayed, "§e", "§7", true, false),
            "§e§lEFEKTY GRACZA",
        ];

        if($selectedPlayer) {
            foreach($selectedPlayer->getEffects() as $effectId => $effect) {
                $messages[] = "§7" . str_replace("%POTION.", "", strtoupper($effect->getType()->getName())) . " §e" . $effect->getEffectLevel() . " §8[§e" . TimeUtil::convertIntToStringTime(($effect->getDuration() / 20), "§e", "§7", true, false) . "§8]";
            }
        }else {
            foreach($sender->getServer()->getOfflinePlayerData($selected)->getTag("ActiveEffects") as $key => $compoundTag) {
                $data = $compoundTag->getValue();

                $effect = new EffectInstance(EffectIdMap::getInstance()->fromId($data["Id"]->getValue()), $data["Duration"]->getValue(), $data["Amplifier"]->getValue());
                $messages[] = "§7" . str_replace("%POTION.", "", strtoupper($effect->getType()->getName())) . " §e" . $effect->getEffectLevel() . " §8[§e" . TimeUtil::convertIntToStringTime(($effect->getDuration() / 20), "§e", "§7", true, false) . "§8]";
            }
        }

        if(($ban = Main::getInstance()->getBanManager()->getBanNickInfo($selected))) {
            $messages[] = "§e§lBAN";
            $messages[] = "§7Wygasa: §e".TimeUtil::convertIntToStringTime(($ban->getEndBanTime() - time()), "§e", "§7", true, false);
            $messages[] = "§7Powod: §e".$ban->getReason();
            $messages[] = "§7Nadal: §e".$ban->getAdmin();
        }

        if(($mute = Main::getInstance()->getMuteManager()->getMuteNickInfo($selected))) {
            $messages[] = "§e§lMUTE";
            $messages[] = "§7Wygasa: §e".TimeUtil::convertIntToStringTime(($mute->getEndMuteTime() - time()), "§e", "§7", true, false);
            $messages[] = "§7Powod: §e".$mute->getReason();
            $messages[] = "§7Nadal: §e".$mute->getAdmin();
        }

        $sender->sendMessage(MessageUtil::formatLines($messages, strtoupper($selected)));
    }
}