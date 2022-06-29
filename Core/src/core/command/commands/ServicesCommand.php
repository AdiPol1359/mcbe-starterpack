<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\form\forms\services\ServicesForm;
use core\manager\managers\ServerManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Player;

class ServicesCommand extends BaseCommand{
    public function __construct() {
        parent::__construct("service", "Services Command", false, true, "Komenda service sluzy do zarzadzania kupionymi rzeczami ze sklepu", ["uslugi", "daj", "is", "itemshop"]);

        $parameters = [
            0 => [
                $this->commandParameter("servicesOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "servicesOptions", ["vip", "svip", "sponsor", "case20", "case45", "case100", "yt"]),
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(empty($args) || !$player->hasPermission(ConfigUtil::PERMISSION_TAG."service")){
            if($player instanceof Player)
                $player->sendForm(new ServicesForm($player));
            return;
        }

        if(!isset($args[1]) || !isset($args[0])){
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["vip", "svip", "sponsor", "case20", "case45", "case100"], ["nick"]]));
            return;
        }

        $argument = $args[0];
        $message = null;

        array_shift($args);
        $user = UserManager::getUser(implode(" ", $args));

        if(!$user){
            $player->sendMessage(MessageUtil::format("Gracz o takim nicku nie istnieje!"));
            return;
        }

        switch($argument){
            case "vip":
                $message = MessageUtil::formatLines(["Gracz o nicku §l§9".$user->getName(), "Zakupil wlasnie §l§9VIPA §r§7na §l§930§r§7dni", "Zakupu dokonal ze strony: §l§9www.DarkMoonPE.PL"]);
                $user->addService(1);
                break;
            case "svip":
                $message = MessageUtil::formatLines(["Gracz o nicku §l§9".$user->getName(), "Zakupil wlasnie §l§9SVIPA §r§7na §l§930§r§7dni", "Zakupu dokonal ze strony: §l§9www.DarkMoonPE.PL"]);
                $user->addService(2);
                break;
            case "sponsor":
                $message = MessageUtil::formatLines(["Gracz o nicku §l§9".$user->getName(), "Zakupil wlasnie §l§9SPONSORA §r§7na §l§930§r§7dni", "Zakupu dokonal ze strony: §l§9www.DarkMoonPE.PL"]);
                $user->addService(3);
                break;
            case "case20":
                $message = MessageUtil::formatLines(["Gracz o nicku §l§9".$user->getName(), "Zakupil wlasnie §l§9MagicCase §r§7w ilosci §l§920", "Zakupu dokonal ze strony: §l§9www.DarkMoonPE.PL"]);
                $user->addService(4);
                break;
            case "case45":
                $message = MessageUtil::formatLines(["Gracz o nicku §l§9".$user->getName(), "Zakupil wlasnie §l§9MagicCase §r§7w ilosci §l§945", "Zakupu dokonal ze strony: §l§9www.DarkMoonPE.PL"]);
                $user->addService(5);
                break;
            case "case100":
                $message = MessageUtil::formatLines(["Gracz o nicku §l§9".$user->getName(), "Zakupil wlasnie §l§9MagicCase §r§7w ilosci §l§9100", "Zakupu dokonal ze strony: §l§9www.DarkMoonPE.PL"]);
                $user->addService(6);
                break;
            case "yt":
                $message = MessageUtil::formatLines(["Gracz o nicku §l§9".$user->getName(), "Odebral wlasnie range §l§9YOUTUBER", "Odebral ja ze strony: §l§9www.DarkMoonPE.PL/youtube"]);
                $user->addService(7);
                break;

            default:
                $player->sendMessage(MessageUtil::format("Nieznana usluga!"));
                return;
        }

        if($message !== null) {
            foreach($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
                if($onlinePlayer->getLevel()->getName() !== ConfigUtil::LOBBY_WORLD)
                    $onlinePlayer->sendMessage($message);
            }
        }

        $player->sendMessage(MessageUtil::format("Poprawnie nadano usluge!"));
    }
}