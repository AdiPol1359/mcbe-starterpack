<?php

namespace mcban;

use pocketmine\event\Listener;

use pocketmine\Server;

use pocketmine\Player;

use pocketmine\utils\TextFormat as color;

use pocketmine\command\Command;

use pocketmine\command\CommandSender;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{

    public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		
		$this->getLogger()->info("§emcban v1.0 §aactiv!");
	}
    
    public function onDisable(){
		$this->getLogger()->info("§emcbab §4not activ!");
    }

public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        switch($command->getName()){
            case "b":
                if(isset($args[0])){
                        $player = $this->getServer()->getPlayer($args[0]);
                        if($this->getServer()->getPlayer($args[0])){
            $reason = implode(" ", $args);
            $worte = explode(" ", $reason);
			 unset($worte[0]);
			  $reason = implode(" ", $worte);
 $sender->getServer()->getCIDBans()->addBan($player->getClientId(), $reason, null, $sender->getName());                     
                            $player->kick("§8======\n§4 Zostales zbanowany na serwerze, §4\nPowod:[" .$reason ."]\n§4YT-XFanta 1337 « Wejdz jezeli chcesz\nSie odwolac od bana.\n§8======");
          $this->getServer()->broadcastMessage("§c» §8[§cBAN§8] §c" .$args[0] ." §aZostal zbanowany na serwerze, powod: §c[" .$reason ."] §c«");
             } else {
      if(!$player instanceof Player){
           $sender->sendMessage("§Gracz jest offline!");
                     return true;
                         }
      if($sender instanceof Player) {
               if($sender->hasPermission("mcban.command")) {
                   } else {
           $sender->sendMessage("§cNie masz permisji!");
           return true;
                 }
              }
          }
       }
     }
  }
  }
 
     
   
