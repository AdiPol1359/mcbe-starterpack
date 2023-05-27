<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use core\utils\TimeUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;
use pocketmine\scheduler\BulkCurlTask;
use pocketmine\scheduler\BulkCurlTaskOperation;
use pocketmine\timings\TimingsHandler;
use pocketmine\utils\InternetException;
use pocketmine\utils\InternetRequestResult;

class TimingsCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("timings", "", true, true, ["timing"]);

        $parameters = [
            0 => [
                $this->commandParameter("timingsOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "chatOptions", ["on", "off", "paste", "reset", "status"])
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["on", "off", "paste", "reset", "status"]]));
            return;
        }

        $mode = strtolower($args[0]);

        switch($mode) {
            case "on":
                if(TimingsHandler::isEnabled()){
                    $sender->sendMessage(MessageUtil::format("Timingi sa juz wlaczone!"));
                    return;
                }

                TimingsHandler::setEnabled(true);
                $sender->sendMessage(MessageUtil::format("Wlaczono timing!"));
                return;

            case "off":
                if(TimingsHandler::isEnabled()){
                    $sender->sendMessage(MessageUtil::format("Timingi sa wylaczone!"));
                    return;
                }

                TimingsHandler::setEnabled(false);
                $sender->sendMessage(MessageUtil::format("Wylaczono timing!"));
                return;

            case "status":
                $sender->sendMessage(MessageUtil::format("Aktualnie timingi sa ".(TimingsHandler::isEnabled() ? "§aWlaczone" : "§cWylaczone")."§7 i trwaja juz ".(!TimingsHandler::isEnabled() ? "§c0" : "§a".TimeUtil::convertIntToStringTime((int)round(time() - TimingsHandler::getStartTime()), "§e", "§7"))));
                return;

            case "reset":
                if(!TimingsHandler::isEnabled()){
                    $sender->sendMessage(MessageUtil::format("Timingi sa wylaczone!"));
                    return;
                }

                TimingsHandler::reload();
                $sender->sendMessage(MessageUtil::format("Zresetowano timingi!"));
                break;

            case "paste":
                if(!TimingsHandler::isEnabled()){
                    $sender->sendMessage(MessageUtil::format("Timingi sa wylaczone!"));
                    return;
                }

                $fileTimings = fopen("php://temp", "r+b");

                $lines = TimingsHandler::printTimings();
                foreach($lines as $line){
                    fwrite($fileTimings, $line . PHP_EOL);
                }

                fseek($fileTimings, 0);
                $data = [
                    "browser" => $agent = $sender->getServer()->getName() . " " . $sender->getServer()->getPocketMineVersion(),
                    "data" => $content = stream_get_contents($fileTimings)
                ];
                fclose($fileTimings);

                $host = $sender->getServer()->getConfigGroup()->getPropertyString("timings.host", "timings.pmmp.io");

                $sender->getServer()->getAsyncPool()->submitTask(new BulkCurlTask(
                    [new BulkCurlTaskOperation(
                        "https://$host?upload=true",
                        10,
                        [],
                        [
                            CURLOPT_HTTPHEADER => [
                                "User-Agent: $agent",
                                "Content-Type: application/x-www-form-urlencoded"
                            ],
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => http_build_query($data),
                            CURLOPT_AUTOREFERER => false,
                            CURLOPT_FOLLOWLOCATION => false
                        ]
                    )],
                    function(array $results) use ($sender, $host) : void{
                        /** @phpstan-var array<InternetRequestResult|InternetException> $results */
                        if($sender instanceof Player and !$sender->isOnline()){
                            return;
                        }
                        $result = $results[0];
                        if($result instanceof InternetException){
                            $sender->getServer()->getLogger()->logException($result);
                            return;
                        }
                        $response = json_decode($result->getBody(), true);
                        if(is_array($response) && isset($response["id"])){
                            $sender->sendMessage(MessageUtil::format("Wyniki timingu: §e"."https://" . $host . "/?id=" . $response["id"]));
                        }else{
                            $sender->sendMessage(MessageUtil::format("Doszlo do bledu podczas tworzenia timingu!"));
                        }
                    }
                ));
                break;

            default:
                $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                break;
        }
    }
}