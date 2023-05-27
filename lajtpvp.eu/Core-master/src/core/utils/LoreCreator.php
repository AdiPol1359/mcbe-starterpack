<?php

declare(strict_types=1);

namespace core\utils;

use pocketmine\utils\TextFormat;

final class LoreCreator {

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

    public function customNameLenghtCalculator() : bool|float {

        $string = preg_replace('/(§([a-z, \d]))/', '', $this->customName);

        $times = strlen($this->getCleanCustomName());

        $explodedText = explode("§l", $string);

        foreach($explodedText as $key => $str) {
            if(str_contains($str, "§r")) {

                $result = substr($str, 0, strpos($str, "§r"));

                if($result === "")
                    continue;

                $times += floor(strlen($result) / 2);
            }
        }

        $times = ($times - strlen(preg_replace('/[^-\[\]=$]/', '', $string))) + (strlen(preg_replace('/[^-\[\]=$]/', '', $string)) * 1.3);

        $times = ($times - strlen(preg_replace('/[^IKLT]/', '', $string))) + (strlen(preg_replace('/[^IKLT]/', '', $string)) * 1.3);

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

            $count = 0;

            $copyString = $string;

            $count += (strlen(preg_replace('![^A-Z]+!', '', $copyString)) / 1.5);
            $count += (strlen(strval(preg_match_all('/[^IKLT]/', '', $copyString))) / 1.5);

            $lines = ($length - strlen($string));

            if($lines > 0 && $lines % 2 === 0 || $lines > 2) {
                if(strlen($string) >= ($length - 2)){
                    $alignText[] = " ".$str;
                    continue;
                }

                while(strlen($string) < ($length - ceil($count))){
                    $str = " ".$str." ";
                    $string = " ".$string." ";
                }

                $alignText[] = $str;
            }else {
                $alignText[] = " " . $str;
            }
        }

        $this->lore = $alignText;
    }
}