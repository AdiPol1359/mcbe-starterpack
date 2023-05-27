<?php

declare(strict_types=1);

namespace core\managers\war;

use core\Main;
use core\utils\SoundUtil;
use core\permissions\managers\FormatManager;
use core\utils\BroadcastUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use JetBrains\PhpStorm\ArrayShape;
use pocketmine\world\particle\HugeExplodeParticle;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class War {

    public const TAG = "tag";
    public const STAT_KILLS = "kills";
    public const STAT_DEATHS = "deaths";

    public function __construct(
        private int $id,
        private array $attackerGuild,
        private array $attackedGuild,
        private int $startTime,
        private int $endTime,
        private bool $ended,
        private string $winnerGuild
    ) {}

    public function getId() : int {
        return $this->id;
    }

    public function getAttacker() : string {
        return $this->attackerGuild[self::TAG];
    }

    public function getAttacked() : string {
        return $this->attackedGuild[self::TAG];
    }

    public function getAttackerStat(string $statName) : int {
        return $this->attackerGuild[$statName];
    }

    public function getAttackedStat(string $statName) : int {
        return $this->attackedGuild[$statName];
    }

    public function getStartTime() : int {
        return $this->startTime;
    }

    public function getEndTime() : int {
        return $this->endTime;
    }

    public function hasEnded() : bool {
        return $this->ended;
    }

    public function getWinner() : string {
        return $this->winnerGuild;
    }

    #[ArrayShape(["tag" => "mixed", "kills" => "mixed", "deaths" => "mixed"])] public function serializeAttacker() : array {
        return [
            "tag" => $this->attackerGuild[self::TAG],
            "kills" => $this->attackerGuild[self::STAT_KILLS],
            "deaths" => $this->attackerGuild[self::STAT_DEATHS]
        ];
    }

    #[ArrayShape(["tag" => "mixed", "kills" => "mixed", "deaths" => "mixed"])] public function serializeAttacked() : array {
        return [
            "tag" => $this->attackedGuild[self::TAG],
            "kills" => $this->attackedGuild[self::STAT_KILLS],
            "deaths" => $this->attackedGuild[self::STAT_DEATHS]
        ];
    }

    public function endWar(?string $winner = null, bool $deleted = false) : void {

        $this->winnerGuild = (!$winner ? $this->getActualWinner() : $winner);
        $this->ended = true;

        $damagerGuild = Main::getInstance()->getGuildManager()->getGuild($this->winnerGuild);
        $loserGuild = Main::getInstance()->getGuildManager()->getGuild($this->getLoser());
        $loserGuildHearts = $loserGuild ? $loserGuild->getHearts() : 1;

        $pointsWinner = "§8[§a+".(!$deleted ? Settings::$WIN_WAR_POINTS : Settings::$DESTROY_GUILD_POINTS)."§8]";
        $pointsLose = "§8[§c-".Settings::$LOSE_WAR_POINTS."§8]";

        $messages = FormatManager::guildFormatMessage("Gildia §e{TAG} ".$pointsWinner." §7wygrala wojne z gildia §e{TAG} ".$pointsLose, [$this->winnerGuild, $this->getLoser()], Server::getInstance()->getOnlinePlayers());

        $times = 0;

        $handler = Main::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() use (&$loserGuild, &$handler, &$times) : void {
            if($times > 60) {
                $handler->cancel();
                return;
            }

            if($loserGuild) {
                Main::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->addParticle($loserGuild->getHeartSpawn(), new HugeExplodeParticle());
            }

            $times++;
        }), 3);

        if($loserGuild) {
            if($loserGuild->getGuildHeart())
                SoundUtil::addSound($loserGuild->getGuildHeart()->getViewers(), $loserGuild->getHeartSpawn(), "mob.enderdragon.death");
        }

        if($loserGuildHearts <= 1 || $deleted) {

            $damagerGuild->addPoints(Settings::$DESTROY_GUILD_POINTS);

            $messages = FormatManager::guildFormatMessage(MessageUtil::format("Gildia §e{TAG}§7 stracila ostatnie serce przez gildie §e{TAG}"), [$this->getLoser(), $damagerGuild->getTag()], Server::getInstance()->getOnlinePlayers());

            BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($messages) : void {
                if(isset($messages[$onlinePlayer->getName()]))
                    $onlinePlayer->sendMessage($messages[$onlinePlayer->getName()]);
            });

            Main::getInstance()->getGuildManager()->deleteGuild($this->getLoser());
        } else {

            $loserGuild->reduceHearts();
            $loserGuild->setHealth(Settings::$MAX_GUILD_HEALTH);
            $loserGuild->setConquerTime((time() + (Settings::$CONQUER_TIME * 3600)));

            $loserGuild->getGuildGolem()?->close();

            $loserGuild->setGuildGolem(null);

            if($damagerGuild->getHearts() < 5) {
                $damagerGuild->addHearts();

                if(($damagerGuildHeart = $damagerGuild->getGuildHeart()) !== null)
                    $damagerGuildHeart->updateTag();
            }

            $damagerGuild->addPoints(Settings::$WIN_WAR_POINTS);
            $loserGuild->reducePoints(Settings::$LOSE_WAR_POINTS);

            $messages = FormatManager::guildFormatMessage(MessageUtil::format("Gildia §e{TAG}§7 stracila §e1 §7serce przez gildie §e{TAG}"), [$this->getLoser(), $damagerGuild->getTag()], Server::getInstance()->getOnlinePlayers());

            BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($messages) : void {
                if(isset($messages[$onlinePlayer->getName()]))
                    $onlinePlayer->sendMessage($messages[$onlinePlayer->getName()]);
            });
        }

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($messages) : void {
            if(isset($messages[$onlinePlayer->getName()]))
                $onlinePlayer->sendMessage(MessageUtil::format($messages[$onlinePlayer->getName()]));
        });
    }

    public function getActualWinner() : string {
        $ratioAttacker = $this->attackerGuild[self::STAT_KILLS] / ($this->attackerGuild[self::STAT_DEATHS] <= 0 ? 1 : $this->attackerGuild[self::STAT_DEATHS]);
        $ratioAttacked = $this->attackedGuild[self::STAT_KILLS] / ($this->attackedGuild[self::STAT_DEATHS] <= 0 ? 1 : $this->attackedGuild[self::STAT_DEATHS]);

        if($ratioAttacker > $ratioAttacked)
            $winner = $this->attackerGuild[self::TAG];
        elseif($ratioAttacker === $ratioAttacked)
            $winner = "";
        else
            $winner = $this->attackedGuild[self::TAG];

        if($winner === "")
            $winner = $this->attackedGuild[self::TAG];

        return $winner;
    }

    public function getLoser() : string {
        return $this->winnerGuild === $this->getAttacker() ? $this->getAttacked() : $this->getAttacker();
    }

    public function addStatAttacker(string $name, int $count) : void {
        $this->attackerGuild[$name] += $count;
    }

    public function addStatAttacked(string $name, int $count) : void {
        $this->attackedGuild[$name] += $count;
    }

    public function reduceStatAttacker(string $name, int $count) : void {
        $this->attackerGuild[$name] -= $count;
    }

    public function reduceStatAttacked(string $name, int $count) : void {
        $this->attackedGuild[$name] -= $count;
    }
}