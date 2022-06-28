<?php

namespace _64FF00\PurePerms\data;

use _64FF00\PurePerms\PPGroup;
use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\event\PPGroupChangedEvent;

use pocketmine\IPlayer;

class UserDataManager
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

    /**
     * @param PurePerms $plugin
     */
    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param IPlayer $player
     * @return array
     */
    public function getData(IPlayer $player)
    {
        return $this->plugin->getProvider()->getPlayerData($player);
    }

    /**
     * @param IPlayer $player
     * @param null $levelName
     * @return PPGroup|null
     */
    public function getGroup(IPlayer $player, $levelName = null)
    {
        $groupName = $levelName != null ? $this->getWorldData($player, $levelName)["group"] : $this->getNode($player, "group");

        $group = $this->plugin->getGroup($groupName);

        return $group;
    }

    /**
     * @param IPlayer $player
     * @param $node
     * @return null|mixed
     */
    public function getNode(IPlayer $player, $node)
    {
        $userData = $this->getData($player);

        if(!isset($userData[$node])) return null;

        return $userData[$node];
    }

    /**
     * @param null $levelName
     * @return array
     */
    public function getUserPermissions(IPlayer $player, $levelName = null)
    {
        $permissions = $levelName != null ? $this->getWorldData($player, $levelName)["permissions"] : $this->getNode($player, "permissions");

        if(!is_array($permissions))
        {
            $this->plugin->getLogger()->critical("Invalid 'permissions' node given to " . __METHOD__);

            return [];
        }

        return $permissions;
    }

    /**
     * @param IPlayer $player
     * @param $levelName
     * @return array
     */
    public function getWorldData(IPlayer $player, $levelName)
    {
        if($levelName === null) $levelName = $this->plugin->getServer()->getDefaultLevel()->getName();

        if(!isset($this->getData($player)["worlds"][$levelName]))
        {
            $tempUserData = $this->getData($player);

            $tempUserData["worlds"][$levelName] = [
                "group" => $this->plugin->getDefaultGroup()->getName(),
                "permissions" => [
                ]
            ];

            $this->setData($tempUserData);
        }

        return $this->getData($player)["worlds"][$levelName];
    }

    public function removeNode(IPlayer $player, $node)
    {
        $tempUserData = $this->getData($player);

        if(isset($tempUserData[$node]))
        {
            unset($tempUserData[$node]);

            $this->setData($player, $tempUserData);
        }
    }

    /**
     * @param IPlayer $player
     * @param array $data
     */
    public function setData(IPlayer $player, array $data)
    {
        $this->plugin->getProvider()->setPlayerData($player, $data);
    }

    /**
     * @param IPlayer $player
     * @param PPGroup $group
     * @param $levelName
     */
    public function setGroup(IPlayer $player, PPGroup $group, $levelName)
    {
        if($levelName === null)
        {
            $this->setNode($player, "group", $group->getName());
        }
        else
        {
            $worldData = $this->getWorldData($levelName);

            $worldData["group"] = $group->getName();

            $this->setWorldData($player, $levelName, $worldData);
        }

        $event = new PPGroupChangedEvent($this->plugin, $player, $group);

        $this->plugin->getServer()->getPluginManager()->callEvent($event);
    }

    /**
     * @param IPlayer $player
     * @param $node
     * @param $value
     */
    public function setNode(IPlayer $player, $node, $value)
    {
        $tempUserData = $this->getData($player);

        $tempUserData[$node] = $value;

        $this->setData($player, $tempUserData);
    }

    /**
     * @param IPlayer $player
     * @param $permission
     * @param null $levelName
     */
    public function setUserPermission(IPlayer $player, $permission, $levelName = null)
    {
        if($levelName === null)
        {
            $tempUserData = $this->getData($player);

            $tempUserData["permissions"][] = $permission;

            $this->setData($player, $tempUserData);
        }
        else
        {
            $worldData = $this->getWorldData($player, $levelName);

            $worldData["permissions"][] = $permission;

            $this->setWorldData($player, $levelName, $worldData);
        }

        $this->plugin->updatePermissions($player, $levelName);
    }

    public function setWorldData(IPlayer $player, $levelName, array $worldData)
    {
        if(isset($this->getData($player)["worlds"][$levelName]))
        {
            $tempUserData = $this->getData($player);

            $tempUserData["worlds"][$levelName] = $worldData;

            $this->setData($player, $tempUserData);
        }
    }

    /**
     * @param IPlayer $player
     * @param $permission
     * @param null $levelName
     */
    public function unsetPermission(IPlayer $player, $permission, $levelName = null)
    {
        if($levelName === null)
        {
            $tempUserData = $this->getData($player);

            if(!in_array($permission, $tempUserData["permissions"])) return;

            $tempUserData["permissions"] = array_diff($tempUserData["permissions"], [$permission]);

            $this->setData($player, $tempUserData);
        }
        else
        {
            $worldData = $this->getWorldData($player, $levelName);

            if(!in_array($permission, $worldData["permissions"])) return;

            $worldData["permissions"] = array_diff($worldData["permissions"], [$permission]);

            $this->setWorldData($player, $levelName, $worldData);
        }

        $this->plugin->updatePermissions($player, $levelName);
    }
}