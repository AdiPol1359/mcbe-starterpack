<?php

namespace core\managers\bossbar\bossbars;

use core\managers\bossbar\Bossbar;
use core\managers\bossbar\BossbarColor;

class GuildTerrain extends Bossbar {

    private string $guildTag;

    public function __construct(string $guildTag = "") {
        $this->guildTag = $guildTag;
        parent::__construct("", 1, "", BossbarColor::COLOR_YELLOW);
    }

    public function getGuildTag() : string {
        return $this->guildTag;
    }
}