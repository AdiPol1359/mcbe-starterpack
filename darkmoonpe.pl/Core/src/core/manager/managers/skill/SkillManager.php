<?php

namespace core\manager\managers\skill;

use core\Main;
use core\manager\BaseManager;

class SkillManager extends BaseManager {

    private static array $skills = [];

    public static function init() : void {
        Main::getDb()->query("CREATE TABLE IF NOT EXISTS skill (nick TEXT, skill INT)");
    }

    public static function getSkill(int $id) : ?Skill {
        return self::$skills[$id];
    }

    public static function loadSkills() : void {

        $db = Main::$skills;

        $skill = [];

        foreach($db as $row => $value)
            $skill[$row] = new Skill($row, $value["name"], $value["description"], $value["cost"]);

        self::$skills = $skill;
    }

    public static function exists(string $nick) : bool {
        return !empty(Main::getDb()->query("SELECT * FROM skill WHERE nick = '$nick'")->fetchArray());
    }

    public static function getSkills() : array {
        return self::$skills;
    }
}