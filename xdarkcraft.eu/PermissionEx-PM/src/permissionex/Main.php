<?php

declare(strict_types=1);

namespace permissionex;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use permissionex\group\GroupManager;
use permissionex\provider\{
	Provider, SQLite3Provider
};
use permissionex\listeners\{
	JoinListener, QuitListener, ChatListener, LevelChangeListener, UpdateGroupListener
};
use permissionex\commands\{
	PexCommand
};
use permissionex\task\GroupsTask;

class Main extends PluginBase {
	
	public const VERSION = '1.0';
	
	private static $instance;
	
	private $groupManager;
	private $settings;
	private $provider;
	
	public static function getInstance() : Main {
		return self::$instance;
	}
	
	public static function format(string $str) : string {
		return "§8(§2PermissionEx§8) §7$str";
	}
	
	public static function getErrorMessage() : string {
		return "§cError in command syntax. Check command help.";
	}
	
	public static function getPermissionMessage() : string {
		return self::format("You don’t have permission!");
	}
	
	public function onEnable() : void {
		$this->init();
		$this->registerCommands();
		$this->registerEvents();
		$this->getScheduler()->scheduleRepeatingTask(new GroupsTask(), 20);
		$this->getLogger()->info("Plugin włączono");
	}
	
	public function getGroupManager() : GroupManager {
		return $this->groupManager;
	}
	
	public function getSettings() : Config {
		return $this->settings;
	}
	
	public function getProvider() : Provider {
		return $this->provider;
	}
	
	private function init() : void {
		$this->saveResource("settings.yml");
		
		self::$instance = $this;
		
		$this->settings = $settings = new Config($this->getDataFolder(). 'settings.yml', Config::YAML);
		$provider = null;
		
		switch(strtolower($settings->get("provider"))) {
			case "sqlite3":
			 $provider = new SQLite3Provider();
			break;
			
			case "mysql":
			 //TODO
			break;
			
			default:
			 $provider = new SQLite3Provider();
		}
		
		$this->provider = $provider;
		
		$this->groupManager = new GroupManager($provider);
	}
	
	private function registerCommands() : void {
		$cmds = [
		 new PexCommand()
		];
		
		$this->getServer()->getCommandMap()->registerAll("core", $cmds);
	}
	
	private function registerEvents() : void {
	 $listeners = [
		 new JoinListener(),
		 new QuitListener(),
		 new ChatListener(),
		 new LevelChangeListener(),
		 new UpdateGroupListener()
		];
		
		foreach($listeners as $listener)
		 $this->getServer()->getPluginManager()->registerEvents($listener, $this);
	}
	
	public function reload() : void {
		$this->settings = $settings = new Config($this->getDataFolder(). 'settings.yml', Config::YAML);
		
		$this->groupManager->reload();
	}
}