<?php

declare(strict_types=1);

namespace Core\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use Core\Main;

abstract class CoreCommand extends Command {

    private $seePermission;
    private $usePermission = null;

    public function __construct(string $name, string $description, bool $usePerm = false, array $aliases= []) {
        $this->seePermission = $seePerm = "PolishHard.command.see";
        $this->setPermission($seePerm);

        if($usePerm)
            $this->usePermission = "PolishHard.command.".$name;

        parent::__construct($name, $description, null, $aliases);
    }

    public function getSeePermission() : string {
        return $this->seePermission;
    }

    public function getUsePermission() : ?string {
        return $this->usePermission;
    }

    public function canUse(CommandSender $sender) : bool {
        if($this->usePermission == null)
            return true;
        else {
            if(!$sender->hasPermission($this->usePermission)) {
                $sender->sendMessage(Main::formatLines(["Nie posiadasz §4permisji§7, aby uzyc tej komendy! §8(§4{$this->usePermission}§8)", "Wpisz §4/pomoc §7aby zobaczyc dostepne dla Ciebie komendy!"]));
                return false;
            }
            return true;
        }
    }
}