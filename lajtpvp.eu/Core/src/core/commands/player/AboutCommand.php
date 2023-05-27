<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class AboutCommand extends BaseCommand{
    
    public function __construct(){
        parent::__construct("about", "", false, true, ["version", "ver"], [
            0 => [
                $this->commandParameter("aboutPluginOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "aboutPlugins", $this->getPlugins())
            ]
        ]);
    }
    
    public function onCommand(CommandSender $sender, array $args) : void {
        if(count($args) === 0)
            $sender->sendMessage(MessageUtil::formatLines(["§r§7Nazwa silnika: §e".$sender->getServer()->getName(), "§r§7Wersja silnika: §e".$sender->getServer()->getPocketMineVersion(), "§r§7Wersja minecrafta: §e".$sender->getServer()->getVersion(), "§r§7Protokol: §e".ProtocolInfo::CURRENT_PROTOCOL]));
        else{
            $pluginName = implode(" ", $args);
            $exactPlugin = $sender->getServer()->getPluginManager()->getPlugin($pluginName);

            if($exactPlugin instanceof Plugin){
                $desc = $exactPlugin->getDescription();
                $message = ["§r§7Nazwa: §e".$desc->getName(), "§r§7Wersja: §r§e".$desc->getVersion()];

                if($desc->getDescription() !== "") {
                    $message[] = "§r§7Opis: §e" . $desc->getDescription();
                }

                if($desc->getWebsite() !== "") {
                    $message[] = "§r§7Strona: §e" . $desc->getWebsite();
                }

                if(count($authors = $desc->getAuthors()) > 0){
                    if(count($authors) === 1)
                        $message[] = "§r§7Autor: §e" . implode(", ", $authors);
                    else
                        $message[] = "§r§7Autorzy: §e" . implode(", ", $authors);
                }

                $sender->sendMessage(MessageUtil::formatLines($message));
                return;
            }

            $found = false;
            $pluginName = strtolower($pluginName);
            foreach($sender->getServer()->getPluginManager()->getPlugins() as $plugin){
                if(stripos($plugin->getName(), $pluginName) !== false){
                    $desc = $plugin->getDescription();
                    $message = ["§r§7Nazwa: §e".$desc->getName(), "§eWersja: §r§e".$desc->getVersion()];

                    if($desc->getDescription() !== "")
                        $message[] = "§r§7Opis: §e".$desc->getDescription();

                    if($desc->getWebsite() !== "")
                        $message[] = "§r§7Strona: §e".$desc->getWebsite();

                    if(count($authors = $desc->getAuthors()) > 0){
                        if(count($authors) === 1)
                            $message[] = "§r§7Autor: §e" . implode(", ", $authors);
                        else
                            $message[] = "§r§7Autorzy: §e" . implode(", ", $authors);
                    }

                    $sender->sendMessage(MessageUtil::formatLines($message));
                    $found = true;
                }
            }

            if(!$found)
                $sender->sendMessage(MessageUtil::format("Na tym serwerze nie znaleziono takiego pluginu!"));
        }
    }

    public function getPlugins() : array {
        $plugins = [];

        foreach(Server::getInstance()->getPluginManager()->getPlugins() as $plugin)
            $plugins[] = strtolower($plugin->getName());

        return $plugins;
    }
}