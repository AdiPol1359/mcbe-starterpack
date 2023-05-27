<?php

declare(strict_types=1);

namespace core\listeners\server;

use core\Main;
use core\network\mcpe\raklib\RakLibInterface;
use pocketmine\event\Listener;
use pocketmine\event\server\NetworkInterfaceRegisterEvent;
use pocketmine\event\server\NetworkInterfaceUnregisterEvent;
use pocketmine\network\mcpe\raklib\RakLibInterface as PMRakLibInterface;
use pocketmine\Server;

class NetworkInterfaceRegisterListener implements Listener {

//    /**
//     * @param NetworkInterfaceRegisterEvent $e
//     * @ignoreCancelled true
//     * @priority LOWEST
//     */
//    public function registerInterface(NetworkInterfaceRegisterEvent $e) : void {
//        if($e->getInterface() instanceof PMRakLibInterface && !$e->getInterface() instanceof RakLibInterface) {
//            var_dump(get_class($e->getInterface()));
//            var_dump("123");
//
//            $server = Server::getInstance();
//            $network = $server->getNetwork();
//
//            $ipV6 = $server->getConfigGroup()->getConfigBool("enable-ipv6", true);
//            $ip = $ipV6 ? $server->getIpV6() : $server->getIp();
//
//            $network->unregisterInterface($e->getInterface());
//            $network->registerInterface(new RakLibInterface($server, $ip, $server->getPort(), $ipV6));
//            Main::getInstance()->getLogger()->info("zarejestrowano");
//    }
}