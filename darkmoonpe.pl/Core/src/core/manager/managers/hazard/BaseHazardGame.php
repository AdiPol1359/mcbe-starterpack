<?php

namespace core\manager\managers\hazard;

interface BaseHazardGame {

    public static function init() : void;

    public static function getName() : string;

    public static function getTime() : int;

    public static function setTime(int $time) : void;

    public static function hasGameStarted() : bool;

    public static function setStartGame(bool $start) : void;

    public static function isLocked() : bool;

    public static function setLock(bool $lockStatus) : void;
}