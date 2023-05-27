<?php

declare(strict_types=1);

namespace core\managers;

use pocketmine\Server;

class CommandLockManager {

    private array $locked = [];
    
    public function isLocked(string $commandName) : bool {
        $cmdMap = Server::getInstance()->getCommandMap();
        $founded = false;

        foreach($cmdMap->getCommands() as $serverCommand) {
            if($commandName !== $serverCommand->getName() && !in_array($commandName, $serverCommand->getAliases())) {
                continue;
            }

            if(in_array($serverCommand->getName(), $this->locked)) {
                $founded = true;
                break;
            }

            foreach($serverCommand->getAliases() as $alias) {
                if(in_array($alias, $this->locked)) {
                    $founded = true;
                    break;
                }
            }
        }

        return $founded;
    }

    public function lockCommand(string $commandName) : bool {
        if(self::isLocked($commandName))
            return false;

        $this->locked[] = $commandName;
        return true;
    }

    public function unLockCommand(string $commandName) : bool {
        if(!self::isLocked($commandName))
            return false;

        $cmdMap = Server::getInstance()->getCommandMap();
        $founded = false;

        foreach($cmdMap->getCommands() as $serverCommand) {
            if($commandName !== $serverCommand->getName() && !in_array($commandName, $serverCommand->getAliases())) {
                continue;
            }

            if(($key = array_search($serverCommand->getName(), $this->locked)) !== false) {
                unset($this->locked[$key]);
                $founded = true;
                break;
            }

            foreach($serverCommand->getAliases() as $alias) {
                if(($key = array_search($alias, $this->locked)) !== false) {
                    unset($this->locked[$key]);
                    $founded = true;
                    break;
                }
            }
        }

        return $founded;
    }

    public function getLockedCommands() : array {
        return $this->locked;
    }
}