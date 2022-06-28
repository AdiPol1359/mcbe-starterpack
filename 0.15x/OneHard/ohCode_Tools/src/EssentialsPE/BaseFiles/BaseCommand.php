<?php
namespace EssentialsPE\BaseFiles;

use EssentialsPE\Loader;
use pocketmine\command\Command;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;

abstract class BaseCommand extends Command implements PluginIdentifiableCommand{
    /** @var Loader  */
    private $plugin;
    /** @var null|string */
    private $consoleUsageMessage = null;

    /**
     * @param Loader $plugin
     * @param string $name
     * @param string $description
     * @param null|string $usageMessage
     * @param bool|null|string $consoleUsageMessage
     * @param array $aliases
     */
    public function __construct(Loader $plugin, $name, $description = "", $usageMessage = null, $consoleUsageMessage = null, array $aliases = []){
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->plugin = $plugin;
        $this->consoleUsageMessage = $consoleUsageMessage;
    }

    /**
     * @return Loader
     */
    public final function getPlugin(){
        return $this->plugin;
    }

    /**
     * @return string
     */
    public function getConsoleUsage(){
        if($this->consoleUsageMessage === null){
            $message = "Usage: " . str_replace("[player]", "<player>", $this->getUsage());
        }elseif(!$this->consoleUsageMessage){
            $message = "[Error] Please run this command in-game";
        }else{
            $message = $this->consoleUsageMessage;
        }
        return TextFormat::RED . $message;
    }

    /**
     * @return string
     */
    public function getUsage(){
        return TextFormat::RED . parent::getUsage();
    }
}
