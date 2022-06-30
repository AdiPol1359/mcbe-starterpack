<?php

namespace core\task;

use core\Main;
use core\manager\BaseManager;
use core\task\tasks\AntylogoutTask;
use core\task\tasks\BanTask;
use core\task\tasks\BotTask;
use core\task\tasks\ClearLagTask;
use core\task\tasks\DepositTask;
use core\task\tasks\GroupsTask;
use core\task\tasks\QuestTask;
use core\task\tasks\RouletteDrawTask;
use core\task\tasks\RouletteTask;
use core\task\tasks\LobbyTask;
use core\task\tasks\MuteTask;
use core\task\tasks\ParticleTask;
use core\task\tasks\PlayTimeTask;
use core\task\tasks\PunishmentMessageTask;
use core\task\tasks\SaveDataTask;
use core\task\tasks\ScoreboardTask;
use core\task\tasks\TpaTask;
use core\task\tasks\VanishTask;

class TaskManager extends BaseManager {

    public static function init() : void{

        (new RouletteDrawTask());

        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DepositTask(), 20);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new AntylogoutTask(), 20);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new QuestTask(), 20);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new ParticleTask(), 2);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new RouletteTask(), 20);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new MuteTask(), 20);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new BanTask(), 20);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new ScoreboardTask(), 50);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new BotTask(), 20 * 60);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new GroupsTask(), 20);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new PlayTimeTask(), 20);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new LobbyTask(), 20);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new PunishmentMessageTask(), 20*10);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new ClearLagTask(60*5), 20);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new TpaTask(), 20);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new VanishTask(), 20);
        Main::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new SaveDataTask(), 20*60*10, 20*60*10);
    }
}