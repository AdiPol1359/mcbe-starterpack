<?php

namespace NicePE_Core;

use pocketmine\utils\TextFormat as TF;

class BlockCmdFormat{

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function translate($m){
        $m = str_replace("{BLACK}", TF::BLACK, $m);
        $m = str_replace("{DARK_BLUE}", TF::DARK_BLUE, $m);
        $m = str_replace("{DARK_GREEN}", TF::DARK_GREEN, $m);
        $m = str_replace("{DARK_AQUA}", TF::DARK_AQUA, $m);
        $m = str_replace("{DARK_RED}", TF::DARK_RED, $m);
        $m = str_replace("{DARK_GRAY}", TF::DARK_GRAY, $m);
        $m = str_replace("{DARK_PURPLE}", TF::DARK_PURPLE, $m);
        $m = str_replace("{LIGHT_PURPLE}", TF::LIGHT_PURPLE, $m);
        $m = str_replace("{GRAY}", TF::GRAY, $m);
        $m = str_replace("{GOLD}", TF::GOLD, $m);
        $m = str_replace("{BLUE}", TF::BLUE, $m);
        $m = str_replace("{GREEN}", TF::GREEN, $m);
        $m = str_replace("{AQUA}", TF::AQUA, $m);
        $m = str_replace("{RED}", TF::RED, $m);
        $m = str_replace("{YELLOW}", TF::YELLOW, $m);
        $m = str_replace("{WHITE}", TF::WHITE, $m);
        $m = str_replace("{OBFUSCATED}", TF::OBFUSCATED, $m);
        $m = str_replace("{BOLD}", TF::BOLD, $m);
        $m = str_replace("{STRIKETHROUGH}", TF::STRIKETHROUGH, $m);
        $m = str_replace("{UNDERLINE}", TF::UNDERLINE, $m);
        $m = str_replace("{ITALIC}", TF::ITALIC, $m);
        $m = str_replace("{RESET}", TF::RESET, $m);
        return $m;
    }

}