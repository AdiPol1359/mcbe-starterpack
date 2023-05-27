<?php

declare(strict_types=1);

namespace core\managers;

use core\utils\MessageUtil;
use core\utils\Settings;
use pocketmine\player\Player;

class CpsManager {

    private array $data = [];
    public array $blockAttack = [];

    public function setData(Player $player, int $clicks, $lastClick, int $seconds) : void{
        $this->data[$player->getName()] = [$clicks, $lastClick, $seconds];
    }

    public function setDefaultData(Player $player) : void{
        $this->setData($player, 0, time(), 0);
    }

    public function Click(Player $player) : void{

        $nick = $player->getName();
        $lastClick = time() - $this->getLastClick($player);
        $time = $this->getSeconds($player);

        if($lastClick <= 0) {
            $this->data[$nick][0] += 1;
            $this->data[$nick][1] = time();

            if ($time < 1)
                $this->data[$nick][2] += 1;

        }elseif($lastClick == 1){
            $this->data[$nick][0] += 1;
            $this->data[$nick][1] = time();
            $this->data[$nick][2] += 1;
        }else
            $this->setData($player, 1, time(), 1);

        $cpsCount = $this->getCps($player);

        if(!isset($this->blockAttack[$nick]) && $cpsCount > Settings::$CPS_LIMIT)
            $this->blockAttack[$nick] = time();

        if(isset($this->blockAttack[$nick])){
            $cooldown = Settings::$CPS_COOL_DOWN - (time() - $this->blockAttack[$player->getName()]);
            if($cooldown <= 0){
                unset($this->blockAttack[$nick]);
                $this->setDefaultData($player);
                return;
            }
            $seconds = Settings::$CPS_COOL_DOWN - (time() - $this->blockAttack[$nick]);
            $player->sendMessage(MessageUtil::format("Przekroczyles limit cps musisz odczekac §e".$seconds."§r§7 sekund!"));
        }
    }

    public function getClicks(Player $player) : int{
        if(!isset($this->data[$player->getName()]))
            $this->setDefaultData($player);
        return $this->data[$player->getName()][0];
    }

    public function getSeconds(Player $player) : int{
        if(!isset($this->data[$player->getName()]))
            $this->setDefaultData($player);
        return $this->data[$player->getName()][2];
    }

    public function getLastClick(Player $player){
        if(!isset($this->data[$player->getName()]))
            $this->setDefaultData($player);
        return $this->data[$player->getName()][1];
    }

    public function getCPS(Player $player) : int{
        if(!isset($this->data[$player->getName()]))
            $this->setDefaultData($player);

        $lastClick = time() - $this->getLastClick($player);
        $clicks = $this->getClicks($player);
        $time = $this->getSeconds($player);

        if($lastClick >= 1)
            return 0;

        if($clicks == 0)
            return 0;

        return (int) round($clicks / $time);
    }
}