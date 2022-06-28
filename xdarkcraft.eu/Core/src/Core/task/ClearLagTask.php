<?php

namespace Core\task;

use pocketmine\scheduler\Task;

use pocketmine\Server;

use Core\Main;

class ClearLagTask extends Task {

	private $time;
	private $d_time;

	public function __construct(int $time){
		$this->time = $time + 1;
		$this->d_time = $time + 1;
	}

	public function onRun($tick){
		$this->time--;

		if($this->time == 30)
			$this->sendMessage("§4§lPolishHard§7.EU §r§8§l>§r §7Itemy zostana usuniete za §430 §7sekund!");

		if($this->time == 15)
			$this->sendMessage("§4§lPolishHard§7.EU §r§8§l>§r §7Itemy zostana usuniete za §415 §7sekund!");

		if($this->time <= 5 && $this->time > 0)
			$this->sendMessage("§4§lPolishHard§7.EU §r§8§l>§r §7Itemy zostana usuniete za §4{$this->time} §7sekund!");

		if($this->time <= 0){
			$this->time = $this->d_time;

			Main::getInstance()->clearLag();
		}
	}
	
	private function sendMessage(string $msg) : void {
		foreach(Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
		 $p->sendMessage($msg);
	}
}