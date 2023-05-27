<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\entity\Location;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class TeleportCommand extends BaseCommand {

    public const MAX_COORD = 30000000;
    public const MIN_COORD = -30000000;

    public function __construct() {
        parent::__construct("teleport", "", true, true, ["tp"]);

        $parameters = [
            0 => [
                $this->commandParameter("teleportPlayer", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ],
        ];

        $this->setOverLoads($parameters);
    }

    private function findPlayer(CommandSender $player, string $playerName) : ?Player{
        $subject = $player->getServer()->getPlayerByPrefix($playerName);

        if($subject === null){
            $player->sendMessage(MessageUtil::format("Nie mozna odnalezc tego gracza!"));
            return null;
        }

        return $subject;
    }
    
    public function onCommand(CommandSender $sender, array $args) : void {

        switch(count($args)){
            case 1:
            case 3:
            case 5:
                if(!($sender instanceof Player)){
                    $sender->sendMessage(MessageUtil::format("Podaj nick gracza!"));
                    return;
                }

                $subject = $sender;
                $targetArgs = $args;
                break;
            case 2:
            case 4:
            case 6:
                $subject = $this->findPlayer($sender, $args[0]);

                if($subject === null)
                    return;

                $targetArgs = $args;
                array_shift($targetArgs);
                break;
            default:
                $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Teleportuje ciebie do danego gracza" => ["§8(§enick§8)"], "Teleportuje wybranego gracza do innego" => ["§8(§enick§8)", "§8(§enick§8)"], "Teleportuje gracza w dane koordynaty" => ["§8(§enick§8)", "§8(§ex, y, z§8)"]]));
                return;
        }

        switch(count($targetArgs)){
            case 1:

                $targetPlayer = $this->findPlayer($sender, $targetArgs[0]);

                if($targetPlayer === null)
                    return;

                $subject->teleport($targetPlayer->getLocation());
                $sender->sendMessage(MessageUtil::format("Poprawnie przeteleportowano gracza §e".$subject->getName()."§7 do gracza §e".$targetPlayer->getName()."§7!"));
                AdminManager::sendMessage($sender, $sender->getName() . " przteleportowal ".$subject->getName()." do ".$targetPlayer->getName());

                return;
            case 3:
            case 5:
                $base = $subject->getLocation();
                if(count($targetArgs) === 5){
                    $yaw = (float) $targetArgs[3];
                    $pitch = (float) $targetArgs[4];
                }else{
                    $yaw = $base->yaw;
                    $pitch = $base->pitch;
                }

                $x = $this->getRelativeDouble($base->x, $targetArgs[0]);
                $y = $this->getRelativeDouble($base->y, $targetArgs[1], 0, 256);
                $z = $this->getRelativeDouble($base->z, $targetArgs[2]);
                $targetLocation = new Location($x, $y, $z, $base->getWorld(), $yaw, $pitch);

                $subject->teleport($targetLocation);

                AdminManager::sendMessage($sender, $sender->getName() . " przteleportowal ".$subject->getName()." w koordynaty ".round($targetLocation->x, 2)." ".round($targetLocation->y, 2) . " " . round($targetLocation->z, 2));
                $sender->sendMessage(MessageUtil::format("Przeteleportowano §e".$subject->getName()."§7 w koordynaty §e".round($targetLocation->x, 2)." ".round($targetLocation->y, 2) . " " . round($targetLocation->z, 2)));
                return;
            default:
        }
    }

    #[Pure] protected function getRelativeDouble(float $original, string $input, float $min = self::MIN_COORD, float $max = self::MAX_COORD) : float{
        if($input[0] === "~"){
            $value = $this->getDouble(substr($input, 1));

            return $original + $value;
        }

        return $this->getDouble($input, $min, $max);
    }

    protected function getDouble($value, float $min = self::MIN_COORD, float $max = self::MAX_COORD) : float{
        $i = (double) $value;

        if($i < $min){
            $i = $min;
        }elseif($i > $max){
            $i = $max;
        }

        return $i;
    }
}