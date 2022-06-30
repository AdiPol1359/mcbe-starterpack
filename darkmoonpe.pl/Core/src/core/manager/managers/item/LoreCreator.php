<?php

namespace core\manager\managers\item;

use pocketmine\utils\TextFormat;

class LoreCreator {

    private string $defaultCustomName;
    private array $defaultLore;

    private string $customName;
    private array $lore;

    public function __construct(string $customName = "", array $lore = []) {
        $this->customName = $customName;
        $this->lore = $lore;
    }

    public function getCustomName() : string {
        return $this->customName;
    }

    public function getLore() : array {
        return $this->lore;
    }

    public function setCustomName(string $customName, bool $default = false) : void {
        $this->customName = $customName;

        if($default)
            $this->defaultCustomName = $customName;
    }

    public function setLore(array $lore, bool $default = false) : void {
        $this->lore = $lore;

        if($default)
            $this->defaultLore = $lore;
    }

    public function resetCustomName() : void {
        $this->customName = $this->defaultCustomName;
    }

    public function resetLore() : void {
        $this->lore = $this->defaultLore;
    }

    public function getCleanCustomName() : string {
        return TextFormat::clean($this->customName);
    }

    public function getCleanLore() : array {
        $cleanText = [];

        foreach($this->lore as $lore)
            $cleanText[] = TextFormat::clean($lore);

        return $cleanText;
    }

    public function customNameLenghtCalculator() : int {

        $string = preg_replace('/(§([a-z, \d]))/', '', $this->customName);

        $times = strlen($this->getCleanCustomName());

        $loopStr = str_split($this->getCleanCustomName());

        foreach($loopStr as $key => $value){
            if(ctype_upper($value))
                $times += 0.25;
        }

        $explodedText = explode("§l", $string);

        foreach($explodedText as $key => $str) {
            if(strpos($str, "§r") !== false) {

                $result = substr($str, 0, strpos($str, "§r"));

                if($result === "")
                    continue;

                $times += floor(strlen($result) / 3);
            }
        }

        return ceil($times);
    }

    public function alignLore() : void {

        $alignText = [];

        $length = $this->customNameLenghtCalculator();

        foreach($this->lore as $str){

            $string = TextFormat::clean($str);

            if($string === ""){
                $alignText[] = " ";
                continue;
            }

            $lines = ($length - strlen($string));

            if($lines > 0 && $lines % 2 === 0 || $lines > 2) {
                if(strlen($string) >= ($length - 2)){
                    $alignText[] = " ".$str;
                    continue;
                }

                while(strlen($string) < ($length + (($lines / 2) - 4))){
                    $str = " ".$str." ";
                    $string = " ".$string." ";
                }

                $alignText[] = $str;
            }else
                $alignText[] = " ".$str;
        }

        $this->lore = $alignText;
    }

    public function alignCustomName(int $lines) : void {
        $len = strlen($this->customName);

        if($lines <= $len)
            return;

        $times = $lines - $len;
        $this->customName = str_replace("---", "---".str_repeat("-", ($times / 2)), $this->customName);

        if(strlen($this->customName) < $lines)
            $this->customName = str_replace("===", "===".str_repeat("=", ($times / 2)), $this->customName);
    }

    public function reset() : void {
        $this->customName = "";
        $this->lore = [];
    }
}