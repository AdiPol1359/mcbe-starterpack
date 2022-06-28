<?php

namespace _64FF00\PurePerms;

class PPGroup
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

    private $name, $plugin;

    /**
     * @param PurePerms $plugin
     * @param $name
     */
    public function __construct(PurePerms $plugin, $name)
    {
        $this->plugin = $plugin;
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->plugin->getProvider()->getGroupData($this);
    }

    /**
     * @return array
     */
    public function getInheritedGroups()
    {
        $inheritedGroups = [];
        
        if(!is_array($this->getNode("inheritance")))
        {
            $this->plugin->getLogger()->critical("Invalid 'inheritance' node given to " .  __METHOD__);
            
            return [];
        }
        
        foreach($this->getNode("inheritance") as $inheritedGroupName)
        {
            $inheritedGroup = $this->plugin->getGroup($inheritedGroupName);
            
            if($inheritedGroup != null) $inheritedGroups[] = $inheritedGroup;
        }
        
        return $inheritedGroups;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $node
     * @return null|mixed
     */
    public function getNode($node)
    {
        if(!isset($this->getData()[$node])) return null;
        
        return $this->getData()[$node];
    }

    /**
     * @param null $levelName
     * @return array|mixed
     */
    public function getGroupPermissions($levelName = null)
    {
        $permissions = $levelName != null ? $this->getWorldData($levelName)["permissions"] : $this->getNode("permissions");
        
        if(!is_array($permissions))
        {
            $this->plugin->getLogger()->critical("Invalid 'permissions' node given to " .  __METHOD__);
            
            return [];
        }

        /** @var PPGroup $inheritedGroup */
        foreach($this->getInheritedGroups() as $inheritedGroup)
        {
            $inheritedGroupPermissions = $inheritedGroup->getGroupPermissions($levelName);
            
            if($inheritedGroupPermissions === null) $inheritedGroupPermissions = [];
            
            $permissions = array_merge($permissions, $inheritedGroupPermissions);
        }
        
        return $permissions;
    }

    /**
     * @param null $levelName
     */
    public function getUsers($levelName = null)
    {
        // TODO
    }

    /**
     * @param $levelName
     * @return null
     */
    public function getWorldData($levelName)
    {
        if($levelName == null) return null;
        
        if(!isset($this->getData()["worlds"][$levelName]))
        {
            $tempGroupData = $this->getData();
            
            $tempGroupData["worlds"][$levelName] = [
                "permissions" => [
                ]
            ];
                
            $this->setData($tempGroupData);
        }
            
        return $this->getData()["worlds"][$levelName];
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return ($this->getNode("isDefault") == true);
    }

    /**
     * @param $node
     */
    public function removeNode($node)
    {
        $tempGroupData = $this->getData();
        
        if(isset($tempGroupData[$node]))
        {               
            unset($tempGroupData[$node]);   
            
            $this->setData($tempGroupData);
        }
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->plugin->getProvider()->setGroupData($this, $data);
    }
    
    public function setDefault()
    {
        $this->setNode("isDefault", true);
    }

    /**
     * @param $permission
     * @param null $levelName
     */
    public function setGroupPermission($permission, $levelName = null)
    {
        if($levelName == null)
        {
            $tempGroupData = $this->getData();
                    
            $tempGroupData["permissions"][] = $permission;
            
            $this->setData($tempGroupData);
        }
        else
        {
            $worldData = $this->getWorldData($levelName);
            
            $worldData["permissions"][] = $permission;
            
            $this->setWorldData($levelName, $worldData);
        }

        $this->plugin->updatePlayersInGroup($this);

        return true;
    }

    /**
     * @param $node
     * @param $value
     */
    public function setNode($node, $value)
    {
        $tempGroupData = $this->getData();
        
        $tempGroupData[$node] = $value;
            
        $this->setData($tempGroupData);
    }

    /**
     * @param $levelName
     * @param array $worldData
     */
    public function setWorldData($levelName, array $worldData)
    {
        if(isset($this->getData()["worlds"][$levelName]))
        {
            $tempGroupData = $this->getData();
            
            $tempGroupData["worlds"][$levelName] = $worldData;
                
            $this->setData($tempGroupData);
        }
    }

    public function sortPermissions()
    {
        $tempGroupData = $this->getData();
            
        if(isset($tempGroupData["permissions"]))
        {
            $tempGroupData["permissions"] = array_unique($tempGroupData["permissions"]);

            sort($tempGroupData["permissions"]);
        }
        
        $isMultiWorldPermsEnabled = $this->plugin->getConfigValue("enable-multiworld-perms");
        
        if($isMultiWorldPermsEnabled and isset($tempGroupData["worlds"]))
        {
            foreach($this->plugin->getServer()->getLevels() as $level)
            {
                $levelName = $level->getName();
                        
                if(isset($tempGroupData["worlds"][$levelName]))
                {
                    $tempGroupData["worlds"][$levelName]["permissions"] = array_unique($tempGroupData["worlds"][$levelName]["permissions"]);

                    sort($tempGroupData["worlds"][$levelName]["permissions"]);
                }
            }
        }
        
        $this->setData($tempGroupData);
    }

    /**
     * @param $permission
     * @param null $levelName
     * @return bool
     */
    public function unsetGroupPermission($permission, $levelName = null)
    {
        if($levelName == null)
        {
            $tempGroupData = $this->getData();
                    
            if(!in_array($permission, $tempGroupData["permissions"])) return false;

            $tempGroupData["permissions"] = array_diff($tempGroupData["permissions"], [$permission]);
            
            $this->setData($tempGroupData);
        }
        else
        {
            $worldData = $this->getWorldData($levelName);
            
            if(!in_array($permission, $worldData["permissions"])) return false;
            
            $worldData["permissions"] = array_diff($worldData["permissions"], [$permission]);
            
            $this->setWorldData($levelName, $worldData);
        }

        $this->plugin->updatePlayersInGroup($this);

        return true;
    }
}