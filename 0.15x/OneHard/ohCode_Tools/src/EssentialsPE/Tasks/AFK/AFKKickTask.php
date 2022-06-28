<?php
namespace EssentialsPE\Tasks\AFK;

use EssentialsPE\BaseFiles\BaseTask;
use EssentialsPE\Loader;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class AFKKickTask extends BaseTask{
    /** @var Player  */
    protected $player;

    /**
     * @param Loader $plugin
     * @param Player $player
     */
    public function __construct(Loader $plugin, Player $player){
        parent::__construct($plugin);
        $this->player = $player;
    }

    /**
     * @param int $currentTick
     */
    public function onRun($currentTick){
        $this->getPlugin()->getServer()->getLogger()->debug(TextFormat::YELLOW . "Running EssentialsPE's AFKKickTask");
        if($this->player instanceof Player && $this->player->isOnline() && $this->getPlugin()->isAFK($this->player) && !$this->player->hasPermission("essentials.afk.kickexempt") && time() - $this->getPlugin()->getLastPlayerMovement($this->player) >= $this->getPlugin()->getConfig()->getNested("afk.auto-set")){
            $this->player->kick("You have been kicked for idling more than " . (($time = floor($this->getPlugin()->getConfig()->getNested("afk.auto-kick"))) / 60 >= 1 ? ($time / 60) . " minutes" : $time . " seconds"), false);
        }
    }
} 