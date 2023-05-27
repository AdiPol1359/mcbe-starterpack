<?php

declare(strict_types=1);

namespace core\utils;

use JetBrains\PhpStorm\Pure;
use pocketmine\world\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;

final class VectorUtil {

    private function __construct() {}

    public static function getPositionFromData(string $positionData) : Position {
        $positionData = str_replace("Position(", "", $positionData);
        $positionData = str_replace(")", "", $positionData);
        $positionData = str_replace("world=", "", $positionData);
        $positionData = str_replace("x=", "", $positionData);
        $positionData = str_replace("y=", "", $positionData);
        $positionData = str_replace("z=", "", $positionData);
        $positionData = explode(",", $positionData);

        return Position::fromObject(new Vector3((float)$positionData[1], (float)$positionData[2], (float)$positionData[3]), Server::getInstance()->getWorldManager()->getWorldByName($positionData[0]));
    }

    public static function getVectorFromData(string $positionData) : Vector3 {
        $positionData = str_replace("Vector3(", "", $positionData);
        $positionData = str_replace(")", "", $positionData);
        $positionData = str_replace("x=", "", $positionData);
        $positionData = str_replace("y=", "", $positionData);
        $positionData = str_replace("z=", "", $positionData);
        $positionData = explode(",", $positionData);

        return new Vector3((float)$positionData[0], (float)$positionData[1], (float)$positionData[2]);
    }

    public static function shortSerializeVector(Vector3 $vector3) : string {
        return $vector3->x.":".$vector3->y.":".$vector3->z;
    }

    #[Pure] public static function shortDeserializeVector(string $value) : ?Vector3 {
        $str = explode(":", $value);

        if(!isset($str[2]))
            return null;

        return new Vector3((float)$str[0], (float)$str[1], (float)$str[2]);
    }
}