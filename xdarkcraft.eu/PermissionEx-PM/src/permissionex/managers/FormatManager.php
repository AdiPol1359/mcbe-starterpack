<?php

declare(strict_types=1);

namespace permissionex\managers;

use pocketmine\Player;
use permissionex\Main;

class FormatManager {
	
	public static function getFormat(Player $player, string $format, ?string $message = null) : string {
        $group = Main::getInstance()->getGroupManager()->getPlayer($player->getName())->getGroup();

        $format = str_replace("&", "§", $format);
        $format = str_replace("{GROUP}", $group->getName(), $format);
        $format = str_replace("{DISPLAYNAME}", $player->getDisplayName(), $format);

        // CHAT MESSAGE
		if($message != null)
		 $format = str_replace("{MESSAGE}", $message, $format);

		$core_api = $player->getServer()->getPluginManager()->getPlugin("Core");
		$g_api = $player->getServer()->getPluginManager()->getPlugin("Gildie");

		if($core_api != null) {
            $format = str_replace("{PKT}", $core_api->getPointsAPI()->getPoints($player->getName()), $format);
        }

		if($g_api != null) {
		    $g = $g_api->getGuildManager()->getPlayerGuild($player->getName());

		    if($g != null)
                $format = str_replace("{GUILD}", $g->getTag(), $format);
		    else {
		        foreach(explode(" ", $format) as $word) {
                    if (strpos($word, "{GUILD}") != null) {
                        $format = str_replace(" ".$word, "", $format);
                    }
                }
            }
        }

		return $format;
	}
}