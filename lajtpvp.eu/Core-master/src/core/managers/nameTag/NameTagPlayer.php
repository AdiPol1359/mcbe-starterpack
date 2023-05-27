<?php

namespace core\managers\nameTag;

use core\guilds\Guild;
use core\Main;
use core\utils\WebhookUtil;
use core\permissions\group\Group;
use core\permissions\managers\FormatManager;
use core\permissions\managers\NameTagManager;
use core\users\User;
use core\utils\Settings;
use core\utils\RandomUtil;
use core\webhooks\types\Message;
use JetBrains\PhpStorm\Pure;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataTypes;
use pocketmine\player\Player;
use pocketmine\Server;

class NameTagPlayer {

    private string $nick;

    public function __construct(string $nick) {
        $this->nick = $nick;
    }

    public function updateNameTag() : void {
        if(!$this->getPlayer())
            return;

        foreach($this->getPlayer()->getViewers() as $onlinePlayer) {
            $nameTag = [];

            $canSeeName = true;
            $user = $this->getUser();
            $incognitoManager = $user->getIncognitoManager();

            if($user) {
                if($incognitoManager->getIncognitoData(Settings::$DATA_NAME))
                    $canSeeName = false;
            }

            if(($guild = $this->getGuild())) {
                if($guild->existsPlayer($onlinePlayer->getName()) || $guild->isAlliancePlayer($onlinePlayer->getName()))
                    $canSeeName = true;
            }

            if($onlinePlayer->hasPermission(Settings::$PERMISSION_TAG . "incognito.see"))
                $canSeeName = true;

            if($incognitoManager->getIncognitoData(Settings::$DATA_INCOGNITO_NAME) === "") {
                $incognitoManager->setIncognitoData(Settings::$DATA_INCOGNITO_NAME, ($randomName = RandomUtil::randomIncognitoName()));
                WebhookUtil::sendWebhook(new Message("**".$onlinePlayer->getName()."** » **".$randomName."**"), Settings::$INCOGNITO_WEBHOOK);
            }

            if(($group = $this->getGroup()) && $this->getPlayer()) {
                $nameTag[0] = NameTagManager::getNameTag($this->getPlayer()->getName(), (!$incognitoManager->getIncognitoData(Settings::$DATA_NAME) ? $this->nick : $incognitoManager->getIncognitoData(Settings::$DATA_INCOGNITO_NAME)));
            } else
                $nameTag[0] = "§7" . (!$incognitoManager->getIncognitoData(Settings::$DATA_NAME) ? $this->nick : $incognitoManager->getIncognitoData(Settings::$DATA_INCOGNITO_NAME));

            if($guild) {
                if($group) {
                    $canSee = true;

                    if($user) {
                        if($incognitoManager->getIncognitoData(Settings::$DATA_GUILD_TAG))
                            $canSee = false;
                    }

                    if($onlinePlayer->hasPermission(Settings::$PERMISSION_TAG . "incognito.see"))
                        $canSee = true;

                    if($guild->existsPlayer($onlinePlayer->getName()) || $guild->isAlliancePlayer($onlinePlayer->getName()))
                        $canSee = true;

                    $nameTag[0] = "[" . ($canSee ? $this->getGuild()->getTag() : RandomUtil::randomIncognitoName(4)) . "] " . $nameTag[0];
                }
            }

            if($incognitoManager->getIncognitoData(Settings::$DATA_NAME) && $canSeeName)
                $nameTag[0] .= " §8(§7" . $this->nick . "§8)";

            if($this->isVanished())
                $nameTag[0] .= " §8(§eV§8)";

            if(!$this->getPlayer())
                return;

            if($onlinePlayer->getName() === $this->nick)
                return;

            $this->getPlayer()->sendData([$onlinePlayer], [EntityMetadataProperties::NAMETAG => [EntityMetadataTypes::STRING, ($this->getGuild() ? $this->getGuild()->getColorForPlayer($onlinePlayer->getName()) : "") . ($this->getGuild() ? FormatManager::str_replace_specify("§7", "", implode("\n", $nameTag)) : implode("\n", $nameTag))]]);
        }
    }

    public function nameTagForPlayer(Player $player) : string {
        if(!$this->getPlayer())
            return "";

        $nameTag = [];

        $canSeeName = true;

        $user = $this->getUser();
        $incognitoManager = $user->getIncognitoManager();

        if($user) {
            if($incognitoManager->getIncognitoData(Settings::$DATA_NAME))
                $canSeeName = false;
        }

        if(($guild = $this->getGuild())) {
            if($guild->existsPlayer($player->getName()) || $guild->isAlliancePlayer($player->getName()))
                $canSeeName = true;
        }

        if($player->hasPermission(Settings::$PERMISSION_TAG . "incognito.see"))
            $canSeeName = true;

        if($incognitoManager->getIncognitoData(Settings::$DATA_INCOGNITO_NAME) === "") {
            $incognitoManager->setIncognitoData(Settings::$DATA_INCOGNITO_NAME, ($randomName = RandomUtil::randomIncognitoName()));
            WebhookUtil::sendWebhook(new Message("**".$player->getName()."** » **".$randomName."**"), Settings::$INCOGNITO_WEBHOOK);
        }

        if(($group = $this->getGroup()) && $this->getPlayer()) {
            $nameTag[0] = NameTagManager::getNameTag($this->getPlayer()->getName(), (!$incognitoManager->getIncognitoData(Settings::$DATA_NAME) ? $this->nick : $incognitoManager->getIncognitoData(Settings::$DATA_INCOGNITO_NAME)));
        } else
            $nameTag[0] = "§7" . (!$incognitoManager->getIncognitoData(Settings::$DATA_NAME) ? $this->nick : $incognitoManager->getIncognitoData(Settings::$DATA_INCOGNITO_NAME));

        if($guild) {
            if($group) {
                $canSee = true;

                if($user) {
                    if($incognitoManager->getIncognitoData(Settings::$DATA_GUILD_TAG))
                        $canSee = false;
                }

                if($player->hasPermission(Settings::$PERMISSION_TAG . "incognito.see"))
                    $canSee = true;

                if($guild->existsPlayer($player->getName()) || $guild->isAlliancePlayer($player->getName()))
                    $canSee = true;

                $nameTag[0] = "[" . ($canSee ? $this->getGuild()->getTag() : RandomUtil::randomIncognitoName(4)) . "] " . $nameTag[0];
            }
        }

        if($incognitoManager->getIncognitoData(Settings::$DATA_NAME) && $canSeeName)
            $nameTag[0] .= " §8(§7" . $this->nick . "§8)";

        if($this->isVanished())
            $nameTag[0] .= " §8(§eV§8)";

        if(!$this->getPlayer())
            return "";

        if($player->getName() === $this->nick)
            return $player->getName();

        return ($this->getGuild() ? $this->getGuild()->getColorForPlayer($player->getName()) : "") . ($this->getGuild() ? FormatManager::str_replace_specify("§7", "", implode("\n", $nameTag)) : implode("\n", $nameTag));
    }

    public function getName() : string {
        return $this->nick;
    }

    #[Pure] public function getGuild() : ?Guild {
        return Main::getInstance()->getGuildManager()->getPlayerGuild($this->nick);
    }

    #[Pure] public function getGroup() : ?Group {
        return Main::getInstance()->getPlayerGroupManager()->getPlayer($this->nick)->getGroup();
    }

    public function isVanished() : bool {
        if(($user = Main::getInstance()->getUserManager()->getUser($this->nick)))
            return $user->isVanished();

        return false;
    }

    public function getPlayer() : ?Player {
        return Server::getInstance()->getPlayerExact($this->nick);
    }

    public function getUser() : ?User {
        return Main::getInstance()->getUserManager()->getUser($this->nick);
    }
}