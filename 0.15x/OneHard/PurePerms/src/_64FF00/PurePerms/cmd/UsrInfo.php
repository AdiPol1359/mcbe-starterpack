<?php

namespace _64FF00\PurePerms\cmd;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\Player;

use pocketmine\utils\TextFormat;

class UsrInfo extends Command implements PluginIdentifiableCommand
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
        
        $this->setPermission("pperms.command.usrinfo");
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
        
        if(count($args) < 1 || count($args) > 2)
        {
            $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.usrinfo.usage"));
            
            return true;
        }
        
        $player = $this->plugin->getPlayer($args[0]);
        
        $levelName = null;
        
        if(isset($args[1]))
        {
            $level = $this->plugin->getServer()->getLevelByName($args[1]);
            
            if($level == null)
            {
                $sender->sendMessage(TextFormat::RED . "[PurePerms] " . $this->plugin->getMessage("cmds.usrinfo.messages.level_not_exist", $args[1]));
                
                return true;
            }
            
            $levelName = $level->getName();
        }
        
        $status = $player instanceof Player ? TextFormat::GREEN . $this->plugin->getMessage("cmds.usrinfo.messages.status_online") : TextFormat::RED . $this->plugin->getMessage("cmds.usrinfo.messages.status_offline");
        
        $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.usrinfo.messages.usrinfo_header", $player->getName()));

        $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.usrinfo.messages.usrinfo_status", $status));

        $ip = $player instanceof Player ? TextFormat::GREEN . $player->getAddress() : TextFormat::RED . $this->plugin->getMessage("cmds.usrinfo.messages.unknown");
        $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.usrinfo.messages.usrinfo_ip", $ip));

        $uuid = $player instanceof Player ? TextFormat::GREEN . $player->getUniqueId()->toString() : TextFormat::RED . $this->plugin->getMessage("cmds.usrinfo.messages.unknown");
        $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.usrinfo.messages.usrinfo_uuid", $uuid));

        $userGroup = $this->plugin->getUserDataMgr()->getGroup($player, $levelName);
        
        $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.usrinfo.messages.usrinfo_group", TextFormat::GREEN . $userGroup->getName()));
        
        return true;
    }
    
    public function getPlugin()
    {
        return $this->plugin;
    }
}