<?php

declare(strict_types=1);

namespace core\commands;

use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use core\utils\SoundUtil;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\command\CommandData;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;

abstract class BaseCommand extends Command {

    private string $label;
    private string $seePermission;
    private ?string $usePermission = null;

    private CommandData $commandData;

    public function __construct(string $name, string $description, bool $usePerm = false, private bool $canUseInConsole = false, array $aliases = [], array $overloads = []) {

        if($usePerm) {
            $this->seePermission = $seePerm = Settings::$PERMISSION_TAG . "command.".$name;
            $this->setPermission($seePerm);
        }

        $this->label = $name;

        $this->commandData = new CommandData($name, $description, 0, 0, null, $overloads ?? [[new CommandParameter()]]);

        if($usePerm) {
            $this->usePermission = Settings::$PERMISSION_TAG . "command." . $name;
        }

        parent::__construct($name, $description, null, $aliases);
    }

    public function setPermission(?string $permission): void {
        $instance = PermissionManager::getInstance();

        if ($instance->getPermission($permission) === null) {
            PermissionManager::getInstance()->addPermission(new Permission($permission, ""));
        }

        parent::setPermission($permission);
    }

    public function getSeePermission() : string {
        return $this->seePermission;
    }

    public function getUsePermission() : ?string {
        return $this->usePermission;
    }

    public function getCommandLabel() : string {
        return $this->label;
    }

    public function setOverLoads(array $overloads) : void {
        foreach($overloads as $key => $overload) {
            foreach($overload as $parameter)
                $this->addParameter($parameter, $key);
        }
    }

    public function addParameter(CommandParameter $parameter, int $overloadIndex = 0) : void{
        $this->commandData->overloads[$overloadIndex][] = $parameter;
    }

    #[Pure] public function commandParameter(string $name, int $type, bool $optional, ?string $enumName = null, ?array $enumValues = null) : CommandParameter {
        if($enumName !== null || $enumValues !== null) {
            $enumParameter = new CommandEnum($name, $enumValues);
        }

        $commandParameter = new CommandParameter();
        $commandParameter->paramName = $name;
        $commandParameter->paramType = $type;
        $commandParameter->isOptional = $optional;
        $commandParameter->enum = $enumParameter ?? null;
        $commandParameter->flags = 1;

        return $commandParameter;
    }

    public function permissionMessage(string $permission) : array {
        return ["Nie posiadasz uprawnien, aby uzyc tej komendy! §8(§e" . $permission . "§r§8)", "Sprawdz liste komend pod §8(§e/pomoc§r§8)"];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if(!$this->canUseInConsole && !$sender instanceof Player) {
            $sender->sendMessage(MessageUtil::formatLines(["Nie mozesz uzyc tej komendy w konsoli!", "Wejdz do gry aby moc skorzystac z tej komendy"]));
            return;
        }

        if($this->usePermission !== null) {
            if (!PermissionUtil::has($sender, $this->usePermission)) {
                $sender->sendMessage(MessageUtil::formatLines($this->permissionMessage($this->usePermission)));
                SoundUtil::addSound([$sender], $sender->getPosition(), "blocks.false_permissions");
                return;
            }
        }

        $this->label = $commandLabel;

        $this->onCommand($sender, $args);
    }

    #[Pure] public function correctUse(string $command, array $usage) : string{
        $messages = [];

        foreach($usage as $description => $data) {
            $messages[] = "§8/§e".$command." ".implode(" ", $data)." §8-§7 ".$description;
        }

        return MessageUtil::formatLines($messages, strtoupper($this->getName()));
    }

    public function simpleCommandCorrectUse(string $command, array $usage) : string{

        $args = "";

        foreach($usage as $argument){
            $args .= "§8(";
            $argumentNames = "";
            foreach($argument as $values) {
                $argumentNames .= "§e" . $values;
                if(end($argument) !== $values)
                    $argumentNames .= "§7/";
            }

            $args .= $argumentNames."§8) ";
        }

        return MessageUtil::format("Uzyj: §e/".$command." ".$args);
    }

    public function selectPlayer(CommandSender $player, array $args, int $argument, bool $nick = false, bool $senderDefault = true) : Player|string|null {
        $senderDefault ? $targetPlayer = $player->getName() : $targetPlayer = "";
        isset($args[$argument]) ? $targetPlayer = implode(" ", array_slice($args, $argument)) : null;
        return (!$nick ? (($p = $player->getServer()->getPlayerExact($targetPlayer)) ? $p : null) : $targetPlayer);
    }

    #[Pure] public function getData() : CommandData{
        $data = clone $this->commandData;
        $aliasesData = $this->getAliases();
        $aliasesData[] = $this->getName();

        $data->aliases = new CommandEnum(ucfirst($this->getName()) . "Aliases", array_values($aliasesData));

        return $data;
    }

    public function testPermissionSilent(CommandSender $target, ?string $permission = null) : bool{
        $permission ??= $this->getPermission();
        if($permission === null or $permission === ""){
            return true;
        }

        foreach(explode(";", $permission) as $p){
            if(PermissionUtil::has($target, $p)){
                return true;
            }
        }

        return false;
    }

    abstract public function onCommand(CommandSender $sender, array $args) : void;
}