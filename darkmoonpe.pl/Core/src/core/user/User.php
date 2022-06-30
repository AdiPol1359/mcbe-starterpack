<?php

namespace core\user;

use core\Main;
use core\manager\managers\CobblestoneManager;
use core\manager\managers\DropManager;
use core\manager\managers\MoneyManager;
use core\manager\managers\particle\ParticleManager;
use core\manager\managers\particle\particles\BaseParticle;
use core\manager\managers\pet\Pet;
use core\manager\managers\pet\PetManager;
use core\manager\managers\quest\Quest;
use core\manager\managers\quest\QuestManager;
use core\manager\managers\service\ServicesManager;
use core\manager\managers\SettingsManager;
use core\manager\managers\skill\SkillManager;
use core\manager\managers\StatsManager;
use core\util\utils\ConfigUtil;
use core\util\utils\InventoryUtil;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;

class User {

    private string $name;
    private string $xuid;

    private ?string $selectedPet;
    private string $pets;
    private ?string $selectedParticle;
    private string $particles;

    private array $drop = [];
    private array $settings = [];
    private array $money = [];
    private array $quest = [];
    private array $skills = [];
    private array $breakCobble = [];
    private array $services = [];
    private array $stats = [];
    private array $killedPlayers = [];

    private int $playTime;
    private int $lastCreateCave;
    private int $lastReport;

    private bool $isOnline;

    private ?Position $pos1;
    private ?Position $pos2;

    public function __construct(string $name, string $xuid) {
        $this->name = $name;
        $this->xuid = $xuid;

        $this->pos1 = null;
        $this->pos2 = null;
        $this->playTime = 0;
        $this->isOnline = false;
        $this->lastCreateCave = 0;
        $this->lastReport = 0;

        $this->setDefaultDrops();
        $this->setDefaultSettings();
        $this->setDefaultMoney();
        $this->setDefaultQuest();
        $this->setDefaultSkills();
        $this->setDefaultCobblestone();
        $this->setDefaultServices();
        $this->setDefaultPets();
        $this->setDefaultParticles();
        $this->setDefaultStats();
    }

    public function getXUID() : string {
        return $this->xuid;
    }

    public function getName() : string {
        return $this->name;
    }

    public function isOnline() : bool {
        return $this->isOnline;
    }

    public function setOnline(bool $status = true) : void {
        $this->isOnline = $status;
    }

    public function getPlayer() : ?Player {
        return Server::getInstance()->getPlayerExact($this->name);
    }

    /*
     *
     * DROP
     *
     */

    public function setDefaultDrops() : void {
        DropManager::registerPlayer($this->name);

        $db = Main::getDb()->query("SELECT * FROM 'drop' WHERE nick = '{$this->name}'")->fetchArray(SQLITE3_ASSOC);

        $drops = [];

        foreach($db as $name => $id) {
            if($name === "nick")
                continue;
            $drops[$name] = $id;
        }

        $this->drop = $drops;
    }

    public function saveDrop() : void {
        foreach($this->drop as $row => $value)
            Main::getDb()->query("UPDATE 'drop' SET '$row' = '$value' WHERE nick = '$this->name'");
    }

    public function isDropEnabled(string $name) : int {
        foreach($this->drop as $row => $value) {
            if($row == $name)
                return $value === 1 ? 1 : 0;
        }

        return 0;
    }

    public function switchDrop(string $name) : void {
        foreach($this->drop as $row => $value) {
            if($row === $name)
                $this->drop[$row] === 1 ? $this->drop[$row] = 0 : $this->drop[$row] = 1;
        }
    }

    public function setDrop(string $name, int $status = 1) : void {
        foreach($this->drop as $row => $value) {
            if($row === $name)
                $this->drop[$row] = $status;
        }
    }

    public function getDrops() : array {
        return $this->drop;
    }

    /*
     *
     * Settings
     *
     */

    public function setDefaultSettings() : void {
        SettingsManager::registerPlayer($this->name);

        $db = Main::getDb()->query("SELECT * FROM 'settings' WHERE nick = '{$this->name}'")->fetchArray(SQLITE3_ASSOC);

        $settings = [];

        foreach($db as $value => $status) {
            if($value === "nick")
                continue;

            $settings[$value] = $status;
        }

        $this->settings = $settings;
    }

    public function isSettingEnabled(string $name) : bool {
        foreach($this->settings as $row => $value) {
            if($row === $name)
                return $value === 1;
        }

        return false;
    }

    public function setSetting(string $name, bool $status = true) : void {
        foreach($this->settings as $row => $value) {
            if($row === $name)
                $this->settings[$row] = ($status ? 1 : 0);
        }
    }

    public function switchSetting(string $name) : void {
        foreach($this->settings as $row => $value) {
            if($row === $name)
                !$this->settings[$row] ? $this->settings[$row] = 1 : $this->settings[$row] = 0;
        }
    }

    public function getSettings() : array {
        return $this->settings;
    }

    public function saveSettings() : void {
        foreach($this->settings as $row => $value)
            Main::getDb()->query("UPDATE 'settings' SET '$row' = '$value' WHERE nick = '$this->name'");
    }

    /*
     *
     * MONEY
     *
     */

    public function setDefaultMoney() : void {
        MoneyManager::registerPlayer($this->name);

        $db = Main::getDb()->query("SELECT * FROM 'money' WHERE nick = '{$this->name}'")->fetchArray(SQLITE3_ASSOC);

        $money = [];

        foreach($db as $row => $value) {
            if($row === "nick")
                continue;
            $money[$row] = $value;
        }

        $this->money = $money;
    }

    public function saveMoney() : void {
        foreach($this->money as $row => $value) {
            $money = (float) sprintf('%0.2f', $value);
            Main::getDb()->query("UPDATE 'money' SET '$row' = '$money' WHERE nick = '$this->name'");
        }
    }

    public function getPlayerMoney() : float {
        foreach($this->money as $row => $value)
            if(!is_null($value))
                return (float) sprintf('%0.2f', $value);
        return 0;
    }

    public function setPlayerMoney(float $status) : void {
        foreach($this->money as $row => $value)
            $this->money[$row] = (float) sprintf('%0.2f', $status);
    }

    public function reducePlayerMoney(float $status) : void {
        foreach($this->money as $row => $value)
            $this->money[$row] -= (float) sprintf('%0.2f', $status);
    }

    public function addPlayerMoney(float $status) : void {
        foreach($this->money as $row => $value)
            $this->money[$row] += (float) sprintf('%0.2f', $status);
    }

    public function getMoney() : array {
        return $this->money;
    }

    /*
     *
     * QUEST
     *
     */

    public function setDefaultQuest() : void {
        QuestManager::registerPlayer($this->name);

        $db = Main::getDb()->query("SELECT * FROM 'quest' WHERE nick = '{$this->name}'")->fetchArray(SQLITE3_ASSOC);

        $quests = [];

        foreach($db as $name => $id) {
            if($name === "nick")
                continue;

            if($name === "quests") {
                $quests[$name] = json_decode($id, true);
                continue;
            }

            $quests[$name] = $id;
        }

        $this->quest = $quests;
    }

    public function saveQuests() : void {
        foreach($this->quest as $row => $value) {
            if($row === "quests")
                $value = json_encode($value);

            Main::getDb()->query("UPDATE 'quest' SET '$row' = '$value' WHERE nick = '$this->name'");
        }
    }

    public function nextQuest() : void {

        $quest = $this->getSelectedQuest();
        switch($quest->getRewardType()) {
            case "money":
                $this->addPlayerMoney($quest->getRewardCount());
                break;
            case "item":
                InventoryUtil::addItem(Item::get($quest->getRewardId(), $quest->getRewardDamage(), (int) $quest->getRewardCount()), Server::getInstance()->getPlayerExact($this->name));
                break;

            default:
                break;
        }

        $this->quest["quests"][$this->quest["selected"]] = true;
        $this->quest["madeQuests"] += 1;
        $this->quest["selected"] = 0;
        $this->quest["status"] = 0;
    }

    public function resetQuest() : void {
        $this->quest["selected"] = 0;
        $this->quest["status"] = 0;
    }

    public function getQuests() : array {
        return (array) $this->quest["quests"];
    }

    public function getSelectedQuest() : ?Quest {
        return QuestManager::getQuest($this->quest["selected"]);
    }

    public function addToStatus(float $count = 1.0) : void {
        $this->getQuestStatus() < $this->getSelectedQuest()->getMaxTimes() ? $this->quest["status"] += $count : null;
    }

    public function getQuestStatus() : float {
        return $this->quest["status"];
    }

    public function hasMadeAllQuests() : bool {

        $bool = true;

        foreach($this->quest["quests"] as $quest => $status) {
            if(!$status)
                $bool = false;
        }

        return $bool;
    }

    public function hasMadeSpecifyQuest(int $questId) : bool {
        return $this->quest["quests"][$questId];
    }

    public function hasMadeQuest() : bool {
        return $this->getSelectedQuest()->getMaxTimes() <= $this->getQuestStatus();
    }

    public function getDoneQuests() : array {
        $quests = [];

        foreach($this->quest["quests"] as $quest => $status) {
            if($status)
                $quests[] = $quest;
        }

        return $quests;
    }

    public function getDoneQuestCount() : int {
        return $this->quest["madeQuests"];
    }

    public function isDoneQuest() : bool {
        if($this->isSelectedQuest()) {
            $status = $this->getQuestStatus();
            $max_status = $this->getSelectedQuest()->getMaxTimes();
            if($max_status != -1)
                if($status >= $max_status)
                    return true;
        }

        return false;
    }

    public function resetQuests() : void {
        $this->quest["quests"] = [];
    }

    public function resetNotBeingProcessed() : void {

        if($this->isSelectedQuest()) {
            $this->resetQuests();
            return;
        }

        $quests = [];

        foreach($this->quest["quests"] as $questId => $status) {
            if(($selectedQuest = $this->getSelectedQuest()) === null)
                return;

            if($selectedQuest->getId() === $questId)
                $quests[$questId] = $status;

        }

        $this->quest["quests"] = $quests;
    }

    public function addQuest(int $questId, bool $status = false) : void {
        $this->quest["quests"][$questId] = $status;
    }

    public function generateQuests(int $count) : void {

        $randomQuests = [];

        for($i = 1; $i <= $count; $i++) {
            $quest = QuestManager::getRandomQuest();

            if(!$quest)
                continue;

            $randomQuests[$quest->getId()] = false;
        }

        $this->quest["quests"] = $randomQuests;
    }

    public function isSelectedQuest() : bool {
        return $this->quest["selected"] > 0;
    }

    public function setSelectQuest(int $status) : void {
        $this->quest["selected"] = $status;
    }

    public function getTimestamp() : int {
        return $this->quest["questTime"];
    }

    public function setTimestamp(int $time) : void {
        $this->quest["questTime"] = $time;
    }

    /*
     *
     * SKILLS
     *
     */

    public function setDefaultSkills() : void {

        if(!SkillManager::exists($this->name))
            return;

        $db = Main::getDb()->query("SELECT * FROM skill WHERE nick = '{$this->name}'");

        $skills = [];

        while($row = $db->fetchArray(SQLITE3_ASSOC))
            $skills[] = $row["skill"];

        $this->skills = $skills;
    }

    public function saveSkills() : void {
        foreach($this->skills as $row => $value) {
            if(empty(Main::getDb()->query("SELECT * FROM skill WHERE nick = '{$this->name}' AND skill = '{$value}'")->fetchArray(SQLITE3_ASSOC)))
                Main::getDb()->query("INSERT INTO skill (nick, skill) VALUES ('{$this->name}', '$value')");
        }
    }

    public function hasSkill(int $id) : bool {
        foreach($this->skills as $quest)
            if($quest === $id)
                return true;
        return false;
    }

    public function addSkill(int $id) : void {
        $this->skills[] = $id;
    }

    public function getSkills() : array {
        $skills = [];

        foreach($this->skills as $quest)
            $skills[] = $quest;

        return $skills;
    }

    /*
     *
     * COBBLESTONE
     *
     */

    public function setDefaultCobblestone() : void {

        CobblestoneManager::registerPlayer($this->name);

        $db = Main::getDb()->query("SELECT * FROM 'cobblestone' WHERE nick = '{$this->name}'")->fetchArray(SQLITE3_ASSOC);

        $cobble = [];

        foreach($db as $name => $id) {
            if($name === "nick")
                continue;
            $cobble[] = [$name, $id];
        }

        $this->breakCobble = $cobble;
    }

    public function saveCobblestone() : void {
        foreach($this->breakCobble as $row => $value)
            Main::getDb()->query("UPDATE 'cobblestone' SET '$value[0]' = '$value[1]' WHERE nick = '$this->name'");
    }

    public function addCobble() : void {
        $this->breakCobble[0][1]++;
    }

    public function getCobble() : int {
        return $this->breakCobble[0][1];
    }

    /**
     *
     * SERVICES
     *
     */

    public function setDefaultServices() : void {

        $services = ServicesManager::getServices($this->name);

        foreach($services as $service => $serviceInfo)
            $this->services[$service] = $serviceInfo;

    }

    public function saveServices() : void {
        foreach($this->services as $row => $value) {
            if(empty(Main::getDb()->query("SELECT * FROM service WHERE id = '{$row}' AND nick = '{$this->name}'")->fetchArray(SQLITE3_ASSOC)))
                Main::getDb()->query("INSERT INTO service (id, nick, service, collected, time) VALUES ('$row', '{$this->name}', '{$value['service']}', '{$value['collected']}', '{$value['time']}')");
            else
                Main::getDb()->query("UPDATE service SET collected = '{$value['collected']}', time = '{$value['time']}' WHERE id = '$row'");
        }
    }

    public function addService(int $index) : void {
        $id = mt_rand(0, 10000);

        while(ServicesManager::existsService($id))
            $id = mt_rand(0, 10000);

        $this->services[$id] = ["nick" => $this->name, "service" => $index, "collected" => false, "time" => 0];
    }

    public function isCollected(int $id) : bool {
        return $this->services[$id]["collected"];
    }

    public function claimReward(int $id, string $command) : void {
        $cmd = str_replace("{nick}", "$this->name", $command);
        Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), "$cmd");
        $this->services[$id]["collected"] = true;
        $this->services[$id]["time"] = time();
    }

    public function hasService() : bool {
        return count($this->services) > 0;
    }

    public function getServices() : array {
        return $this->services;
    }

    /**
     *
     * PETS
     *
     */

    public function setDefaultPets() : void {

        PetManager::registerPlayer($this->name);

        $db = Main::getDb()->query("SELECT * FROM 'pet' WHERE nick = '{$this->name}'")->fetchArray(SQLITE3_ASSOC);

        $pets = "";
        $selectedPet = null;

        foreach($db as $name => $id) {
            if($name === "nick")
                continue;

            if($name === "selectedPet")
                $selectedPet = $id;

            if($name === "pets")
                $pets = $id;
        }

        $this->selectedPet = $selectedPet;
        $this->pets = $pets;
    }

    public function savePets() : void {
        Main::getDb()->query("UPDATE 'pet' SET 'pets' = '{$this->pets}', 'selectedPet' = '{$this->selectedPet}' WHERE nick = '$this->name'");
    }

    public function addPet(Pet $pet) : void {
        $this->pets .= $pet->getName() . ";";
    }

    public function hasPet(Pet $pet) : bool {
        $petsNames = explode(";", $this->pets);

        foreach($petsNames as $name) {
            if(!$name)
                continue;

            $petManager = PetManager::getPet($name);

            if(!$pet)
                continue;

            if($petManager->getName() === $pet->getName())
                return true;
        }

        return false;
    }

    public function setSelectedPet(?Pet $pet) : void {
        $this->selectedPet = $pet ? $pet->getName() : '';
    }

    public function getSelectedPet() : ?Pet {
        return !$this->selectedPet ? null : PetManager::getPet($this->selectedPet);
    }

    public function getPetsNames() : array {
        $petsNames = explode(";", $this->pets);

        $pets = [];

        foreach($petsNames as $name) {
            if(!$name)
                continue;

            $pet = PetManager::getPet($name);

            if(!$pet)
                continue;

            $pets[] = $pet;
        }

        return $pets;
    }

    /**
     *
     * PARTICLES
     *
     */

    public function setDefaultParticles() : void {

        ParticleManager::registerPlayer($this->name);

        $db = Main::getDb()->query("SELECT * FROM 'particle' WHERE nick = '{$this->name}'")->fetchArray(SQLITE3_ASSOC);

        $particles = "";
        $selectedParticle = null;

        foreach($db as $name => $id) {
            if($name === "nick")
                continue;

            if($name === "selectedParticle")
                $selectedParticle = $id;

            if($name === "particles")
                $particles = $id;
        }

        $this->selectedParticle = $selectedParticle;
        $this->particles = $particles;
    }

    public function saveParticles() : void {
        Main::getDb()->query("UPDATE 'particle' SET 'particles' = '{$this->particles}', 'selectedParticle' = '{$this->selectedParticle}' WHERE nick = '$this->name'");
    }

    public function addParticle(BaseParticle $particle) : void {
        $this->particles .= $particle->getName() . ";";
    }

    public function hasParticle(BaseParticle $particle) : bool {
        $particleNames = explode(";", $this->particles);

        foreach($particleNames as $name) {
            if(!$name)
                continue;

            $particleManager = ParticleManager::getParticle($name);

            if(!$particle)
                continue;

            if($particleManager->getName() === $particle->getName())
                return true;
        }

        return false;
    }

    public function setSelectedParticle(?BaseParticle $particle = null) : void {
        $this->selectedParticle = $particle ? $particle->getName() : null;
    }

    public function getSelectedParticle() : ?BaseParticle {
        return !$this->selectedParticle ? null : ParticleManager::getParticle($this->selectedParticle);
    }

    public function getParticlesNames() : array {
        $particleNames = explode(";", $this->particles);

        $particles = [];

        foreach($particleNames as $name) {
            if(!$name)
                continue;

            $particle = ParticleManager::getParticle($name);

            if(!$particle)
                continue;

            $particles[] = $particle;
        }

        return $particles;
    }

    /*
     *
     * Stats
     *
     */

    public function setDefaultStats() : void {
        StatsManager::registerPlayer($this->name);

        $db = Main::getDb()->query("SELECT * FROM 'stats' WHERE nick = '{$this->name}'")->fetchArray(SQLITE3_ASSOC);

        $stats = [];

        foreach($db as $value => $status) {
            if($value === "nick")
                continue;

            $stats[$value] = $status;
        }

        $this->stats = $stats;
    }

    public function setStat(string $statName, int $value) : void {
        $this->stats[$statName] = $value;
    }

    public function addToStat(string $statName, int $value) : void {
        $this->stats[$statName] += $value;
    }

    public function reduceStat(string $statName, int $value) : void {
        $this->stats[$statName] -= $value;
    }

    public function getStat(string $statName) : int {
        return $this->stats[$statName];
    }

    public function saveStats() : void {
        foreach($this->stats as $row => $value)
            Main::getDb()->query("UPDATE 'stats' SET '$row' = '$value' WHERE nick = '$this->name'");
    }

    public function getStats() : array {
        return $this->stats;
    }

    /**
     *
     * PROTECT
     *
     */

    public function getPos1() : ?Position {
        return $this->pos1;
    }

    public function setPos1($pos1) : void {
        $this->pos1 = $pos1;
    }

    public function getPos2() : ?Position {
        return $this->pos2;
    }

    public function setPos2($pos2) : void {
        $this->pos2 = $pos2;
    }

    /**
     *
     * PLAYTIME
     *
     */

    public function getPlayTime() : int {
        return $this->playTime;
    }

    public function addToPlayTime(int $time = 1) : void {
        $this->playTime += $time;
    }

    public function setPlayTime(int $time = 0) : void {
        $this->playTime = $time;
    }

    /**
     *
     * LAST CREATE CAVE
     *
     */

    public function getLastCreateCave() : int {
        return $this->lastCreateCave;
    }

    public function setLastCreateCave() : void {
        $this->lastCreateCave = (time() + ConfigUtil::CREATED_CAVE);
    }

    /**
     *
     * LAST REPORT
     *
     */

    public function getLastReport() : int {
        return $this->lastReport;
    }

    public function setLastReport() : void {
        $this->lastReport = (time() + (60 * ConfigUtil::REPORT_COOLDOWN_TIME));
    }

    /**
     *
     * KILLED PLAYERS
     *
     */

    public function getKilledPlayers() : array {
        return $this->killedPlayers;
    }

    public function addKilledPlayer(string $nick, string $ip) : void {
        $this->killedPlayers[$nick] = ["ip" => $ip, "time" => (time() + (60 * ConfigUtil::KILL_STREAK_DELAY))];
    }

    public function hasKilled(string $nick, ?string $ip = null) : bool {
        foreach($this->killedPlayers as $entityName => $data) {
            if($data["time"] <= time()) {
                unset($this->killedPlayers[$entityName]);
                continue;
            }

            if($entityName === $nick || $data["ip"] === $ip)
                return true;
        }

        return false;
    }
}