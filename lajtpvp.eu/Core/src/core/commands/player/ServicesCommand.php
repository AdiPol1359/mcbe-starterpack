<?php

namespace core\commands\player;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\ServiceInventory;
use core\Main;
use core\managers\AdminManager;
use core\managers\ServerManager;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class ServicesCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("services", "", false, true, ["uslugi", "is", "itemshop", "service"]);
        
        $parameters = [
            0 => [
                $this->commandParameter("servicesOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "servicesOptions", Main::getInstance()->getServicesManager()->getCommandNames()),
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        
        if(!Main::getInstance()->getServerManager()->isSettingEnabled(ServerManager::ITEMSHOP) && !PermissionUtil::has($sender, Settings::$PERMISSION_TAG."services")) {
            $sender->sendMessage(MessageUtil::format("Itemshop jest aktualnie wylaczony!"));
            return;
        }

        if($sender instanceof Player) {
            if(empty($args) || !PermissionUtil::has($sender, Settings::$PERMISSION_TAG."services")) {
                $user = Main::getInstance()->getUserManager()->getUser($sender->getName());

                if(!$user->getServicesManager()->hasServiceToCollect()) {
                    $sender->sendMessage(MessageUtil::format("Nie masz zadnych uslug do odebrania!"));
                    return;
                }

                (new ServiceInventory($sender))->openFor([$sender]);
                return;
            }
        }

        if(!isset($args[1]) || !isset($args[0])) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [Main::getInstance()->getServicesManager()->getCommandNames(), ["nick"]]));
            return;
        }

        $argument = $args[0];

        array_shift($args);
        $user = Main::getInstance()->getUserManager()->getUser(implode(" ", $args));

        if(!$user) {
            $sender->sendMessage(MessageUtil::format("Gracz o takim nicku nie istnieje!"));
            return;
        }

        if(($service = Main::getInstance()->getServicesManager()->getServiceByCommandName($argument)) === null) {
            $sender->sendMessage(MessageUtil::format("Usluga pod ta nazwa nie istnieje!"));
            return;
        }

        $user->getServicesManager()->addService($service->getId());

        if(($selectedPlayer = $sender->getServer()->getPlayerExact($user->getName())) !== null)
            $selectedPlayer->sendMessage(MessageUtil::format("Masz nowa usluge pod §8/§eitemshop"));

        $sender->sendMessage(MessageUtil::format("Poprawnie nadano usluge!"));
        AdminManager::sendMessage($sender, $sender->getName()." nadal usluge ".$service->getName()." graczowi ".$user->getName());
    }
}