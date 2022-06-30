<?php

namespace core\util\utils;

use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;

class VectorUtil {

    public static function getPositionFromData(string $positionData) : Position {
        $positionData = str_replace("Position(", "", $positionData);
        $positionData = str_replace(")", "", $positionData);
        $positionData = str_replace("level=", "", $positionData);
        $positionData = str_replace("x=", "", $positionData);
        $positionData = str_replace("y=", "", $positionData);
        $positionData = str_replace("z=", "", $positionData);
        $positionData = explode(",", $positionData);

        Server::getInstance()->loadLevel($positionData[0]);

        return Position::fromObject(new Vector3((int) $positionData[1], (int) $positionData[2], (int) $positionData[3]), Server::getInstance()->getLevelByName($positionData[0]));
    }
}