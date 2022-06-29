<?php

namespace core\util\utils;

class ConfigUtil {

    public const LIMIT_KOXY = 1;
    public const LIMIT_REFY = 6;
    public const LIMIT_PERLY = 3;

    public const REPORT_COOLDOWN_TIME = 1;

    public const MAX_PLAYER_OFFER = 1;
    public const MAX_VIP_OFFER = 2;
    public const MAX_SVIP_OFFER = 3;
    public const MAX_SPONSOR_OFFER = 5;

    public const MAX_PLAYER_CAVES = 1;
    public const MAX_VIP_CAVES = 1;
    public const MAX_SVIP_CAVES = 1;
    public const MAX_SPONSOR_CAVES = 2;

    public const ENTITIES_LIMIT = 5;

    public const TELEPORT_TIME = 10;

    public const PROTOCOLS = [422, 419];

    public const BLACK_LIST_WORDS = ["sweephc.pl", "sweephc", "bullhc"];

    public const PERMISSION_TAG = "core.";
    public const LOBBY_WORLD = "lobby";
    public const DEFAULT_WORLD = "world";
    public const PVP_WORLD = "eventy";
    public const BOSS_WORLD = "boss";
    public const AUTO_LOAD_WORLDS = ["lobby"];

    public const BLOCK_WEBHOOK = "https://discordapp.com/api/webhooks/764803824396402709/zx0-2XUYp6Iwr_G9iZtKyHf9WDmDU2lB7ujDazGrxJ3_hrpvtdOtwQXVtXSw0iOm1sPr";
    public const REPORT_WEBHOOK = "https://discord.com/api/webhooks/824661878760013864/pyYTY6GMPtXh1JmYtb9nBrUS13oPPLbhPsgGg3W3QWE46vCgCgkxGmtXtUpzup6scbGO";
    public const DISCORD_ADMINISTRATOR_ROLES = [
        "ROOT" => "<@&728566154607460413>",
        "Admin" => "<@&728580574469554227>",
        "Support" => "<@&728569163551080558>"
    ];

    public const LEVEL = "CaveBlock_";
    public const DISCORD_INVITE = "https://discord.gg/2a9KgGB";

    public const DEFAULT_MONEY = 0.5;
    public const STATTRACK_RESET_COST = 5;
    public const STATTRACK_BUY_COST = 100;
    public const CAVE_BORDER = 100;

    public const CREATED_CAVE = 60*30;
    public const ROULETTE_TIME = 60*30;

    public const ANTYLOGOUT_TIME = 30;
    public const ANTYLOGOUT_COMMANDS = ["cb", "spawn", "schowek", "warp", "repair", "enchanty", "tpa", "tpaccept", "tpdeny", "heal", "feed", "repair"];
    public const KILL_MONEY = 0.1;
    public const KILL_STREAK_DELAY = 60;

    public const COMMAND_SPAM_TIME = 2;
    public const SPONSOR_DROP_CHANCE = 0.3;

    public const BOT_MESSAGES = [
        "§l§8[§9!§8]§r§7 Wszystkie komendy serwerowe pod §l§8/§9pomoc",
        "§l§8[§9!§8]§r§7 Aby zarzadzac jaskinia wpisz §l§8/§9caveblock",
        "§l§8[§9!§8]§r§7 Strona serwera §l§9DARKMOONPE.PL",
        "§l§8[§9!§8]§r§7 Administracja pod §l§8/§9administracja",
        "§l§8[§9!§8]§r§7 Zglaszac graczy mozna dzieki komendzie §l§8/§9report",
        "§l§8[§9!§8]§r§7 Tryb serwera to §l§9CAVEBLOCK",
        "§l§8[§9!§8]§r§7 Discord serwera pod §l§8/§9discord",
        "§l§8[§9!§8]§r§7 Jest to nasza §l§92 §r§7edycja!",
        "§l§8[§9!§8]§r§7 Wylaczyc wiadomosci bota mozna poprzez komende §l§8/§9ustawienia",
        "§l§8[§9!§8]§r§7 To co daja rangi premium znajdziesz pod §l§8/§9rangi",
        "§l§8[§9!§8]§r§7 Discord wlasciciela §l§9iDarkQ#0001",
        "§l§8[§9!§8]§r§7 W jaskini nie ma limitu osob!",
        "§l§8[§9!§8]§r§7 Jesli Tracisz czyjes zaufanie, Pamietaj ze zawsze mozesz zabrac mu uprawnienia w menu jaskini!",
        "§l§8[§9!§8]§r§7 Masz jakis pomysl? Podeslij na discordzie badz napisz administratorowi"
    ];

    public const MAX_LOCK_CHEST_PLAYER = 2;
    public const MAX_LOCK_CHEST_VIP = 2;
    public const MAX_LOCK_CHEST_SVIP = 4;
    public const MAX_LOCK_CHEST_SPONSOR = 6;

    public const PETS = [
        "bat" => [
            "networkID" => 19,
            "width" => 0.5,
            "height" => 0.9,
            "speed" => 1.4,
            "price" => 120,
            "displayName" => "Nietoperz",
            "canFly" => true
        ],

        "rabbit" => [
            "networkID" => 18,
            "width" => 0.402,
            "height" => 0.402,
            "speed" => 1.4,
            "price" => 80,
            "displayName" => "Krolik",
            "canFly" => false
        ],

        "silverfish" => [
            "networkID" => 39,
            "width" => 0.4,
            "height" => 0.3,
            "speed" => 1.4,
            "price" => 50,
            "displayName" => "Silverfish",
            "canFly" => false
        ],

        "bee" => [
            "networkID" => 122,
            "width" => 0.55,
            "height" => 0.5,
            "speed" => 1.4,
            "price" => 140,
            "displayName" => "Pszczola",
            "canFly" => true
        ],

        "chicken" => [
            "networkID" => 10,
            "width" => 0.6,
            "height" => 0.8,
            "speed" => 1.4,
            "price" => 80,
            "displayName" => "Kurczak",
            "canFly" => false
        ],

        "parrot" => [
            "networkID" => 30,
            "width" => 0.5,
            "height" => 0.9,
            "speed" => 1.4,
            "price" => 140,
            "displayName" => "Papuga",
            "canFly" => true
        ],

        "phantom" => [
            "networkID" => 58,
            "width" => 0.9,
            "height" => 0.5,
            "speed" => 1.4,
            "price" => 150,
            "displayName" => "Phantom",
            "canFly" => true
        ],

        "vex" => [
            "networkID" => 105,
            "width" => 0.4,
            "height" => 0.8,
            "speed" => 1.4,
            "price" => 100,
            "displayName" => "Vex",
            "canFly" => true
        ],

        "endermite" => [
            "networkID" => 55,
            "width" => 0.4,
            "height" => 0.3,
            "speed" => 1.4,
            "price" => 70,
            "displayName" => "EnderMite",
            "canFly" => false
        ],

        "fox" => [
            "networkID" => 121,
            "width" => 0.7,
            "height" => 0.6,
            "speed" => 1.4,
            "price" => 90,
            "displayName" => "Lis",
            "canFly" => false
        ],

        "cat" => [
            "networkID" => 75,
            "width" => 0.48,
            "height" => 0.56,
            "speed" => 1.4,
            "price" => 100,
            "displayName" => "Kot",
            "canFly" => false
        ],

        "ocelot" => [
            "networkID" => 22,
            "width" => 0.6,
            "height" => 0.7,
            "speed" => 1.4,
            "price" => 100,
            "displayName" => "Ocelot",
            "canFly" => false
        ],

        "wolf" => [
            "networkID" => 14,
            "width" => 0.6,
            "height" => 0.8,
            "speed" => 1.4,
            "price" => 110,
            "displayName" => "Wilk",
            "canFly" => false
        ],
    ];
}