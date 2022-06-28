<?php

declare(strict_types=1);

namespace permissionex\managers;

use permissionex\Main;

class ChatManager {
	
	public static function setChatPerWorld(bool $status = true) : void {
		$settings = Main::getInstance()->getSettings();
		
		$settings->set("chat-per-world", $status);
		$settings->save();
	}
	
	public static function isChatPerWorld(bool $status = true) : bool {
		$settings = Main::getInstance()->getSettings();
		
		return (bool) $settings->get("chat-per-world");
	}
}