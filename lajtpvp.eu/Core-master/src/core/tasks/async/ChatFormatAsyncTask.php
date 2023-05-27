<?php

declare(strict_types=1);

namespace core\tasks\async;

use core\Main;
use core\permissions\group\Group;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class ChatFormatAsyncTask extends AsyncTask {

    private array $recipients;
    private int $points;
    private array $guildPlayers;
    private array $guildAlliances;
    private string $guildTag;
    private Group $playerGroup;
    private string $playerName;
    private string $format;
    private ?string $message;
    private bool $hasPermission;

    private array $resultFormats;

    public function __construct(array $recipients, int $points, array $guildPlayers, array $guildAlliances, string $guildTag, Group $playerGroup, string $playerName, string $format, ?string $message = null, bool $hasPermission = false) {
        $this->recipients = $recipients;
        $this->points = $points;
        $this->guildPlayers = $guildPlayers;
        $this->guildAlliances = $guildAlliances;
        $this->guildTag = $guildTag;
        $this->playerGroup = $playerGroup;
        $this->playerName = $playerName;
        $this->format = $format;
        $this->message = $message;
        $this->hasPermission = $hasPermission;
    }

    public function onRun() : void {
        $formats = [];
        $guild = null;
        $user = null;

        foreach($this->recipients as $recipient) {
            $data = [];

            $recipientFormat = $this->format;

            $recipientFormat = str_replace("&", "§", $recipientFormat);
            $recipientFormat = str_replace("{GROUP}", $this->playerGroup->getGroupName(), $recipientFormat);
            $recipientFormat = str_replace("{DISPLAYNAME}", $this->playerName, $recipientFormat);

            if($this->message != null)
                $recipientFormat = str_replace("{MESSAGE}", $this->message, $recipientFormat);

            $data[] = "§8[§7" . $this->points . "§8]";

            $recipientFormat = str_replace("{GROUP}", $this->playerGroup->getGroupName(), $recipientFormat);
            $recipientFormat = str_replace("{DISPLAYNAME}", $this->playerName, $recipientFormat);

            if(!empty($this->guildPlayers)) {
                $guildFormat = "§c";

                if((array)$this->guildPlayers[$recipient])
                    $guildFormat = "§a";

                if((array)$this->guildAlliances[$recipient])
                    $guildFormat = "§6";

                $data[] = "§8[" . $guildFormat . $this->guildTag . "§8]";
            }

            $recipientFormat = str_replace("{DATA}", implode(" ", $data), $recipientFormat);
            $formats[$recipient] = $recipientFormat;
        }

        $this->resultFormats = $formats;
    }

    public function onCompletion() : void {
        foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if(!isset($this->resultFormats[$onlinePlayer->getName()]))
                continue;

            if(!$this->hasPermission) {
                if(($user = Main::getInstance()->getUserManager()->getUser($onlinePlayer->getName()))) {
                    if($user->getIgnoreManager()->isIgnoring($this->playerName))
                        continue;
                }
            }

            $onlinePlayer->sendMessage($this->resultFormats[$onlinePlayer->getName()]);
        }
    }
}