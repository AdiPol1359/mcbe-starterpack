<?php

namespace core\guilds;

use JetBrains\PhpStorm\Pure;

class GuildPlayer {

    public const LEADER = "Lider";
    public const OFFICER = "Oficer";
    public const MEMBER = "Czlonek";

    public const BLOCK_BREAK = "block_break";
    public const BEACON_BREAK = "beacon_break";
    public const BLOCK_PLACE = "block_place";
    public const TNT_PLACE = "tnt_place";
    public const INTERACT_CHEST = "interact_chest";
    public const INTERACT_FURNACE = "interact_furnace";
    public const INTERACT_BEACON = "interact_beacon";
    public const USE_CUSTOM_BLOCKS = "use_custom_blocks";
    public const ADD_PLAYER = "add_player";
    public const KICK_PLAYER = "kick_player";
    public const FRIENDLY_FIRE = "friendly_fire";
    public const TREASURY = "treasury";
    public const PANEL = "panel";
    public const REGENERATION = "regeneration";
    public const TELEPORT = "teleport";
    public const BATTLE = "battle";
    public const ALLIANCE = "alliance";
    public const ALLIANCE_PVP = "alliance_pvp";
    public const CHEST_LOCKER = "chest_locker";

    #[Pure] public function __construct(private string $nick, private string $rank, private string $guild, private array $settings = []) {
        if(empty($settings))
            $settings = $this->getDefaultSettings();

        $this->settings = $settings;
    }

    public function getName() : string {
        return $this->nick;
    }

    public function getGuildName() : string {
        return $this->guild;
    }

    public function getRank() : string {
        return $this->rank;
    }

    public function getSettings() : array {
        return $this->settings;
    }

    public function setAllSettings(bool $value) : void {
        foreach($this->settings as $setting => $status)
            $this->settings[$setting] = $value;
    }

    public function getSetting(string $name) : bool {
        return $this->settings[$name];
    }

    public function setRank(string $rankName) : void {
        $this->rank = $rankName;
    }

    public function setSetting(string $settingName, bool $value) : void {
        $this->settings[$settingName] = $value;
    }

    public function switchSetting(string $settingName) : void {
        $this->settings[$settingName] = $this->settings[$settingName] ? 0 : 1;
    }

    public function setDefaultSettings() : void {
        $this->settings = $this->getDefaultSettings();
    }

    public function getDefaultSettings() : array {
        return [
            self::BLOCK_BREAK => true,
            self::BEACON_BREAK => false,
            self::BLOCK_PLACE => true,
            self::TNT_PLACE => false,
            self::INTERACT_CHEST => true,
            self::INTERACT_FURNACE => true,
            self::INTERACT_BEACON => false,
            self::USE_CUSTOM_BLOCKS => false,
            self::ADD_PLAYER => false,
            self::KICK_PLAYER => false,
            self::FRIENDLY_FIRE => false,
            self::TREASURY => false,
            self::PANEL => false,
            self::REGENERATION => false,
            self::TELEPORT => false,
            self::BATTLE => false,
            self::ALLIANCE => false,
            self::ALLIANCE_PVP => false,
            self::CHEST_LOCKER => false
        ];
    }
}