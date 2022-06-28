<?php

namespace _64FF00\PureChat;

use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\TextFormat;

class PureChat extends PluginBase
{
    /*
        PurePerms by 64FF00 (Twitter: @64FF00)

          888  888    .d8888b.      d8888  8888888888 8888888888 .d8888b.   .d8888b.
          888  888   d88P  Y88b    d8P888  888        888       d88P  Y88b d88P  Y88b
        888888888888 888          d8P 888  888        888       888    888 888    888
          888  888   888d888b.   d8P  888  8888888    8888888   888    888 888    888
          888  888   888P "Y88b d88   888  888        888       888    888 888    888
        888888888888 888    888 8888888888 888        888       888    888 888    888
          888  888   Y88b  d88P       888  888        888       Y88b  d88P Y88b  d88P
          888  888    "Y8888P"        888  888        888        "Y8888P"   "Y8888P"
    */

    private $purePerms, $factionsPro;
    
    public function onLoad()
    {
        $this->saveDefaultConfig();
        
        if($this->getConfig()->getNested("enable-multiworld-support"))
            $this->getLogger()->notice("Successfully enabled PureChat multiworld support");
    }
    
    public function onEnable()
    {
        $this->purePerms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
        $this->factionsPro = $this->getServer()->getPluginManager()->getPlugin("ohCode_Gildie");
        $this->elo = $this->getServer()->getPluginManager()->getPlugin("ohCode_Punkty");
		$this->gracz = $this->getServer()->getPluginManager()->getPlugin("ohCode_Core");
        $this->getServer()->getPluginManager()->registerEvents(new ChatListener($this), $this);
    }

    /**
     * @param $string
     * @return string
     */
    public function addColors($string)
    {
        $string = str_replace("{COLOR_BLACK}", TextFormat::BLACK, $string);
        $string = str_replace("{COLOR_DARK_BLUE}", TextFormat::DARK_BLUE, $string);
        $string = str_replace("{COLOR_DARK_GREEN}", TextFormat::DARK_GREEN, $string);
        $string = str_replace("{COLOR_DARK_AQUA}", TextFormat::DARK_AQUA, $string);
        $string = str_replace("{COLOR_DARK_RED}", TextFormat::DARK_RED, $string);
        $string = str_replace("{COLOR_DARK_PURPLE}", TextFormat::DARK_PURPLE, $string);
        $string = str_replace("{COLOR_GOLD}", TextFormat::GOLD, $string);
        $string = str_replace("{COLOR_GRAY}", TextFormat::GRAY, $string);
        $string = str_replace("{COLOR_DARK_GRAY}", TextFormat::DARK_GRAY, $string);
        $string = str_replace("{COLOR_BLUE}", TextFormat::BLUE, $string);
        $string = str_replace("{COLOR_GREEN}", TextFormat::GREEN, $string);
        $string = str_replace("{COLOR_AQUA}", TextFormat::AQUA, $string);
        $string = str_replace("{COLOR_RED}", TextFormat::RED, $string);
        $string = str_replace("{COLOR_LIGHT_PURPLE}", TextFormat::LIGHT_PURPLE, $string);
        $string = str_replace("{COLOR_YELLOW}", TextFormat::YELLOW, $string);
        $string = str_replace("{COLOR_WHITE}", TextFormat::WHITE, $string);

        $string = str_replace("{FORMAT_OBFUSCATED}", TextFormat::OBFUSCATED, $string);
        $string = str_replace("{FORMAT_BOLD}", TextFormat::BOLD, $string);
        $string = str_replace("{FORMAT_STRIKETHROUGH}", TextFormat::STRIKETHROUGH, $string);
        $string = str_replace("{FORMAT_UNDERLINE}", TextFormat::UNDERLINE, $string);
        $string = str_replace("{FORMAT_ITALIC}", TextFormat::ITALIC, $string);
        $string = str_replace("{FORMAT_RESET}", TextFormat::RESET, $string);

        return $string;
    }

    /**
     * @param Player $player
     * @param $message
     * @param null $levelName
     * @return string
     */
    public function getCustomChatFormat(Player $player, $message, $levelName = null)
    {
        $group = $this->purePerms->getUserDataMgr()->getGroup($player, $levelName);

        $groupName = $group->getName();

        if($levelName === null)
        {
            if($this->getConfig()->getNested("groups.$groupName.default-chat") === null)
            {
                $this->getConfig()->setNested("groups.$groupName.default-chat", "[$groupName] {display_name} > {message}");

                $this->saveConfig();
            }

            $chatFormat = $this->getConfig()->getNested("groups.$groupName.default-chat");
        }
        else
        {
            if($this->getConfig()->getNested("groups.$groupName.worlds.$levelName.default-chat") === null)
            {
                $this->getConfig()->setNested("groups.$groupName.worlds.$levelName.default-chat", "[$groupName] {display_name} > {message}");

                $this->saveConfig();
            }

            $chatFormat = $this->getConfig()->getNested("groups.$groupName.worlds.$levelName.default-chat");
        }

        if($this->factionsPro !== null)
        {
            if($this->getConfig()->getNested("custom-no-fac-message") === null)
            {
                $this->getConfig()->setNested("custom-no-fac-message", "...");

                $this->saveConfig();
            }

            if(!$this->factionsPro->isInFaction($player->getName()))
            {
                $chatFormat = str_replace("{faction}", $this->getConfig()->getNested("custom-no-fac-message"), $chatFormat);
            }

            if($this->factionsPro->isLeader($player->getName()))
            {
                $chatFormat = str_replace("{faction}", "" . $this->factionsPro->getPlayerFaction($player->getName()), $chatFormat);
            }
            elseif($this->factionsPro->isOfficer($player->getName()))
            {
                $chatFormat = str_replace("{faction}", "" . $this->factionsPro->getPlayerFaction($player->getName()), $chatFormat);
            }
            else
            {
                $chatFormat = str_replace("{faction}", "" . $this->factionsPro->getPlayerFaction($player->getName()), $chatFormat);
            }
        }
		$chatFormat = str_replace("{pkt}", "" . $this->elo->getElo($player->getName()), $chatFormat);
        $chatFormat = str_replace("{world_name}", $levelName, $chatFormat);
        $chatFormat = str_replace("{display_name}", $player->getDisplayName(), $chatFormat);
        $chatFormat = str_replace("{user_name}", $player->getName(), $chatFormat);

        $chatFormat = $this->addColors($chatFormat);

        if(!$player->hasPermission("pchat.colored.format")) $chatFormat = $this->removeColors($chatFormat);

        $message = $this->addColors($message);

        if(!$player->hasPermission("pchat.colored.chat")) $message = $this->removeColors($message);

        $chatFormat = str_replace("{message}", $message, $chatFormat);

        return $chatFormat;
    }

    public function getNameTag(Player $player, $levelName)
    {
        $group = $this->purePerms->getUserDataMgr()->getGroup($player, $levelName);

        $groupName = $group->getName();

        if($levelName === null)
        {
            if($this->getConfig()->getNested("groups.$groupName.default-nametag") === null)
            {
                $this->getConfig()->setNested("groups.$groupName.default-nametag", "[$groupName] {display_name}\n{pkt}");
            }

            $nameTag = $this->getConfig()->getNested("groups.$groupName.default-nametag");
        }
        else
        {
            if($this->getConfig()->getNested("groups.$groupName.worlds.$levelName.default-nametag") === null)
            {
                $this->getConfig()->setNested("groups.$groupName.worlds.$levelName.default-nametag", "[$groupName] {display_name}\n{pkt}");

                $this->getConfig()->save();
            }

            $nameTag = $this->getConfig()->getNested("groups.$groupName.worlds.$levelName.default-nametag");
        }

        if($this->factionsPro !== null)
        {
            if($this->getConfig()->getNested("custom-no-fac-message") === null)
            {
                $this->getConfig()->setNested("custom-no-fac-message", "...");

                $this->saveConfig();
            }

            if(!$this->factionsPro->isInFaction($player->getName()))
            {
                $nameTag = str_replace("{faction}", $this->getConfig()->getNested("custom-no-fac-message"), $nameTag);
            }

            if($this->factionsPro->isLeader($player->getName()))
            {
                $nameTag = str_replace("{faction}", "" . $this->factionsPro->getPlayerFaction($player->getName()), $nameTag);
            }
            elseif($this->factionsPro->isOfficer($player->getName()))
            {
                $nameTag = str_replace("{faction}", "" . $this->factionsPro->getPlayerFaction($player->getName()), $nameTag);
            }
            else
            {
                $nameTag = str_replace("{faction}", "" . $this->factionsPro->getPlayerFaction($player->getName()), $nameTag);
            }
        }
		$nameTag = str_replace("{pkt}", "" . $this->elo->getElo($player->getName()), $nameTag);
        $nameTag = str_replace("{world_name}", $levelName, $nameTag);
        $nameTag = str_replace("{display_name}", $player->getDisplayName(), $nameTag);
        $nameTag = str_replace("{user_name}", $player->getName(), $nameTag);

        $nameTag = $this->addColors($nameTag);

        if(!$player->hasPermission("pchat.colored.nametag")) $nameTag = $this->removeColors($nameTag);

        return $nameTag;
    }

    /**
     * @param $string
     * @return string
     */
    public function removeColors($string)
    {
        $string = str_replace(TextFormat::BLACK, '', $string);
        $string = str_replace(TextFormat::DARK_BLUE, '', $string);
        $string = str_replace(TextFormat::DARK_GREEN, '', $string);
        $string = str_replace(TextFormat::DARK_AQUA, '', $string);
        $string = str_replace(TextFormat::DARK_RED, '', $string);
        $string = str_replace(TextFormat::DARK_PURPLE, '', $string);
        $string = str_replace(TextFormat::GOLD, '', $string);
        $string = str_replace(TextFormat::GRAY, '', $string);
        $string = str_replace(TextFormat::DARK_GRAY, '', $string);
        $string = str_replace(TextFormat::BLUE, '', $string);
        $string = str_replace(TextFormat::GREEN, '', $string);
        $string = str_replace(TextFormat::AQUA, '', $string);
        $string = str_replace(TextFormat::RED, '', $string);
        $string = str_replace(TextFormat::LIGHT_PURPLE, '', $string);
        $string = str_replace(TextFormat::YELLOW, '', $string);
        $string = str_replace(TextFormat::WHITE, '', $string);

        $string = str_replace(TextFormat::OBFUSCATED, '', $string);
        $string = str_replace(TextFormat::BOLD, '', $string);
        $string = str_replace(TextFormat::STRIKETHROUGH, '', $string);
        $string = str_replace(TextFormat::UNDERLINE, '', $string);
        $string = str_replace(TextFormat::ITALIC, '', $string);
        $string = str_replace(TextFormat::RESET, '', $string);

        return $string;
    }
}