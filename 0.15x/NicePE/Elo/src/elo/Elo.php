<?php

namespace elo;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use elo\commands\EloCommand;
use elo\commands\AddEloCommand;
use elo\commands\TopEloCommand;
use elo\commands\SeeEloCommand;
use elo\commands\RemoveEloCommand;
use pocketmine\utils\TextFormat as TF;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use elo\EventListener;
use elo\commands\ResetujRanking;

Class Elo extends PluginBase implements Listener{
 
 CONST prefix = TF::RED.TF::BOLD."Elo ".TF::RESET;
    /** @var Config $eloyaml */
 public $eloyaml;
    /** @var Config $config */
 public $config;

   public function onEnable(){
     // Config start
    if(!file_exists($this->getDataFolder())){
         @mkdir($this->getDataFolder());
      }
             $this->saveDefaultConfig();
               $this->config = $this->getConfig();
              	$this->eloyaml = new Config($this->getDataFolder() ."elo.yml", Config::YAML);
     if(!($this->eloyaml->exists("Steve"))){
    $this->eloyaml->set("Steve", $this->config->get("Starting-Elo"));
    $this->eloyaml->save();
         $this->eloyaml->reload();
}
    // Config end
   // Commands start
   $server = $this->getServer();
    $server->getCommandMap()->register("staty", new EloCommand($this, "staty"));
    $server->getCommandMap()->register("topka", new TopEloCommand($this, "topka"));
    $server->getCommandMap()->register("punkty", new SeeEloCommand($this, "punkty"));
    $server->getCommandMap()->register("dodajpunkty", new AddEloCommand($this, "dodajpunkty"));
    $server->getCommandMap()->register("usunpunkty", new RemoveEloCommand($this, "usunpunkty"));
	$server->getCommandMap()->register("resetujranking", new ResetujRanking($this, "resetujranking"));
  //Commands end
     $server->getPluginManager()->registerEvents(new EventListener($this), $this);
   }

  public function addElo(String $playername, int $elo){
   if($this->eloyaml->exists($playername)){
   $currentelo = $this->eloyaml->get($playername);
   $setelo = $currentelo + $elo;
    $this->eloyaml->set($playername, $setelo);
     $this->eloyaml->save();
       $this->eloyaml->reload();
 }
}

   public function removeElo(String $playername, int $elo){
    if($this->eloyaml->exists($playername)){
     $currentelo = $this->eloyaml->get($playername);
     $setelo = $currentelo - $elo;
      $this->eloyaml->set($playername, $setelo);
        $this->eloyaml->save();
        $this->eloyaml->reload();
   }
}

   public function getElo(String $playername){
    if($this->eloyaml->exists($playername)){
      $elo = $this->eloyaml->get($playername);
        return $elo;
    }
return null;
}

  public function sendTopEloTo($player, int $amount){
   $array = $this->eloyaml->getAll();
    arsort($array);
       $arraykeys = array_keys($array);
       $arrayvalues = array_values($array);
    $player->sendMessage("§l§8)§7===========§8( (§cTOP 10 GRACZY§8) )§7==========§8(");
     for($i = 0; $i < $amount; $i++){
       $player->sendMessage("§8• §c* §7- ".($i + 1.)."§7. Gracz:§c ".$arraykeys[$i]." §7-§c ".$arrayvalues[$i]." §7Punktow");
   }
}

   public function resetElo(String $playername){
    if($this->eloyaml->exists($playername)){
     $this->eloyaml->set($playername, 500);
     $this->eloyaml->save();
        $this->eloyaml->reload();
    }
}

  public function createDataFor(String $playername){
   if(!($this->eloyaml->exists($playername))){
       $this->eloyaml->set($playername, $this->config->get("Starting-Elo"));
   }
  }
}
