<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\guilds\GuildPlayer;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;

class SetBaseCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("setbase", "", false, false, ["ustawbaze"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $senderGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName());

        if(!$senderGuild) {
            $sender->sendMessage(MessageUtil::format("Nie znajdujesz sie w zadnej gildii!"));
            return;
        }

        $senderGuildUser = $senderGuild->getPlayer($sender->getName());

        if($senderGuildUser->getRank() !== GuildPlayer::LEADER) {
            $sender->sendMessage(MessageUtil::format("Tylko lider moze ustawic baze gildii!"));
            return;
        }

        if(!$senderGuild->isInPlot($sender->getPosition())){
            $sender->sendMessage(MessageUtil::format("Baze gildii mozna ustawic wylacznie na jej terenie!"));
            return;
        }

        $senderGuild->setBase($sender->getPosition());
        $sender->sendMessage(MessageUtil::format("Ustawiono baze gildii!"));
    }
}