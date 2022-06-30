<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\Main;
use core\manager\managers\EventManager;
use core\manager\managers\SoundManager;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;

class EventCommand extends BaseCommand{
    public function __construct() {
        parent::__construct("event", "Event Command", true, false, "Komenda event sluzy do prostego zarzadzania eventami na serwerze", ["eventy"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        if(empty($args)){
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["rtp", "alert", "tpall", "reload"], ["ilosc"]]));
            return;
        }

        switch($args[0]){
            case "rtp":

                $players = [];

                foreach(EventManager::getEventPlayers() as $p) {
                    if($p === $player->getName())
                        continue;
                    $players[$p] = 1;
                }

                if(empty($players)){
                    $player->sendMessage(MessageUtil::format("Na serwerze nie ma innych graczy!"));
                    return;
                }

                $randomPlayer = $player->getServer()->getPlayerExact(($name = array_rand($players, 1)));

                if(!$randomPlayer){
                    $player->sendMessage(MessageUtil::format("Wylosowany gracz byl offline! §l§9".$name));
                    return;
                }

                $randomPlayer->teleport($player->asPosition());
                SoundManager::addSound($randomPlayer, $randomPlayer->asVector3(), "random.explode", 1);
                $player->sendMessage(MessageUtil::format("Przeteleportowano gracza o nicku: §9§l" . $randomPlayer->getName()));

                break;
            case "alert":
                foreach(EventManager::getEventPlayers() as $p) {
                    $p = $this->getServer()->getPlayerExact($p);

                    if(!$p)
                        continue;

                    $p->sendMessage("§l§9EVENT DO KTOREGO SIE ZGLOSILES WLASNIE SIE ROZPOCZAL!");
                    SoundManager::addSound($p, $p->asVector3(), "random.explode", 1);
                }
                break;
            case "tpall":

                foreach(EventManager::getEventPlayers() as $p) {
                    $p = $this->getServer()->getPlayerExact($p);

                    if(!$p)
                        continue;

                    $p->teleport($player->asPosition());
                    $p->sendMessage("§l§9ZOSTALES PRZETELEPORTOWANY NA EVENT!");
                }

                SoundManager::addSound($player, $player->asVector3(), "random.explode", 1);
                $player->sendMessage(MessageUtil::format("Poprawnie przeteleportowano wszystkich uczestnikow eventu!"));

                break;
            case "reload":

                if(is_file(Main::getInstance()->getDataFolder()."/data/event.json"))
                    Main::getInstance()->saveResource("data/event.json");

                Main::setEvent(new Config(Main::getInstance()->getDataFolder()."data/event.json"));
                $player->sendMessage(MessageUtil::format("Config zostal odswiezony!"));
                break;

            default:
                break;
        }
    }
}