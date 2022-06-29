<?php

namespace core\manager\managers\pet;

use core\Main;
use core\manager\BaseManager;
use core\util\utils\ConfigUtil;
use pocketmine\entity\Entity;
use pocketmine\Player;

class PetManager extends BaseManager {

    /** @var Pet[] */
    private static array $pets = [];

    private static array $playerPets = [];

    public static function init() : void {

        Main::getDb()->query("CREATE TABLE IF NOT EXISTS pet (nick TEXT, selectedPet TEXT, pets TEXT)");

        Entity::registerEntity(PetEntity::class, true);

        foreach(ConfigUtil::PETS as $name => $data)
            self::$pets[$name] = new Pet($name, $data["networkID"], (float) $data["width"], (float) $data["height"], (float) $data["speed"], $data["price"], $data["displayName"], $data["canFly"]);

    }

    public static function registerPlayer(string $nick) : void{

        if(self::exists($nick))
            return;

        Main::getDb()->query("INSERT INTO pet (nick, selectedPet, pets) VALUES ('$nick', '', '')");
    }

    public static function exists(string $nick) : bool {
        return !empty(Main::getDb()->query("SELECT * FROM pet WHERE nick = '{$nick}'")->fetchArray());
    }

    public static function spawnPet(Pet $pet, Player $owner, string $displayName) : Pet {

        $entity = Entity::createEntity("PetEntity", $owner->getLevel(), Entity::createBaseNBT($owner), $pet);
        $entity->spawnToAll();

        $entity->setNameTag($displayName);

        $pet->setEntity($entity);
        $pet->setOwner($owner);

        self::$playerPets[$owner->getName()][] = $pet;
        return $pet;
    }

    /**
     * @return Pet[]
     */
    public static function getPets() : array {
        return self::$pets;
    }

    /**
     * @return Pet[]
     */
    public static function getPlayerPets() : array {
        return self::$playerPets;
    }

    /**
     * @param string $nick
     * @return Pet[]
     */

    public static function getSpecifyPlayerPets(string $nick) : ?array {
        return self::$playerPets[$nick] ?? null;
    }

    public static function getPet(string $name) : ?Pet {
        return clone self::$pets[$name] ?? null;
    }
}