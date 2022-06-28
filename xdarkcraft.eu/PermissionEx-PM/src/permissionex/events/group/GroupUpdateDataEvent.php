<?php

declare(strict_types=1);

namespace permissionex\events\group;

use permissionex\group\Group;

class GroupUpdateDataEvent extends GroupEvent {
	
	public function __construct(Group $group) {
		$this->group = $group;
	}
}