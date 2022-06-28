<?php

namespace _64FF00\PurePerms\provider;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\PPGroup;

use pocketmine\IPlayer;

class SQLite3Provider implements ProviderInterface
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
    
    private $db, $groupsData;

    /**
     * @param PurePerms $plugin
     */
    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;

        $this->db = new \SQLite3($this->plugin->getDataFolder() . "PurePerms.db");

        $db_query = stream_get_contents($this->plugin->getResource("sqlite3_deploy.sql"));

        $this->db->exec($db_query);

        $this->loadGroupsData();
    }

    /**
     * @param PPGroup $group
     * @return array
     */
    public function getGroupData(PPGroup $group)
    {
        $groupName = $group->getName();

        if(!isset($this->getGroupsData()[$groupName]) || !is_array($this->getGroupsData()[$groupName])) return [];

        return $this->getGroupsData()[$groupName];
    }

    /**
     * @return array
     */
    public function getGroupsData()
    {
        return $this->groupsData;
    }

    /**
     * @param IPlayer $player
     * @return array
     */
    public function getPlayerData(IPlayer $player)
    {
        $userName = $player->getName();

        $userData = [
            "userName" => $userName,
            "group" => $this->plugin->getDefaultGroup()->getName(),
            "permissions" => []
        ];

        $stmt01 = $this->db->prepare("
            SELECT userGroup, permissions
            FROM players
            WHERE userName = :userName;
        ");

        $stmt01->bindValue(":userName", $userName, SQLITE3_TEXT);

        $result01 = $stmt01->execute();

        if($result01 instanceof \SQLite3Result)
        {
            while($currentRow = $result01->fetchArray(SQLITE3_ASSOC))
            {
                $userData["group"] = $currentRow["userGroup"];
                $userData["permissions"] = $currentRow["permissions"] !== "" ? explode(",", $currentRow["permissions"]) : [];
            }
        }

        $result01->finalize();

        $stmt02 = $this->db->prepare("
            SELECT worldName, userGroup, permissions
            FROM players_mw
            WHERE userName = :userName;
        ");

        $stmt02->bindValue(":userName", $userName, SQLITE3_TEXT);

        $result02 = $stmt02->execute();

        if($result02 instanceof \SQLite3Result)
        {
            while($currentRow = $result02->fetchArray(SQLITE3_ASSOC))
            {
                $worldName = $currentRow["worldName"];
                $worldGroup = $currentRow["userGroup"];
                $worldPerms = explode(",", $currentRow["permissions"]);

                $userData["worlds"][$worldName]["group"] = $worldGroup;
                $userData["worlds"][$worldName]["permissions"] = $worldPerms;
            }
        }

        $result02->finalize();

        return $userData;
    }

    public function loadGroupsData()
    {
        $this->groupsData = [];

        $result01 = $this->db->query("
            SELECT groupName, isDefault, inheritance, permissions
            FROM groups;
        ");

        if($result01 instanceof \SQLite3Result)
        {
            while($currentRow = $result01->fetchArray(SQLITE3_ASSOC))
            {
                $groupName = $currentRow["groupName"];

                $this->groupsData[$groupName]["isDefault"] = $currentRow["isDefault"];
                $this->groupsData[$groupName]["inheritance"] = $currentRow["inheritance"] !== "" ? explode(",", $currentRow["inheritance"]) : [];
                $this->groupsData[$groupName]["permissions"] = explode(",", $currentRow["permissions"]);
            }
        }

        $result01->finalize();

        $result02 = $this->db->query("
            SELECT groupName, worldName, permissions
            FROM groups_mw;
        ");

        if($result02 instanceof \SQLite3Result)
        {
            while($currentRow = $result02->fetchArray(SQLITE3_ASSOC))
            {
                $groupName = $currentRow["groupName"];

                foreach($currentRow["worlds"] as $worldName => $worldPerms)
                {
                    $this->groupsData[$groupName]["worlds"][$worldName] = explode(",", $worldPerms);
                }
            }
        }

        $result02->finalize();
    }

    /**
     * @param $groupName
     */
    public function removeGroupData($groupName)
    {
        $stmt01 = $this->db->prepare("
            DELETE FROM groups WHERE groupName = :groupName;
        ");

        $stmt01->bindValue(":groupName", $groupName, SQLITE3_TEXT);

        $result01 = $stmt01->execute();

        $result01->finalize();

        $stmt02 = $this->db->prepare("
            DELETE OR IGNORE FROM groups_mw WHERE groupName = :groupName;
        ");

        $stmt02->bindValue(":groupName", $groupName, SQLITE3_TEXT);

        $result02 = $stmt02->execute();

        $result02->finalize();
    }

    /**
     * @param PPGroup $group
     * @param array $tempGroupData
     */
    public function setGroupData(PPGroup $group, array $tempGroupData)
    {
        $groupName = $group->getName();

        $this->updateGroupData($groupName, $tempGroupData);

        $this->loadGroupsData();
    }

    /**
     * @param array $tempGroupsData
     */
    public function setGroupsData(array $tempGroupsData)
    {
        $tempGroupData01 = array_diff_key($this->groupsData, $tempGroupsData);

        $tempGroupName01 = key($tempGroupData01);

        if($tempGroupData01 != []) $this->removeGroupData($tempGroupName01);

        foreach($tempGroupsData as $tempGroupName02 => $tempGroupData02)
        {
            $this->updateGroupData($tempGroupName02, $tempGroupData02);
        }

        $this->loadGroupsData();
    }

    /**
     * @param IPlayer $player
     * @param array $tempUserData
     */
    public function setPlayerData(IPlayer $player, array $tempUserData)
    {
        $userName = $player->getName();
        $userGroup = $this->plugin->getDefaultGroup()->getName();
        $permissions = "";

        if(isset($tempUserData["userName"])) $userName = $tempUserData["userName"];
        if(isset($tempUserData["group"])) $userGroup = $tempUserData["group"];
        if(isset($tempUserData["permissions"])) $permissions = implode(",", $tempUserData["permissions"]);

        $stmt01 = $this->db->prepare("
            INSERT OR REPLACE INTO players (userName, userGroup, permissions)
            VALUES (:userName, :userGroup, :permissions);
        ");

        $stmt01->bindValue(":userName", $userName, SQLITE3_TEXT);
        $stmt01->bindValue(":userGroup", $userGroup, SQLITE3_TEXT);
        $stmt01->bindValue(":permissions", $permissions, SQLITE3_TEXT);

        $result01 = $stmt01->execute();

        $result01->finalize();

        if(isset($tempUserData["worlds"]))
        {
            $stmt02 = $this->db->prepare("
                INSERT OR REPLACE INTO players_mw (userName, worldName, userGroup, permissions)
                VALUES (:userName, :worldName, :userGroup, :permissions);
            ");

            foreach($tempUserData["worlds"] as $worldName => $worldData)
            {
                $worldGroup = $this->plugin->getDefaultGroup()->getName();
                $worldPerms = "";

                if(isset($worldData["userGroup"])) $worldGroup = $worldData["userGroup"];
                if(isset($worldData["permissions"])) $worldPerms = $worldData["permissions"];

                $stmt02->bindValue(":userName", $userName, SQLITE3_TEXT);
                $stmt02->bindValue(":worldName", $worldName, SQLITE3_TEXT);
                $stmt02->bindValue(":userGroup", $worldGroup, SQLITE3_TEXT);
                $stmt02->bindValue(":permissions", $worldPerms, SQLITE3_TEXT);

                $result02 = $stmt02->execute();

                $result02->finalize();
            }
        }
    }

    /**
     * @param $groupName
     * @param $tempGroupData
     */
    public function updateGroupData($groupName, $tempGroupData)
    {
        $isDefault = false;
        $inheritance = "";
        $permissions = "";

        if(isset($tempGroupData["isDefault"])) $isDefault = $tempGroupData["isDefault"];
        if(isset($tempGroupData["inheritance"])) $inheritance = implode(",", $tempGroupData["inheritance"]);
        if(isset($tempGroupData["permissions"])) $permissions = implode(",", $tempGroupData["permissions"]);

        $stmt01 = $this->db->prepare("
            INSERT OR REPLACE INTO groups (groupName, isDefault, inheritance, permissions)
            VALUES (:groupName, :isDefault, :inheritance, :permissions);
        ");

        $stmt01->bindValue(":groupName", $groupName, SQLITE3_TEXT);
        $stmt01->bindValue(":isDefault", $isDefault, SQLITE3_INTEGER);
        $stmt01->bindValue(":inheritance", $inheritance, SQLITE3_TEXT);
        $stmt01->bindValue(":permissions", $permissions, SQLITE3_TEXT);

        $result01 = $stmt01->execute();

        $result01->finalize();

        if(isset($tempGroupData["worlds"]))
        {
            foreach($tempGroupData["worlds"] as $worldName => $worldPerms)
            {
                $stmt02 = $this->db->prepare("
                    INSERT OR REPLACE INTO groups_mw (groupName, worldName, permissions)
                    VALUES (:groupName, :worldName, :permissions);
                ");

                $stmt02->bindValue(":groupName", $groupName, SQLITE3_TEXT);
                $stmt02->bindValue(":worldName", $worldName, SQLITE3_TEXT);
                $stmt02->bindValue(":permissions", $worldPerms, SQLITE3_TEXT);

                $result02 = $stmt02->execute();

                $result02->finalize();
            }
        }
    }
    
    public function close()
    {
        $this->db->close();
    }
}