<?php
namespace EssentialsPE\Tasks\Updater;

use EssentialsPE\BaseFiles\BaseTask;
use EssentialsPE\Loader;
use pocketmine\utils\TextFormat;

class AutoFetchCallerTask extends BaseTask{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin);
    }

    /**
     * @param int $currentTick
     */
    public function onRun($currentTick){
        $this->getPlugin()->getServer()->getLogger()->debug(TextFormat::YELLOW . "Running EssentialsPE's AutoFetchCallerTask");
        $this->getPlugin()->fetchEssentialsPEUpdate(false);
    }
}