<?php

namespace elo\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use elo\Elo;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;

class TopEloCommand extends PluginCommand
{
    private $main;

    public function __construct(Elo $main, $name)
    {
        parent::__construct($name, $main);
        $this->main = $main;
        $this->setPermission("top.command");
    }

    public function execute(CommandSender $sender, $currentAlias, array $args)
    {
        if ($this->testPermission($sender)) {
            $this->main->sendTopEloTo($sender, 10);
    $sender->sendMessage("§l§8)§7===========§8( (§cTOP 10 GRACZY§8) )§7==========§8(");
        }
    }
}
