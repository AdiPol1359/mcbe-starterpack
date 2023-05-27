<?php

declare(strict_types=1);

namespace core\managers\wing;

use GdImage;

class Wings {

    private string $name;
    private string $geometryName;
    private false|string $geometryData;
    private string $path;

    public function __construct(string $name, string $path) {
        $this->name = $name;
        $this->geometryName = "geometry." . $name;
        $this->path = $path . $name . DIRECTORY_SEPARATOR;
        $this->geometryData = file_get_contents($this->path . "geometry.json");
    }

    public function getName() : string {
        return $this->name;
    }

    public function getGeometryName() : string {
        return $this->geometryName;
    }

    public function getGeometryData() : string {
        return $this->geometryData;
    }

    public function getImage() : GdImage|bool {
        $image = @imagecreatefrompng($this->path . "skin.png");
        imagecolortransparent($image, imagecolorallocatealpha($image, 0, 0, 0, 127));

        return $image;
    }
}