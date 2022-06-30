<?php

namespace core\command;

use core\manager\managers\SoundManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\CommandEnum;
use pocketmine\network\mcpe\protocol\types\CommandParameter;
use pocketmine\Player;
use pocketmine\Server;

abstract class BaseCommand extends Command {

    private string $helpDescription;
    private string $label;
    private string $seePermission;
    private ?string $usePermission = null;

    private bool $canUseInConsole;
    private Server $server;

    public function __construct(string $name, string $description, bool $usePerm = false, bool $canUseInConsole = false, string $helpDescription = "", array $aliases = [], array $overloads = []) {

        $this->server = Server::getInstance();

        if($usePerm) {
            $this->seePermission = $seePerm = ConfigUtil::PERMISSION_TAG . "command.".$name;
            $this->setPermission($seePerm);
        }

        $this->canUseInConsole = $canUseInConsole;
        $this->helpDescription = $helpDescription;
        $this->label = $name;

        if($usePerm)
            $this->usePermission = ConfigUtil::PERMISSION_TAG . "command." . $name;

        parent::__construct($name, $description, null, $aliases, $overloads);
    }

    public function setOverLoads(array $overloads) : void {
        foreach($overloads as $key => $overload) {
            foreach($overload as $parameter)
                $this->addParameter($parameter, $key);
        }
    }

    public function commandParameter(string $name, int $type, bool $optional, ?string $enumName = null, ?array $enumValues = null) : CommandParameter {

        if($enumName !== null || $enumValues !== null) {
            $enumParameter = new CommandEnum();

            if($enumName !== null)
                $enumParameter->enumName = $name;

            if($enumValues !== null)
                $enumParameter->enumValues = $enumValues;
        }

        return new CommandParameter($name, $type, $optional, $enumParameter ?? null, 1);
    }

    public function getHelpDescription() : string {
        return $this->helpDescription;
    }

    public function getSeePermission() : string {
        return $this->seePermission;
    }

    public function getUsePermission() : ?string {
        return $this->usePermission;
    }

    public function getServer() : Server{
        return $this->server;
    }

    public function getCommandLabel() : string {
        return $this->label;
    }

    public function permissionMessage(string $permission) : array {
        return ["Nie posiadasz uprawnien, aby uzyc tej komendy! §8(§9§l" . $permission . "§r§8)", "Sprawdz liste komend pod §8(§9§l/pomoc§r§8)"];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if(!$this->canUseInConsole && !$sender instanceof Player) {
            $sender->sendMessage(MessageUtil::formatLines(["Nie mozesz uzyc tej komendy w konsoli!", "Wejdz do gry aby moc skorzystac z tej komendy"]));
            return;
        }

        if($this->usePermission !== null) {
            if(!$sender->hasPermission($this->usePermission)) {
                $sender->sendMessage(MessageUtil::formatLines($this->permissionMessage($this->usePermission)));
                SoundManager::addSound($sender, $sender->asVector3(), "block.false_permissions");
                return;
            }
        }

        $this->label = $commandLabel;
        $this->onCommand($sender, $args);
    }

    public function correctUse(string $command, array $usage) : string{

        $args = "";

        foreach($usage as $argument){
            $args .= "§8(";
            $argumentNames = "";
            foreach($argument as $values) {
                $argumentNames .= "§9" . $values;
                if(end($argument) !== $values)
                    $argumentNames .= "§7/";
            }

            $args .= $argumentNames."§8) ";
        }

        return MessageUtil::format("Poprawne uzycie komendy to: §9§l/".$command." ".$args);
    }

    public function selectPlayer(CommandSender $player, array $args, int $argument, bool $nick = false, bool $senderDefault = true) {
        $senderDefault ? $targetPlayer = $player->getName() : $targetPlayer = "";
        isset($args[$argument]) ? $targetPlayer = implode(" ", array_slice($args, $argument)) : null;
        return (!$nick ? (($p = $this->getServer()->getPlayerExact($targetPlayer)) ? $p : null) : $targetPlayer);
    }

    abstract public function onCommand(CommandSender $player, array $args) : void;
}