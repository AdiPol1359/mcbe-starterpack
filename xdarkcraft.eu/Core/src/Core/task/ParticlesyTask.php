<?php

namespace Core\task;

use Core\api\ParticlesyAPI;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\level\particle\{
    DustParticle, ExplodeParticle
};
use pocketmine\math\Vector3;
use Core\Main;

class ParticlesyTask extends Task {

	
	public function onRun(int $currentTick) {
	    foreach(ParticlesyAPI::$particles as $type => $datas) {
            foreach ($datas as $data) {
                $player = Server::getInstance()->getPlayerExact($data[0]);
                $color = $data[1];

                if ($player == null)
                    continue;

                $nick = $player->getName();

                if ($type == ParticlesyAPI::PARTICLE_RINGO) {
                    if(!$player->isOnGround())
                        continue;

                    if(isset(Main::$antylogoutPlayers[$nick]))
                        continue;

                    if(!isset(Main::$lastPosition[$nick]['ringo']))
                        Main::$lastPosition[$nick]['ringo'] = $player->asVector3();

                    $from = Main::$lastPosition[$player->getName()]['ringo'];

                    if($player->getX() == $from->getX() && $player->getY() == $from->getY() && $player->getZ() == $from->getZ()) {
                        $y = $player->getY() + 0.1;
                        $count = 100;

                        $rgb = ParticlesyAPI::getRGB($player, $color);
                        $particle = new DustParticle($player, $rgb[0], $rgb[1], $rgb[2], 1);
                        for ($yaw = 1, $i = 1; $i <= $count; $yaw += (M_PI * 2) / $count, $i++) {
                            $x = -sin($yaw) + $player->x;
                            $z = cos($yaw) + $player->z;
                            $particle->setComponents($x, $y, $z);
                            $player->getLevel()->addParticle($particle);
                        }
                    } else Main::$lastPosition[$nick]['ringo'] = $player->asVector3();
                }

                if($type == ParticlesyAPI::PARTICLE_CLOUD) {
                    if(!isset(Main::$lastPosition[$nick]['cloud']))
                        Main::$lastPosition[$nick]['cloud'] = $player->asVector3();

                    $from = Main::$lastPosition[$player->getName()]['cloud'];

                    if($player->getX() == $from->getX() && $player->getY() == $from->getY() && $player->getZ() == $from->getZ()) {
                            $rgb = ParticlesyAPI::getRGB($player, ParticlesyAPI::COLOR_DARK_BLUE);

                        //$player->getLevel()->addParticle(new DustParticle($player->add((mt_rand(-20, 20)/10), 3, (mt_rand(-20, 20)/10)), $rgb[0], $rgb[1], $rgb[2]));
                    } else Main::$lastPosition[$nick]['cloud'] = $player->asVector3();
                }
            }
        }
    }
}