<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\guilds\GuildPlayer;
use core\Main;
use core\utils\BroadcastUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use core\utils\TimeUtil;
use pocketmine\command\CommandSender;

class BattleCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("battle", "", false, false, ["walka"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(($guild = Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName())) === null) {
            $sender->sendMessage(MessageUtil::format("Nie znajdujesz sie w zadnej gildii!"));
            return;
        }

        if(!($guildPlayer = $guild->getPlayer($sender->getName())->getSetting(GuildPlayer::BATTLE))) {
            $sender->sendMessage(MessageUtil::format("Nie masz uprawnien aby to zrobic!"));
            return;
        }

        if(!$guild->canSendBattleMessage()) {
            $sender->sendMessage(MessageUtil::format("Nastepna informacje o walce bedziesz mogl wyslac dopiero za ".TimeUtil::convertIntToStringTime(($guild->getLastBattleMessage() - time()), "§e")));
            return;
        }

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($guild) : void {
            $onlinePlayer->sendMessage(MessageUtil::formatLines(["Gildia §e".$guild->getTag()."§7 zaprasza na walke!", "Koordynaty gildii §8X §e".$guild->getGuildHeart()->round()->getX()." §8Z §e".$guild->getGuildHeart()->round()->getZ()], "WALKA"));
        });

        $guild->setLastBattleMessage((time() + (Settings::$BATTLE_TIME)));
    }
}