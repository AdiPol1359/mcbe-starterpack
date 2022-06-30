<?php

namespace core\form\forms;

use core\form\BaseForm;
use core\Main;
use core\manager\managers\WarpManager;
use core\task\tasks\TeleportTask;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\level\Position;
use pocketmine\Player;

class WarpForm extends BaseForm {

    public function __construct() {

        $data = [
            "type" => "form",
            "title" => "§l§9WARPY",
            "content" => "",
            "buttons" => []
        ];

        $result = Main::getDb()->query("SELECT * FROM warps");
        while($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data["buttons"][] = ["type" => "button", "text" => "§8§l» §9" . $row["name"] . " §8§l«§r\n§8Kliknij aby sie przeteleportowac"];
        }

        if(empty($data["buttons"]))
            $data["content"] = "§9Nie ma zadnych warpow!";

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        $warp = WarpManager::getWarpByIndex(intval($data));

        if($warp === null) {
            $player->sendForm(new Error($player, "Ten warp nie istnieje!", $this));
            return;
        }

        if(isset(Main::$teleportPlayers[$player->getName()])) {
            $player->sendMessage(MessageUtil::format("Jestes w trakcje teleportacji!"));
            return;
        }

        if($player->getLevel()->getName() === ConfigUtil::PVP_WORLD) {
            Main::$teleportPlayers[$player->getName()] = Main::getInstance()->getScheduler()->scheduleRepeatingTask(new TeleportTask($player->getName(), ConfigUtil::TELEPORT_TIME, WarpManager::getWarpPosition($warp)), 20);
            return;
        }

        $player->teleport(WarpManager::getWarpPosition($warp));
        $player->sendMessage(MessageUtil::format("Pomyslnie przeteleportowano na warp o nazwie §l§9$warp"));
    }
}