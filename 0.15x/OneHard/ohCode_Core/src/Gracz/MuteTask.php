<?php
namespace Gracz;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
class MuteTask extends PluginTask{
    private $main;
    private $player;
    public function __construct(AntiSpammer $main, Player $p){
        parent::__construct($main);
        $this->main = $main;
        $this->player = $p;
    }
    public function onRun($tick){
        $this->main->unMutePlayer($this->player);
        $this->player->sendMessage(FMT::colorMessage($this->main->getConfig()->getAll(){"un-muted_message"}));
    }
}