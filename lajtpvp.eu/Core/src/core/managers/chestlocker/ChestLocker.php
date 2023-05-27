<?php

declare(strict_types=1);

namespace core\managers\chestlocker;

use core\guilds\GuildPlayer;
use core\Main;
use pocketmine\world\Position;
use pocketmine\player\Player;

class ChestLocker {

    private bool $removed = false;

    public function __construct(
        private int $id,
        private string $player,
        private int $face,
        private Position $position,
        private bool $isFromDataBase
    ) {}

    public function getId() : int {
        return $this->id;
    }

    public function getPlayer() : string {
        return $this->player;
    }

    public function getFace() : int {
        return $this->face;
    }

    public function getPosition() : Position {
        return $this->position;
    }

    public function getChestPosition(): Position {
        return $this->getPosition()->getSide($this->getFace() ^ 0x01);
    }

    public function isRemoved() : bool {
        return $this->removed;
    }

    public function isFromDataBase() : bool {
        return $this->isFromDataBase;
    }

    public function remove(bool $value = true) : void {
        $this->removed = $value;
    }

    public function canOpen(Player $player) : bool {
        $playerGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($this->getPlayer());

        if($player->getName() === $this->getPlayer() || $player->getServer()->isOp($player->getName()))
            return true;

        if($playerGuild) {
            if($playerGuild->existsPlayer($this->getPlayer())) {
                if(($guildPlayer = $playerGuild->getPlayer($player->getName())))
                    return $guildPlayer->getSetting(GuildPlayer::CHEST_LOCKER);
            } else
                return false;
        }

        return false;
    }
}