<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\forms\Confirmation;
use core\guilds\GuildPlayer;
use core\Main;
use core\managers\nameTag\NameTagPlayerManager;
use core\utils\BroadcastUtil;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class DeleteCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("delete", "", false, false, ["usun"]);
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
            $sender->sendMessage(MessageUtil::format("Tylko lider moze usunac gildie!"));
            return;
        }

        $sender->sendForm(new Confirmation("§ePOTWIERDZENIE", "§r§7Kilkajac guzik usun jestes swiadomy ze zostaje ona usunieta na stale!", "§cUSUN GILDIE", "§r§8Anuluj", function() use ($sender, $senderGuild) : void {

            BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($sender, $senderGuild) : void {
                $onlinePlayer->sendMessage(MessageUtil::format("Gildia §e". $senderGuild->getTag()." §8[§e".$senderGuild->getName()."§8] §7zostala usunieta przez §e".$sender->getName()));
            });

            if(($war = Main::getInstance()->getWarManager()->getWar($senderGuild->getTag())) !== null)
                $war->endWar(($war->getAttacker() === $senderGuild->getTag() ? $war->getAttacked() : $war->getAttacker()), true);

            $sender->sendMessage(MessageUtil::format("Poprawnie usunieto gildie!"));

            Main::getInstance()->getGuildManager()->deleteGuild($senderGuild->getTag());
            NameTagPlayerManager::updatePlayersAround($sender);
        }, function() : void {}));
    }
}