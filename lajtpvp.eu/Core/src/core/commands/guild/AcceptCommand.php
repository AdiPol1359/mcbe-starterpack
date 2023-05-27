<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\Main;
use core\managers\nameTag\NameTagPlayerManager;
use core\utils\BroadcastUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class AcceptCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("accept", "", false, false, ["akceptuj", "dolacz", "accept"]);

        $parameters = [
            0 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }
        
        if(!($user = Main::getInstance()->getUserManager()->getUser($sender->getName())))
            return;

        $senderGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName());

        if($senderGuild) {
            $sender->sendMessage(MessageUtil::format("Znajdujesz sie juz w jednej gildii!"));
            return;
        }

        if(empty($args)) {

            $invites = [];

            foreach($user->getInvites() as $tag => $time) {
                if($user->hasInvite($tag))
                    $invites[] = $tag;
            }

            if(empty($invites))
                $sender->sendMessage(MessageUtil::format("Nie masz zaproszenia do zadnej gildii!"));
            else
                $sender->sendMessage(MessageUtil::format("Twoje zaproszenia: §e" . implode("§7, §e", $invites) . "§7!"));

            return;
        }

        $tag = implode(" ", $args);

        if(!$user->hasInvite($tag)) {
            $sender->sendMessage(MessageUtil::format("Nie otrzymales zaproszenia od takiej gildii!"));
            return;
        }

        $guild = Main::getInstance()->getGuildManager()->getGuild($tag);

        if(!$guild) {
            $sender->sendMessage(MessageUtil::format("Gildia o takim tagu nie istnieje!"));
            return;
        }

        if(count($guild->getPlayers()) >= Settings::$GUILD_MEMBERS_LIMIT || count($guild->getPlayers()) >= $guild->getSlots()) {
            $sender->sendMessage(MessageUtil::format("Ta gildia osiagnela limit czlonkow!"));
            return;
        }

        $user->removeInvite($tag);
        $guild->addPlayer($sender->getName());

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($tag, $sender) : void {
            $onlinePlayer->sendMessage(MessageUtil::format("Gracz §e" . $sender->getName() . "§7 dolaczyl do gildii §e" . $tag . "§7!"));
        });

        NameTagPlayerManager::updatePlayersAround($sender);
    }
}