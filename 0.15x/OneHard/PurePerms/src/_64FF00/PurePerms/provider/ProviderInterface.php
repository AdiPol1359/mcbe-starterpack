<?php

namespace _64FF00\PurePerms\provider;

use _64FF00\PurePerms\PPGroup;

use pocketmine\IPlayer;

interface ProviderInterface
{
    /*
        PurePerms by 64FF00 (Twitter: @64FF00)

          888  888    .d8888b.      d8888  8888888888 8888888888 .d8888b.   .d8888b.
          888  888   d88P  Y88b    d8P888  888        888       d88P  Y88b d88P  Y88b
        888888888888 888          d8P 888  888        888       888    888 888    888
          888  888   888d888b.   d8P  888  8888888    8888888   888    888 888    888
          888  888   888P "Y88b d88   888  888        888       888    888 888    888
        888888888888 888    888 8888888888 888        888       888    888 888    888
          888  888   Y88b  d88P       888  888        888       Y88b  d88P Y88b  d88P
          888  888    "Y8888P"        888  888        888        "Y8888P"   "Y8888P"
    */

    public function getGroupData(PPGroup $group);

    public function getGroupsData();

    public function getPlayerData(IPlayer $player);

    public function setGroupData(PPGroup $group, array $tempGroupData);

    public function setGroupsData(array $tempGroupsData);

    public function setPlayerData(IPlayer $player, array $tempPlayerData);

    public function close();
}