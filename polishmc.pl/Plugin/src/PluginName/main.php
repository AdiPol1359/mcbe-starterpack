<?php

namespace boniarski\WildTP;

use pocketmine\plugin\PluginBase;
use pocketmine\command\{Command,CommandSender};
use pocketmine\level\{Level,Position};
use pocketmine\math\Vector3;
use pocketmine\{Server,Player};
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase {
    
    public function onEnable(){
        $this->getLogger()->info(C::GREEN."Enabled!");
    }
    
    public function onCommand(CommandSender $s, Command $cmd, $label, array $args){
        if(strtolower($cmd->getName() == "losowe")){
            $x = rand(1,2000);
            $y = rand(75,82);
            $z = rand(1,2000);
            $s->teleport(new Position($x,$y,$z));
            $s->sendMessage(C::GREEN."§8• §8(§aSVTP§8) §7Teleportowanie w losowe miejsce: X: §a$x §7Y: §a$y §7Z: §a$z •");
        }
        return true;
    }
}
