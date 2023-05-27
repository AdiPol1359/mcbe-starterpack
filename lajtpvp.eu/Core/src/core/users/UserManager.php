<?php

declare(strict_types=1);

namespace core\users;

use core\Main;
use JetBrains\PhpStorm\Pure;
use pocketmine\player\PlayerInfo;
use Ramsey\Uuid\Uuid;

class UserManager {

    /** @var User[] */
    private array $users = [];

    public function __construct(private Main $plugin) {}
    
    public function createUser(PlayerInfo $player) : void {
        $deviceId = $player->getExtraData()["DeviceId"];
        $this->users[$player->getUsername()] = new User($player->getUsername(), $player->getUuid()->toString(), ($deviceId === null ? UUID::uuid4()->toString() : $deviceId));
    }

    public function getUser(string $player) : ?User {
        return $this->users[$player] ?? null;
    }

    public function save() : void {
        foreach($this->users as $user) {
            try {
                $user->save();

                $name = $user->getName();
                $uuid = $user->getUUID();
                $deviceId = $user->getDeviceId();

                if(empty(Main::getInstance()->getProvider()->getQueryResult("SELECT * FROM 'users' WHERE userName = '$name'", true))) {
                    Main::getInstance()->getProvider()->executeQuery("INSERT INTO 'users' (userName, uuid, deviceId) VALUES ('$name', '$uuid', '$deviceId')");
                }
            } catch(\PDOException $exception) {
                $this->plugin->getLogger()->error("Doszlo do bledu podczas zapisywania usera " . $user->getName() . " (".$user->getDeviceId()."): " . $exception);
            }
        }
    }

    public function loadAllUsers() : void {
        $players = [];

        foreach($this->plugin->getProvider()->getQueryResult("SELECT * FROM 'users'", true) as $data) {
            if(is_string($data["userName"]) && is_string($data["uuid"])) {
                $players[$data["userName"]] = new User($data["userName"], $data["uuid"], $data["deviceId"]);
            }
        }

        $this->users = $players;
    }

    public function getUsers() : array {
        return $this->users;
    }

    #[Pure] public function getOnlineUsers() : array {
        $user = [];

        foreach($this->users as $user) {
            if($user->isConnected())
                $user[] = $user;
        }

        return $user;
    }
}