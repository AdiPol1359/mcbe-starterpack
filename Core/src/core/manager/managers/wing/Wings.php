<?php

namespace core\manager\managers\wing;

class Wings {

    private string $name;
    private string $geometryName;
    private $geometryData;
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

    public function getImage() {
        $image = @imagecreatefrompng($this->path . "skin.png");
        imagecolortransparent($image, imagecolorallocatealpha($image, 0, 0, 0, 127));

        return $image;
    }
}