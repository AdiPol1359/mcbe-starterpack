<?php

namespace Core\item;

use pocketmine\item\ItemFactory;

class ItemManager {
	public static function init() : void {
	 ItemFactory::registerItem(new Bow(), true);
	 ItemFactory::registerItem(new GoldenApple(), true);
	}
}