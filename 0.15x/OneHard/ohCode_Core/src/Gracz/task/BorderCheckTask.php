<?php

namespace Gracz\task;

use Gracz\Main;
use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

class BorderCheckTask extends PluginTask {

    private $plugin;

    public function __construct(Main $plugin) {

        parent::__construct($plugin);
        $this->plugin = $plugin;

    }

    /**
     * Checks if the player is inside the border.
     *
     * @param $currentTick
     *
     * @return void
     */
    public function onRun($currentTick) {

        $players = $this->plugin->getServer()->getOnlinePlayers();

        foreach($players as $player) {

            #echo PHP_EOL.$player->getName().' is inside border: '.($this->plugin->border->insideBorder($player->getX(), $player->getZ()) ? 'yes' : 'no').PHP_EOL;

            if($this->plugin->border->insideBorder($player->getX(), $player->getZ())) continue;

            #echo PHP_EOL.'Prior X: '.$player->getPosition()->getX().' Y: '.$player->getPosition()->getY().' Z: '.$player->getPosition()->getZ();

            $location = $this->plugin->border->correctPosition($player->getLocation());

            #echo PHP_EOL.'Teleport to X: '.$location->getX().' Y: '.$location->getY().' Z: '.$location->getZ().PHP_EOL;

            # Push to teleports array to ensure that the player's teleport won't be stopped.
            if(!in_array($player->getName(), $this->plugin->teleports)) array_push($this->plugin->teleports, $player->getName());

            $player->teleport(new Vector3($location->getX(), $location->getY(), $location->getZ()));

            $player->sendMessage($this->plugin->msgReachedEnd);

        }

    }

}
