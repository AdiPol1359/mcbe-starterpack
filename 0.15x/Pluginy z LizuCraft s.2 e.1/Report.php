<?php

/*
__PocketMine Plugin__
name=Report
version=1.0.6
author=ZacHack
class=Report
apiversion=10,11,12
*/

class Report implements Plugin{
	private $api, $message, $player;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
		$this->server = ServerAPI::request();
	}
	
	public function init(){
		$this->config = new Config($this->api->plugin->configPath($this)."Reports.yml", CONFIG_YAML, array(
			"Reports" => array(),
		));
		$this->settings = new Config($this->api->plugin->configPath($this)."Settings.yml", CONFIG_YAML, array(
			"Console Notice" => "on",
		));
		$this->notice = $this->settings->get("Console Notice");
		$this->api->console->register("report", "Report a player for the owner", array($this, "cmd"));
		$this->config = $this->api->plugin->readYAML($this->api->plugin->configPath($this) ."Reports.yml");
		$this->server->api->ban->cmdWhitelist("report");
	}
	
	public function cmd($cmd, $args, $issuer){
		switch($cmd){
			case "report":
				if($args[0] == ""){
					$output = "Usage: /report <gracz powud>";
				}else{
					$name = $issuer->username;
					$msg = implode(" ", $args);
					$this->config['Reports'][] = array($name, $msg);
					$output = "Wykona?es report";
					$this->api->plugin->writeYAML($this->api->plugin->configPath($this) ."Reports.yml", $this->config);
					if($this->notice == "on"){
						console("[Report] ".$issuer." Gracz reportowany");
						break;
					}else{
						break;
					}
				}
		}
		return $output;
	}
	public function __destruct(){}
}