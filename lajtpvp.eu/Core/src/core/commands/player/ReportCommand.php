<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;

use core\Main;
use core\utils\BroadcastUtil;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use core\utils\TimeUtil;
use core\utils\WebhookUtil;
use core\webhooks\types\Embed;
use core\webhooks\types\Message;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class ReportCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("report", "", false, false, ["helpop"]);

        $parameters = [
            0 => [
                $this->commandParameter("wiadomosc", AvailableCommandsPacket::ARG_TYPE_STRING, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(empty($args)){
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["wiadomosc"]]));
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($sender->getName());

        if(!$user)
            return;

        if($user->hasLastData(Settings::$REPORT_DELAY)) {
            $sender->sendMessage(MessageUtil::format("Nastepne zgloszenie bedziesz mogl wyslac dopiero za ".TimeUtil::convertIntToStringTime(($user->getLastData(Settings::$REPORT_DELAY)["value"] - time()), "§e", "§7")));
            return;
        }

        $message = implode(" ", $args);

        $sender->sendMessage(MessageUtil::format("Wyslales zgloszenie!"));

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($sender, $message) : void {
            if(PermissionUtil::has($onlinePlayer, Settings::$PERMISSION_TAG."achat"))
                $onlinePlayer->sendMessage("§4[§cREPORT§4] §r§7".$sender->getName()." §8» §c" . $message);
        });

        WebhookUtil::sendWebhook(new Message("", new Embed("ZGLOSZENIE", "\nNick: **" . $sender->getName() . "**\n"."Zgloszenie: **" . $message. "**\n", null, true)), Settings::$HELPOP_WEBHOOK);

        $user->setLastData(Settings::$REPORT_DELAY, (time() + Settings::$REPORT_DELAY_TIME), Settings::$TIME_TYPE);
    }
}