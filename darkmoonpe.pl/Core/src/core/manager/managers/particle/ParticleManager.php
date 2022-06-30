<?php

namespace core\manager\managers\particle;

use core\Main;
use core\manager\BaseManager;
use core\manager\managers\particle\particles\BaseParticle;
use core\manager\managers\particle\particles\types\AngryVillagerParticle;
use core\manager\managers\particle\particles\types\CampfireParticle;
use core\manager\managers\particle\particles\types\CriticalsParticle;
use core\manager\managers\particle\particles\types\EndRodParticle;
use core\manager\managers\particle\particles\types\ExplodeParticle;
use core\manager\managers\particle\particles\types\FlameParticle;
use core\manager\managers\particle\particles\types\HappyVillagerParticle;
use core\manager\managers\particle\particles\types\HeartParticle;
use core\manager\managers\particle\particles\types\LavaParticle;
use core\manager\managers\particle\particles\types\NoteParticle;
use core\manager\managers\particle\particles\types\RainParticle;
use core\manager\managers\particle\particles\types\RingoParticle;
use core\manager\managers\particle\particles\types\TotemParticle;

class ParticleManager extends BaseManager {

    private static array $particles = [];

    public static function init() : void {

        $particles = [
            new RingoParticle(),
            new FlameParticle(),
            new ExplodeParticle(),
            new HeartParticle(),
            new CampfireParticle(),
            new EndRodParticle(),
            new LavaParticle(),
            new NoteParticle(),
            new RainParticle(),
            new CriticalsParticle(),
            new HappyVillagerParticle(),
            new AngryVillagerParticle(),
            new TotemParticle()
        ];

        Main::getDb()->query("CREATE TABLE IF NOT EXISTS particle (nick TEXT, selectedParticle TEXT, particles TEXT)");

        $players = [];

        $query = Main::getDb()->query("SELECT * FROM particle");

        while($row = $query->fetchArray(SQLITE3_ASSOC))
            $players[$row["nick"]] = $row["selectedParticle"];

        foreach($particles as $particle) {
            foreach($players as $playerName => $particleData) {
                if($particleData === null)
                    continue;

                if($particleData === $particle->getName())
                    $particle->addPlayer($playerName);
            }

            self::$particles[$particle->getName()] = $particle;
        }
    }

    public static function registerPlayer(string $nick) : void{

        if(self::exists($nick))
            return;

        Main::getDb()->query("INSERT INTO particle (nick, selectedParticle, particles) VALUES ('$nick', '', '')");
    }

    public static function exists(string $nick) : bool {
        return !empty(Main::getDb()->query("SELECT * FROM particle WHERE nick = '{$nick}'")->fetchArray());
    }

    public static function getParticle(string $particleName) : ?BaseParticle {
        return self::$particles[$particleName] ?? null;
    }

    public static function getPlayerParticles(string $nick) : array {

        $particles = [];

        foreach(self::$particles as $key => $particle){

            if(($key = array_search($nick, $particle->getPlayers())) !== false)
                $particles[] = $particle;
        }

        return $particles;
    }

    public static function getParticles() : ?array {
        return self::$particles;
    }
}