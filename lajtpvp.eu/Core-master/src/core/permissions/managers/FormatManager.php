<?php

declare(strict_types=1);

namespace core\permissions\managers;

use core\Main;
use core\utils\Settings;
use pocketmine\player\Player;

class FormatManager {

    public static function getFormat(Player $player, string $format, ?string $message = null) : string {
        $group = Main::getInstance()->getPlayerGroupManager()->getPlayer($player->getName())->getGroup();
        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        $format = str_replace("&", "§", $format);
        $format = str_replace("{GROUP}", $group->getGroupName(), $format);
        $format = str_replace("{DISPLAYNAME}", $player->getDisplayName(), $format);

        if($message != null)
            $format = str_replace("{MESSAGE}", $message, $format);

        $guild = Main::getInstance()->getGuildManager()->getPlayerGuild($player->getName());

        $data = [];
        $data[] = "§8[§7" . $user->getStatManager()->getStat(Settings::$STAT_POINTS) . "§8]";

        if($guild)
            $data[] = "§8[§e" . $guild->getTag() . "§8]";

        $format = str_replace("{GROUP}", $group->getGroupName(), $format);
        $format = str_replace("{DISPLAYNAME}", $player->getDisplayName(), $format);
        $format = str_replace("{MESSAGE}", $message, $format);

        return str_replace("{DATA}", implode(" ", $data), $format);
    }

    public static function getNameTagFormat(string $nick, string $fakeName, string $format, ?string $message = "") : string {
        $group = Main::getInstance()->getPlayerGroupManager()->getPlayer($nick)->getGroup();

        $format = str_replace("&", "§", $format);
        $format = str_replace("{GROUP}", $group->getGroupName(), $format);
        $format = str_replace("{DISPLAYNAME}", $fakeName, $format);

        if($message != "")
            $format = str_replace("{MESSAGE}", $message, $format);

        $format = str_replace("{GROUP}", $group->getGroupName(), $format);
        return str_replace("{DISPLAYNAME}", $fakeName, $format);
    }

    static function str_replace_specify(string $from, string $to, string $content) : array|string|null {
        $from = '/' . preg_quote($from, '/') . '/';

        return preg_replace($from, $to, $content, 1);
    }

    public static function guildFormatMessage(string $format, array $dataInfo = [], array $recipients = []) : array {

        $formats = [];

        foreach($recipients as $recipient) {
            if(!$recipient instanceof Player)
                continue;

            if($recipient->getWorld()->getDisplayName() === Settings::$LOBBY_WORLD)
                continue;

            $cloneData = $dataInfo;

            $recipientFormat = $format;

            foreach($cloneData as $key => $data) {
                if($data === "")
                    continue;

                $guild = Main::getInstance()->getGuildManager()->getGuild($data);

                if($guild)
                    $recipientFormat = self::str_replace_specify("{TAG}", "§8[" . $guild->getColorForPlayer($recipient->getName()) . $guild->getTag() . "§8]", $recipientFormat);
            }

            $formats[$recipient->getName()] = $recipientFormat;
        }

        return $formats;
    }
}