<?php

declare(strict_types=1);

namespace core\utils;

use core\Main;
use GdImage;

final class SkinUtil {

    private function __construct() {}

    public static array $skinWidths = [
        64 * 32 * 4 => 64,
        64 * 64 * 4 => 64,
        128 * 128 * 4 => 128,
        128 * 256 * 4 => 256
    ];

    public static array $skinHeights = [
        64 * 32 * 4 => 32,
        64 * 64 * 4 => 64,
        128 * 128 * 4 => 128,
        128 * 256 * 4 => 128
    ];

    public static function skinDataToImage(string $skinData) : GdImage|bool|null {
        $size = strlen($skinData);

        $width = self::$skinWidths[$size];
        $height = self::$skinHeights[$size];
        $skinPos = 0;
        $image = imagecreatetruecolor($width, $height);

        if($image === false)
            return null;

        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));

        for($y = 0; $y < $height; $y++) {
            for($x = 0; $x < $width; $x++) {
                $r = ord($skinData[$skinPos]);
                $skinPos++;
                $g = ord($skinData[$skinPos]);
                $skinPos++;
                $b = ord($skinData[$skinPos]);
                $skinPos++;
                $a = 127 - intdiv(ord($skinData[$skinPos]), 2);
                $skinPos++;
                $col = imagecolorallocatealpha($image, $r, $g, $b, $a);
                imagesetpixel($image, $x, $y, $col);
            }
        }

        imagesavealpha($image, true);
        return $image;
    }

    public static function skinImageToBytes($image) : string {
        $bytes = "";

        for ($y = 0; $y < imagesy($image); $y++) {
            for ($x = 0; $x < imagesx($image); $x++) {
                $rgba = @imagecolorat($image, $x, $y);
                $a = ((~($rgba >> 24)) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }

        @imagedestroy($image);

        return $bytes;
    }

    public static function getSkinFromPath(string $path) : string {

        if(!file_exists($path))
            $path = Main::getInstance()->getDataFolder()."/default/defaultSkin.png";

        assert(file_exists($path));
        $img = @imagecreatefrompng($path);
        $bytes = '';

        $size = getimagesize($path);
        for($y = 0; $y < $size[1]; $y++) {
            for($x = 0; $x < $size[0]; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~(($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $bytes;
    }
}