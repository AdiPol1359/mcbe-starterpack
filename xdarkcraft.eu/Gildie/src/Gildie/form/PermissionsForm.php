<?php

namespace Gildie\form;

use Gildie\guild\GuildManager;
use pocketmine\Player;
use Gildie\Main;

class PermissionsForm extends Form {

    private $nick;

	public function __construct(string $nick) {
	    $this->nick = $nick;

		$data = [
		 "type" => "form",
		 "title" => "§7Permisje gracza §4$nick",
		 "content" => "",
		 "buttons" => []
		];

		$guildManager = Main::getInstance()->getGuildManager();

		$data["buttons"][] = ["text" => "Staw. i niszcz. stoniarek\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_STONIARKI) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/blocks/stone"]];
		$data["buttons"][] = ["text" => "Usuwanie stoniarek\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_STONIARKI_DESTROY) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/blocks/stone"]];
		$data["buttons"][] = ["text" => "Stawianie blokow\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_BLOCKS_PLACE) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/blocks/brick"]];
		$data["buttons"][] = ["text" => "Niszczenie blokow\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_BLOCKS_BREAK) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/items/diamond_pickaxe"]];
		$data["buttons"][] = ["text" => "Stawianie TNT\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_TNT_PLACE) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/blocks/tnt_side"]];
		$data["buttons"][] = ["text" => "Stawianie i niszczenie skrzyn\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_CHEST_PLACE_BREAK) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/blocks/chest_front"]];
		$data["buttons"][] = ["text" => "Otwieranie skrzyn\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_CHEST_OPEN) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/blocks/chest_front"]];
        $data["buttons"][] = ["text" => "Stawianie i niszczenie piecy\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_FURNACE_PLACE_BREAK) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/blocks/furnace_front_off"]];
        $data["buttons"][] = ["text" => "Otwieranie piecy\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_FURNACE_OPEN) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/blocks/furnace_front_off"]];
        $data["buttons"][] = ["text" => "Stawianie i niszcz. beaconow\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_BEACON_PLACE_BREAK) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/blocks/beacon"]];
        $data["buttons"][] = ["text" => "Otwieranie beaconow\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_BEACON_OPEN) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/blocks/beacon"]];
        $data["buttons"][] = ["text" => "Otwieranie sejfu gildii\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_SKARBIEC_OPEN) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/blocks/chest_front"]];
        $data["buttons"][] = ["text" => "Tpaccept\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_TPACCEPT) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/items/ender_pearl"]];
        $data["buttons"][] = ["text" => "Wylewanie lawy\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_LAVA) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/items/bucket_lava"]];
        $data["buttons"][] = ["text" => "Wylewanie wody\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_WATER) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/items/bucket_water"]];
        $data["buttons"][] = ["text" => "Uzywanie interakcji\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_INTERACT) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/items/lever"]];
        $data["buttons"][] = ["text" => "Wlaczanie/wylaczanie PVP\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_PVP) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/items/diamond_sword"]];
        $data["buttons"][] = ["text" => "Zapraszanie do gildii\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_INVITE_MEMBERS) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/items/diamond"]];
        $data["buttons"][] = ["text" => "Wyrzucanie z gildii\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_KICK_MEMBERS) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/items/diamond_boots"]];
        $data["buttons"][] = ["text" => "Nadawanie permisji\n".($guildManager->hasPermission($nick, GuildManager::PERMISSION_SET_PERMISSIONS) ? "§l§4WLACZONE" : "§l§cWYLACZONE"), "image" => ["type" => "path", "data" => "textures/blocks/redstone_torch_on"]];
        $data["buttons"][] = ["text" => "Wlacz wszystkie"];
        $data["buttons"][] = ["text" => "Wylacz wszystkie"];
        $data["buttons"][] = ["text" => "Ustaw domyslne permisje"];

        $this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		
		$formData = json_decode($data);
		
		if($formData === null) return;

		$guildManager = Main::getInstance()->getGuildManager();

		$player_switch = $player->getServer()->getPlayerExact($this->nick);
		$nick = $this->nick;

		switch($formData) {

            case "0":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_STONIARKI);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_STONIARKI)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do stawiania i niszczenia stoniarek");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do stawiania i niszczenia stoniarek");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do stawiania i niszczenia stoniarek");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do stawiania i niszczenia stoniarek");
                }
            break;

            case "1":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_STONIARKI_DESTROY);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_STONIARKI_DESTROY)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do niszczenia stoniarek zlotym kilofem");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do niszczenia stoniarek zlotym kilofem");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do niszczenia stoniarek zlotym kilofem");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do niszczenia stoniarek zlotym kilofem");
                }
            break;

            case "2":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_BLOCKS_PLACE);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_BLOCKS_PLACE)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do stawiania blokow");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do stawiania blokow");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do stawiania blokow");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do niszczenia blokow");
                }
            break;

            case "3":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_BLOCKS_BREAK);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_BLOCKS_BREAK)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do niszczenia blokow");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do niszczenia blokow");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do niszczenia blokow");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do niszczenia blokow");
                }
            break;

            case "4":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_TNT_PLACE);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_TNT_PLACE)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do stawiania TNT");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do stawiania TNT");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do stawiania TNT");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do stawiania TNT");
                }
            break;

            case "5":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_CHEST_PLACE_BREAK);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_CHEST_PLACE_BREAK)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do stawiania skrzynek");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do stawiania skrzynek");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do stawiania skrzynek");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do stawiania skrzynek");
                }
            break;

            case "6":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_CHEST_OPEN);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_CHEST_OPEN)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do otwierania skrzynek");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do otwierania skrzynek");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do otwierania skrzynek");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do otwierania skrzynek");
                }
            break;

            case "7":
            $guildManager->switchPermission($nick, GuildManager::PERMISSION_FURNACE_PLACE_BREAK);

            if($guildManager->hasPermission($nick, GuildManager::PERMISSION_FURNACE_PLACE_BREAK)) {
                $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do stawiania i niszczenia piecy");

                if($player_switch != null)
                    $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do stawiania i niszczenia piecy");
            } else {
                $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do stawiania i niszczenia piecy");

                if($player_switch != null)
                    $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do stawiania i niszczenia piecy");
            }
            break;

            case "8":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_FURNACE_OPEN);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_FURNACE_OPEN)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do otwierania piecy");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do otwierania piecy");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do otwierania piecy");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do otwierania piecy");
                }
            break;

            case "9":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_BEACON_PLACE_BREAK);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_BEACON_PLACE_BREAK)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do stawiania i niszczenia beaconow");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do stawiania i niszczenia beaconow");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do stawiania i niszczenia beaconow");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do stawiania i niszczenia beaconow");
                }
            break;

            case "10":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_BEACON_OPEN);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_BEACON_OPEN)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do otwierania beaconow");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do otwierania beaconow");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do otwierania beaconow");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do otwierania beaconow");
                }
            break;

            case "11":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_SKARBIEC_OPEN);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_SKARBIEC_OPEN)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do otwierania skarbca gildii");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do otwierania skarbca gildii");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do otwierania skarbca gildii");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do otwierania skarbca gildii");
                }
            break;

            case "12":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_TPACCEPT);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_TPACCEPT)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do uzywania §4/tpaccept §7na terenie gildii");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do uzywania §4/tpaccept §7na terenie gildii");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do uzywania §4/tpaccept §7na terenie gildii");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do uzywania §4/tpaccept §7na terenie gildii");
                }
            break;

            case "13":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_LAVA);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_LAVA)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do wylewania lawy");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do wylewania lawy");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} permisje do wylewania lawy");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do wylewania lawy");
                }
            break;

            case "14":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_WATER);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_WATER)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do wylewania wody");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do wylewania wody");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} permisje do wylewania wody");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do wylewania wody");
                }
            break;

            case "15":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_INTERACT);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_INTERACT)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do uzywania interakcji");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do uzywania interakcji");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do uzywania interakcji");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do uzywania interakcji");
                }
            break;

            case "16":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_PVP);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_PVP)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do wlaczania i wylaczania PVP");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do wlaczania i wylaczania PVP");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do wlaczania i wylaczania PVP");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do wlaczania i wylaczania PVP");
                }
            break;

            case "17":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_INVITE_MEMBERS);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_INVITE_MEMBERS)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do zapraszania innych do gildii");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do zapraszania innych do gildii");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do zapraszania innych do gildii");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do zapraszania innych do gildii");
                }
            break;

            case "18":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_KICK_MEMBERS);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_KICK_MEMBERS)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do wyrzucania innych z gildii");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do wyrzucania innych z gildii");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do wyrzucania innych z gildii");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do wyrzucania innych z gildii");
                }
            break;

            case "19":
                $guildManager->switchPermission($nick, GuildManager::PERMISSION_SET_PERMISSIONS);

                if($guildManager->hasPermission($nick, GuildManager::PERMISSION_SET_PERMISSIONS)) {
                    $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7permisje do nadawania permisji innym");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Nadano ci permisje do nadawania permisji innym");
                } else {
                    $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7permisje do nadawania permisji innym");

                    if($player_switch != null)
                        $player_switch->sendMessage("§8§l>§r §7Odebrano ci permisje do nadawania permisji innym");
                }
            break;

            case "20":
                $guildManager->setAllPermissions($nick);

                $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7wszystkie permisje");

                if($player_switch != null)
                    $player_switch->sendMessage("§8§l>§r §7Nadano ci wszystkie permisje");
            break;

            case "21":
                $guildManager->removeAllPermissions($nick);

                $player->sendMessage("§8§l>§r §7Odebrano graczu §4{$nick} §7wszystkie permisje");

                if($player_switch != null)
                    $player_switch->sendMessage("§8§l>§r §7Odebrano ci wszystkie permisje");
            break;

            case "22":
                $guildManager->setDefaultPermissions($nick);

                $player->sendMessage("§8§l>§r §7Nadano graczu §4{$nick} §7domyslne permisje");

                if($player_switch != null)
                    $player_switch->sendMessage("§8§l>§r §7Nadano ci domyslne permisje");
            break;
        }

		$player->sendForm(new PermissionsForm($this->nick));
	}
}