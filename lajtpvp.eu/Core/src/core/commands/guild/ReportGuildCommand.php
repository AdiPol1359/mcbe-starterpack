<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\Main;
use core\utils\Settings;
use core\utils\MessageUtil;
use core\utils\WebhookUtil;
use core\webhooks\types\Embed;
use core\webhooks\types\Message;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class ReportGuildCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("reportguild", "", false, false, ["zglos", "zglosgildie"]);

        $parameters = [
            0 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true))
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $position = $sender->getPosition();

        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["gildia", "powod"]]));
            return;
        }

        if(($guild = Main::getInstance()->getGuildManager()->getGuild($args[0])) === null) {
            $sender->sendMessage(MessageUtil::format("Gildia o takim tagu nie istnieje!"));
            return;
        }

        $arr = $args;
        array_shift($arr);

        $reason = "BRAK";
        if(!empty($arr))
            $reason = implode(" ", $arr);

        $sender->sendMessage(MessageUtil::format("Gildia zostala poprawnie zgloszona!"));

        WebhookUtil::sendWebhook(new Message("", new Embed("ZGŁOSZONO GILDIE",
            "\nTag: **" . $args[1] . "**\n"."Nazwa: **" . $guild->getName() . "**\n"."Przez: **" . $sender->getName() . "**\n"."Powod: **" . $reason . "**\n"."Koordynaty: **" . $position->getFloorX() . " " . $position->getFloorY() . " " . $position->getFloorZ()."**", null, true)), Settings::$GUILD_REPORT_WEBHOOK);
    }
}