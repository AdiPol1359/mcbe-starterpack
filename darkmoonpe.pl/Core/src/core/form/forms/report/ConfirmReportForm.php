<?php

namespace core\form\forms\report;

use core\form\BaseForm;
use core\manager\managers\WebhookManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use core\webhook\types\Embed;
use core\webhook\types\Message;
use pocketmine\Player;
use pocketmine\Server;

class ConfirmReportForm extends BaseForm {

    private string $administrators;
    private string $type;
    private string $reportMessage;

    public function __construct(string $reportMessage, string $type, string $administrators, string $important = "") {

        $data = [
            "type" => "modal",
            "title" => "§9§lPOTWIERDZENIE ZGLOSZENIA§8!",
            "content" => "§7Zgloszenie: §l§9".$reportMessage."§r\n"."§7Typ zgloszenia: §l§9".$type."\n\n"."§4WAZNE!"."\n"."§r§7".$important,
            "button1" => "§l§9Wyslij zgloszenie",
            "button2" => "§8Anuluj"
        ];

        $this->data = $data;

        $this->type = $type;
        $this->reportMessage = $reportMessage;
        $this->administrators = $administrators;
    }

    public function handleResponse(Player $player, $data) : void {
        if($data == 1) {

            $user = UserManager::getUser($player->getName());

            if(!$user)
                return;

            $player->sendMessage(MessageUtil::format("Zgloszenie zostalo wyslane!"));

            foreach(Server::getInstance()->getOnlinePlayers() as $p) {
                if($p->hasPermission(ConfigUtil::PERMISSION_TAG . "report") || $p->isOp()) {
                    $user->setLastReport();
                    $p->sendMessage("§l§8[§4HELPOP§8] §r§c".$player->getName().": §4".$this->reportMessage);
                    WebhookManager::sendWebhook(new Message("",
                        new Embed("Zgloszenie gracza **".$player->getName()."**",
                            "\nZgłoszenie: **".$this->reportMessage."**"."\n".
                            "Typ zgłoszenia: **".$this->type."**"."\n".
                            "swiat: **".$player->getLevel()->getName()."**"."\n".
                            "Kordynaty: x: **".$player->round()->x."**, y: **". $player->round()->y."**, z: **".$player->round()->z."**"."\n\n".
                            $this->administrators,
                            [],
                            true, "https://i.ibb.co/t2nhJfH/logoDM2.png", 0x000000)),
                        ConfigUtil::REPORT_WEBHOOK);
                    return;
                }
            }
        }
    }
}