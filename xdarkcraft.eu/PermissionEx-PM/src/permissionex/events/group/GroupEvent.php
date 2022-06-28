<?php

declare(strict_types=1);

namespace permissionex\events\group;

use pocketmine\event\Event;
use permissionex\group\Group;

abstract class GroupEvent extends Event {
	
	protected $group;

	public function getGroup() : Group {
		return $this->group;
	}
}