<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\Main;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class SetHomeCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("sethome", "", false, false);

        $parameters = [
            0 => [
                $this->commandParameter("nazwa", AvailableCommandsPacket::ARG_TYPE_STRING, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(!$sender instanceof Player) {
            return;
        }

        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["nazwa"]]));
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($sender->getName());

        if(!$user)
            return;

        $homeName = implode(" ", $args);

        if(strlen($homeName) > 15 || strlen($homeName) <= 1) {
            $sender->sendMessage(MessageUtil::format("Nazwa home'a musi zawierac od §e2§7 do §e15 §7znakow!"));
            return;
        }

        if(!ctype_alnum($args[0])) {
            $sender->sendMessage(MessageUtil::format("Nazwa home'a moze zawierac wylacznie litery i cyfry"));
            return;
        }

        if($user->getHomeManager()->getHome($homeName)) {
            $sender->sendMessage(MessageUtil::format("Posiadasz juz home'a o takiej nazwie"));
            return;
        }

        if(($guilds = Main::getInstance()->getGuildManager()->getGuildFromPos($sender->getPosition())) !== null) {
            if(!$guilds->existsPlayer($sender->getName())) {
                $sender->sendMessage(MessageUtil::format("Nie mozesz ustawic home'a na terenie czyjejs gildii!"));
                return;
            }
        }

        if(($count = $this->getHomesCountForPlayer($sender)) > 0) {
            if(count($user->getHomeManager()->getHomes()) >= $count) {
                $sender->sendMessage(MessageUtil::format("Osiagnales limit home'ow twoj limit wynosi ".$count));
                return;
            }
        }

        $terrains = Main::getInstance()->getTerrainManager()->getTerrainsFromPos($sender->getPosition());

        foreach($terrains as $terrain) {
            if($terrain->getName() === Settings::$SPAWN_TERRAIN || $terrain->getName() === Settings::$PVP_TERRAIN) {
                $sender->sendMessage(MessageUtil::format("Nie mozesz ustawic home'a na terenie spawna!"));
                return;
            }
        }

        $sender->sendMessage(MessageUtil::format("Ustawiles home'a o nazwie §e".$homeName));
        $user->getHomeManager()->createHome($homeName, $sender->getPosition());
    }

    public function getHomesCountForPlayer(Player $sender) : int {
        $count = Settings::$HOME_LIMIT_PLAYER;

        if(PermissionUtil::has($sender, Settings::$PERMISSION_TAG."home.vip"))
            $count = Settings::$HOME_LIMIT_VIP;

        if(PermissionUtil::has($sender, Settings::$PERMISSION_TAG."home.svip"))
            $count = Settings::$HOME_LIMIT_SVIP;

        if(PermissionUtil::has($sender, Settings::$PERMISSION_TAG."home.sponsor"))
            $count = Settings::$HOME_LIMIT_SPONSOR;

        if(PermissionUtil::has($sender, Settings::$PERMISSION_TAG."home.nolimit"))
            $count = 0;

        return $count;
    }
}