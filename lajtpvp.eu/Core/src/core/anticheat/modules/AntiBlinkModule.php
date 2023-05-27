<?php

declare(strict_types=1);

namespace core\anticheat\modules;

use core\anticheat\BaseModule;
use JetBrains\PhpStorm\Pure;

class AntiBlinkModule extends BaseModule {

    #[Pure] public function __construct() {
        parent::__construct("Blink");
        //TODO: Nadpisać klase player i skopiować anty blinka
        $this->enabled = false;
    }
}