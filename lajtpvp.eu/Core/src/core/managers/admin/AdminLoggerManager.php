<?php

declare(strict_types=1);

namespace core\managers\admin;

use core\Main;
use core\utils\Settings;
use JetBrains\PhpStorm\Pure;

class AdminLoggerManager {

    /** @var Admin[] */
    private array $admins = [];

    public function __construct(private Main $plugin) {
        $this->loadAdmins();
    }

    public function loadAdmins() : void {
        $provider = $this->plugin->getProvider();

        foreach($provider->getQueryResult("SELECT * FROM admins", true) as $row) {
            $this->admins[] = new Admin($row["nick"], (int)$row["spendTime"], (int)$row["messages"], (int)$row["bans"], (int)$row["mutes"]);
        }
    }
    public function save() : void {
        $provider = $this->plugin->getProvider();

        foreach($this->admins as $admin) {
            $adminUser = $this->plugin->getUserManager()->getUser($admin->getName());

            if(!$adminUser)
                return;

            $statManager = $adminUser->getStatManager();

            $admin->addTime((time() - $statManager->getStat(Settings::$STAT_LAST_JOIN_TIME)));

            if(!empty($provider->getQueryResult("SELECT * FROM admins WHERE nick = '".$admin->getName()."'", true))) {
                $provider->getQueryResult("UPDATE admins SET spendTime = '" . $admin->getSpendTime() . "', messages = '" . $admin->getMessages() . "', bans = '" . $admin->getBans() . "', mutes = '" . $admin->getMutes() . "' WHERE nick = '" . $admin->getName() . "'");
            } else {
                $provider->getQueryResult("INSERT INTO admins (nick, spendTime, messages, bans, mutes) VALUES ('" . $admin->getName() . "', '" . $admin->getSpendTime() . "', '" . $admin->getMessages() . "', '" . $admin->getBans() . "', '" . $admin->getMutes() . "')");
            }
        }
    }

    public function createAdminData(string $nick) : void {
        $this->admins[] = new Admin($nick, 0, 0, 0, 0);
    }

    #[Pure] public function getAdminDataByName(string $nick) : ?Admin {
        foreach($this->admins as $admin) {
            if($admin->getName() === $nick)
                return $admin;
        }

        return null;
    }
}