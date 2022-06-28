<?php

namespace _64FF00\PurePerms\cmd;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\utils\TextFormat;

class ListUPerms extends Command implements PluginIdentifiableCommand
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
     * @param $name
     * @param $description
     */
    public function __construct(PurePerms $plugin, $name, $description)
    {
        $this->plugin = $plugin;
        
        parent::__construct($name, $description);
        
        $this->setPermission("pperms.command.listuperms");
    }

    /**
     * @param CommandSender $sender
     * @param $label
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, $label, array $args)
    {
        if(!$this->testPermission($sender))
            return false;
        
        if(count($args) < 1 || count($args) > 3)
        {
            $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.listuperms.usage"));
            
            return true;
        }
        
        $player = $this->plugin->getPlayer($args[0]);
        
        $levelName = null;
        
        if(isset($args[2]))
        {
            $level = $this->plugin->getServer()->getLevelByName($args[2]);
            
            if($level == null)
            {
                $sender->sendMessage(TextFormat::RED . "[PurePerms] " . $this->plugin->getMessage("cmds.setgperm.messages.level_not_exist", $args[2]));
                
                return true;
            }
            
            $levelName = $level->getName();
        }
        
        $permissions = $this->plugin->getUserDataMgr()->getUserPermissions($player, $levelName);
        
        if(empty($permissions))
        {
            $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.listuperms.messages.no_user_perms", $player->getName()));
            
            return true;
        }
        
        $pageHeight = $sender instanceof ConsoleCommandSender ? 24 : 6;
                
        $chunkedPermissions = array_chunk($permissions, $pageHeight); 
        
        $maxPageNumber = count($chunkedPermissions);
        
        if(!isset($args[1]) || !is_numeric($args[1]) || $args[1] <= 0) 
        {
            $pageNumber = 1;
        }
        else if($args[1] > $maxPageNumber)
        {
            $pageNumber = $maxPageNumber;   
        }
        else 
        {
            $pageNumber = $args[1];
        }
        
        $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.listuperms.messages.user_perms_list", $player->getName(), $pageNumber, $maxPageNumber));
        
        foreach($chunkedPermissions[$pageNumber - 1] as $permission)
        {
            $sender->sendMessage(TextFormat::BLUE . "[PurePerms] - " . $permission);
        }
        
        return true;
    }
    
    public function getPlugin()
    {
        return $this->plugin;
    }
}