<?php

namespace obsydianiarka;

use pocketmine\block\Block;
use pocketmine\block\Stone;
use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;

class Task extends PluginTask{

    private $z;
    private $y;
    private $x;
	private $gracz;
    
    public function  __construct($gracz, $x, $y, $z){
	parent::__construct($gracz);
    $this->x = $x;
	$this->y = $y;
	$this->z = $z;
	$this->gracz = $gracz;
    }
	
 public function onRun($currentTick){
		 $level = $this->gracz->getServer()->getLevelByName("world");
	 if($level->getBlock(new Vector3($this->x, $this->y-1, $this->z))->getId() == 121) { 
	 if($level->getBlock(new Vector3($this->x, $this->y, $this->z))->getId() == 0) { 
       $level->setBlock(new Vector3($this->x, $this->y, $this->z), new Stone());
    }
}
 }
}

