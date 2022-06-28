<?php
namespace NicePE_Core;

use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;
use pocketmine\Player;
class allowtalk extends PluginTask{
	public $player;
	public function __construct(Plugin $owner, Player $player){
		parent::__construct($owner);
		$this->player = $player;
	}
	
	public function onRun($currentTick){
		if ($this->player instanceof Player){
			if (in_array($this->player->getName(), $this->getOwner()->talked)){
				$id = array_search($this->player->getName(), $this->getOwner()->talked);
				unset($this->getOwner()->talked[$id]);
			}
			
		}
	}
}