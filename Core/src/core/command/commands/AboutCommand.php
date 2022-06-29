<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\plugin\Plugin;

class AboutCommand extends BaseCommand{
    public function __construct(){
        parent::__construct("about", "About Command", false, true, "Komenda about sluzy do wyswietlania informacji o pluginach czy silniku serwera", ["version", "ver"]);

        $parameters = [
            0 => [
                $this->commandParameter("aboutPluginOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "aboutPlugins", $this->getPlugins())
            ]
        ];

        $this->setOverLoads($parameters);
    }
    public function onCommand(CommandSender $player, array $args) : void {
        if(count($args) === 0)
            $player->sendMessage(MessageUtil::formatLines(["§r§7Nazwa: §l§9".$this->getServer()->getName(), "§r§7Wersja silnika: §l§9".$this->getServer()->getPocketMineVersion(), "§r§7Wersja minecrafta: §l§9".$this->getServer()->getVersion(), "§r§7Protokol: §l§9".ProtocolInfo::CURRENT_PROTOCOL]));
        else{
            $pluginName = implode(" ", $args);
            $exactPlugin = $this->getServer()->getPluginManager()->getPlugin($pluginName);

            if($exactPlugin instanceof Plugin){
                $desc = $exactPlugin->getDescription();
                $message = ["§r§7Nazwa: §l§9".$desc->getName(), "§r§7Wersja: §r§7".$desc->getVersion()];

                if($desc->getDescription() !== "")
                    $message[] = "§r§7Opis: §l§9".$desc->getDescription();

                if($desc->getWebsite() !== "")
                    $message[] = "§r§7Strona: §l§9".$desc->getWebsite();

                if(count($authors = $desc->getAuthors()) > 0){
                    if(count($authors) === 1)
                        $message[] = "§r§7Autor: §l§9" . implode(", ", $authors);
                    else
                        $message[] = "§r§7Autorzy: §l§9" . implode(", ", $authors);
                }

                $player->sendMessage(MessageUtil::formatLines($message));
                return;
            }

            $found = false;
            $pluginName = strtolower($pluginName);
            foreach($this->getServer()->getPluginManager()->getPlugins() as $plugin){
                if(stripos($plugin->getName(), $pluginName) !== false){
                    $desc = $plugin->getDescription();
                    $message = ["§r§7Nazwa: §l§9".$desc->getName(), "§l§9Wersja: §r§7".$desc->getVersion()];

                    if($desc->getDescription() !== "")
                        $message[] = "§r§7Opis: §l§9".$desc->getDescription();

                    if($desc->getWebsite() !== "")
                        $message[] = "§r§7Strona: §l§9".$desc->getWebsite();

                    if(count($authors = $desc->getAuthors()) > 0){
                        if(count($authors) === 1)
                            $message[] = "§r§7Autor: §l§9" . implode(", ", $authors);
                        else
                            $message[] = "§r§7Autorzy: §l§9" . implode(", ", $authors);
                    }

                    $player->sendMessage(MessageUtil::formatLines($message));
                    $found = true;
                }
            }

            if(!$found)
                $player->sendMessage(MessageUtil::format("Na tym serwerze nie znaleziono takiego pluginu!"));
        }
    }

    public function getPlugins() : array {
        $plugins = [];

        foreach($this->getServer()->getPluginManager()->getPlugins() as $plugin)
            $plugins[] = strtolower($plugin->getName());

        return $plugins;
    }
}