<?php

declare(strict_types=1);

namespace core\utils;

use core\items\custom\BoyFarmer;
use core\items\custom\Crowbar;
use core\items\custom\FastPickaxe;
use core\items\custom\FosMiner;
use core\items\custom\ThrownTNT;
use core\Main;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\EffectIds;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\Server;

final class Settings {

    public static function __init() {

        $itemFactory = ItemFactory::getInstance();

        $stonePickaxe = VanillaItems::STONE_PICKAXE();
        $stonePickaxe->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2));

        $helmet = VanillaItems::IRON_HELMET();
        $helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3));
        $helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2));

        $chestPlate = VanillaItems::IRON_CHESTPLATE();
        $chestPlate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3));
        $chestPlate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2));

        $leggings = VanillaItems::IRON_LEGGINGS();
        $leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3));
        $leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2));

        $boots = VanillaItems::IRON_BOOTS();
        $boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3));
        $boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2));
        $boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 2));

        $sword = VanillaItems::IRON_SWORD();
        $sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 3));
        $sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
        $sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), 1));

        $knockBack = VanillaItems::IRON_SWORD();
        $knockBack->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
        $knockBack->addEnchantment(new EnchantmentInstance(VanillaEnchantments::KNOCKBACK(), 2));

        $diamondPickaxe = VanillaItems::DIAMOND_PICKAXE();
        $diamondPickaxe->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 5));
        $diamondPickaxe->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
        $diamondPickaxe->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::FORTUNE), 3));

        $bow = VanillaItems::BOW();
        $bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FLAME(), 1));
        $bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
        $bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 3));

        self::$COBBLEX_DROP = [
            0 => [
                "item" => VanillaItems::ENDER_PEARL(),
                "chance" => 9,
                "name" => "Perla kresu"
            ],

            1 => [
                "item" => VanillaItems::APPLE(),
                "chance" => 9,
                "name" => "Jablko"
            ],

            2 => [
                "item" => VanillaItems::SUGAR(),
                "chance" => 9,
                "name" => "Cukier"
            ],

            3 => [
                "item" => VanillaItems::LEATHER(),
                "chance" => 9,
                "name" => "Skora"
            ],

            4 => [
                "item" => VanillaBlocks::TNT()->asItem(),
                "chance" => 9,
                "name" => "TNT"
            ],

            5 => [
                "item" => VanillaItems::STRING(),
                "chance" => 9,
                "name" => "Nicie"
            ],

            6 => [
                "item" => VanillaItems::GOLD_INGOT(),
                "chance" => 9,
                "name" => "Zloto"
            ],

            7 => [
                "item" => VanillaItems::ARROW(),
                "chance" => 9,
                "name" => "Strzaly"
            ],

            8 => [
                "item" => VanillaItems::WATER_BUCKET(),
                "chance" => 9,
                "name" => "Wiadro wody"
            ],

            9 => [
                "item" => VanillaItems::LAVA_BUCKET(),
                "chance" => 9,
                "name" => "Wiadro lawy"
            ],

            10 => [
                "item" => VanillaItems::SNOWBALL(),
                "chance" => 9,
                "name" => "Sniezki"
            ]
        ];

//do zrobienia

        self::$PREMIUMCASE_DROP = [0 => ["item" => (new FastPickaxe())->__toItem(),
            "chance" => 0.1,
            "name" => "Kilof 6§8/§e3§8/§e3"
        ],
            1 => ["item" => (new ThrownTNT())->__toItem(),
                "chance" => 0.1,
                "name" => "RZUCAK"
            ],
            2 => ["item" => $helmet,
                "chance" => 7,
                "name" => "Helm 4/3"
            ],
            3 => ["item" => $chestPlate,
                "chance" => 7,
                "name" => "Klate 4/3"
            ],
            4 => ["item" => $leggings,
                "chance" => 7,
                "name" => "Spodnie 4/3"
            ],
            5 => ["item" => $boots,
                "chance" => 7,
                "name" => "Buty 4/3"
            ],
            6 => ["item" => $sword,
                "chance" => 6.7,
                "name" => "Miecz 3/3/1"
            ],
            7 => ["item" => $bow,
                "chance" => 4,
                "name" => "Luk 3/3/1"
            ],
            8 => ["item" => $knockBack,
                "chance" => 7,
                "name" => "Miecz knock 3/2"
            ],
            9 => ["item" => $diamondPickaxe,
                "chance" => 6.6,
                "name" => "Kilof 5/3/3"
            ],
            10 => ["item" => $itemFactory->get(ItemIds::ENCHANTED_GOLDEN_APPLE, 0, Settings::$ENCHANTED_LIMIT * 2),
                "chance" => 5,
                "name" => "Koxy"
            ],
            11 => ["item" => $itemFactory->get(ItemIds::GOLDEN_APPLE, 0, Settings::$REFILE_LIMIT),
                "chance" => 5,
                "name" => "Refy"
            ],
            12 => ["item" => $itemFactory->get(ItemIds::ENDER_PEARL, 0, Settings::$PEARL_LIMIT),
                "chance" => 5,
                "name" => "Perly"
            ],
            13 => ["item" => (new BoyFarmer())->__toItem()->setCount(16),
                "chance" => 8,
                "name" => "BoyFarmer"
            ],
            14 => ["item" => (new FosMiner())->__toItem()->setCount(16),
                "chance" => 8,
                "name" => "Kopacz Fos"
            ],
            15 => ["item" => (new Crowbar())->__toItem(),
                "chance" => 0.5,
                "name" => "Lom"
            ],
            18 => ["item" => $itemFactory->get(ItemIds::ARROW, 0, 32),
                "chance" => 8,
                "name" => "Strzaly"
            ],
            //14
            19 => ["item" => $itemFactory->get(ItemIds::SNOWBALL, 0, 16),
                "chance" => 8,
                "name" => "Sniezki"
            ],// 14
        ];

        self::$PREMIUMCASE_DROP = [
            0 => [
                "item" => VanillaItems::EMERALD(),
                "chance" => 0.5,
                "name" => "Emerald"
            ]
        ];


        self::$HEAD_DROP = [
            0 => [
                "item" => VanillaItems::FIRE_RESISTANCE_POTION(),
                "chance" => 20,
                "name" => "Potka odpornosci na ogien (3:00)"
            ],

            1 => [
                "item" => VanillaItems::LONG_FIRE_RESISTANCE_POTION(),
                "chance" => 20,
                "name" => "Potka odpornosci na ogien (8:00)"
            ],

            2 => [
                "item" => VanillaItems::LEAPING_POTION(),
                "chance" => 20,
                "name" => "Potka wysokiego skakania (3:00)"
            ],

            3 => [
                "item" => VanillaItems::REGENERATION_POTION(),
                "chance" => 20,
                "name" => "Potka regeneracji zdrowia (0:45)"
            ],

            4 => [
                "item" => VanillaItems::HEALING_POTION(),
                "chance" => 20,
                "name" => "Potka natychmiastowego zdrowia"
            ],
        ];

        self::$DROP = [
            0 => [
                "name" => "Diament",
                "dropName" => "diamond",
                "chance" => 15,
                "default" => false,
                "exp" => 8,
                "fortune" => true,
                "turbo" => true,
                "color" => "§b",
                "deposit" => [
                    "depositItem" => false,
                    "depositName" => ""
                ],
                "slot" => 10,
                "bonuses" => [
                    "drop.vip" => 0.2,
                    "drop.svip" => 0.3,
					"drop.sponsor" => 0.4
                ],
                "message" => "§8({COLOR}+{COUNT}§8) {COLOR}{NAME}",
                "tool" => [
                    VanillaItems::STONE_PICKAXE(),
                    VanillaItems::GOLDEN_PICKAXE(),
                    VanillaItems::IRON_PICKAXE(),
                    VanillaItems::DIAMOND_PICKAXE()
                ],
                "amount" => [
                    "min" => 1,
                    "max" => 3
                ],
                "drop" => [
                    "what" => VanillaItems::DIAMOND(),
                    "from" => VanillaBlocks::STONE()
                ]
            ],

            1 => [
                "name" => "Szmaragd",
                "dropName" => "emerald",
                "chance" => 8,
                "default" => false,
                "exp" => 19,
                "fortune" => true,
                "turbo" => true,
                "color" => "§a",
                "deposit" => [
                    "depositItem" => false,
                    "depositName" => ""
                ],
                "slot" => 11,
                "bonuses" => [
                    "drop.vip" => 0.2,
                    "drop.svip" => 0.3,
					"drop.sponsor" => 0.4
                ],
                "message" => "§8({COLOR}+{COUNT}§8) {COLOR}{NAME}",
                "tool" => [
                    VanillaItems::STONE_PICKAXE(),
                    VanillaItems::GOLDEN_PICKAXE(),
                    VanillaItems::IRON_PICKAXE(),
                    VanillaItems::DIAMOND_PICKAXE()
                ],
                "amount" => [
                    "min" => 1,
                    "max" => 3
                ],
                "drop" => [
                    "what" => VanillaItems::EMERALD(),
                    "from" => VanillaBlocks::STONE()
                ]
            ],

            2 => [
                "name" => "Zloto",
                "dropName" => "gold",
                "chance" => 6,
                "default" => false,
                "exp" => 12,
                "fortune" => true,
                "turbo" => true,
                "color" => "§e",
                "deposit" => [
                    "depositItem" => false,
                    "depositName" => ""
                ],
                "slot" => 12,
                "bonuses" => [
                    "drop.vip" => 0.2,
                    "drop.svip" => 0.3,
					"drop.sponsor" => 0.4
                ],
                "message" => "§8({COLOR}+{COUNT}§8) {COLOR}{NAME}",
                "tool" => [
                    VanillaItems::STONE_PICKAXE(),
                    VanillaItems::GOLDEN_PICKAXE(),
                    VanillaItems::IRON_PICKAXE(),
                    VanillaItems::DIAMOND_PICKAXE()
                ],
                "amount" => [
                    "min" => 1,
                    "max" => 3
                ],
                "drop" => [
                    "what" => VanillaItems::GOLD_INGOT(),
                    "from" => VanillaBlocks::STONE()
                ]
            ],

            3 => [
                "name" => "Zelazo",
                "dropName" => "iron",
                "chance" => 11,
                "default" => false,
                "exp" => 6,
                "fortune" => true,
                "turbo" => true,
                "color" => "§f",
                "deposit" => [
                    "depositItem" => false,
                    "depositName" => ""
                ],
                "slot" => 13,
                "bonuses" => [
                    "drop.vip" => 0.2,
                    "drop.svip" => 0.3,
					"drop.sponsor" => 0.4
                ],
                "message" => "§8({COLOR}+{COUNT}§8) {COLOR}{NAME}",
                "tool" => [
					VanillaItems::WOODEN_PICKAXE(),
                    VanillaItems::STONE_PICKAXE(),
                    VanillaItems::GOLDEN_PICKAXE(),
                    VanillaItems::IRON_PICKAXE(),
                    VanillaItems::DIAMOND_PICKAXE()
                ],
                "amount" => [
                    "min" => 1,
                    "max" => 3
                ],
                "drop" => [
                    "what" => VanillaItems::IRON_INGOT(),
                    "from" => VanillaBlocks::STONE()
                ]
            ],

            4 => [
                "name" => "Perla kresu",
                "dropName" => "enderpearl",
                "chance" => 1.2,
                "default" => false,
                "exp" => 40,
                "fortune" => false,
                "turbo" => false,
                "color" => "§9",
                "deposit" => [
                    "depositItem" => true,
                    "depositName" => "enderpearl"
                ],
                "slot" => 14,
                "bonuses" => [
                    "drop.vip" => 0.2,
                    "drop.svip" => 0.3,
					"drop.sponsor" => 0.4
                ],
                "message" => "§8({COLOR}+{COUNT}§8) {COLOR}{NAME}",
                "tool" => [
                    VanillaItems::IRON_PICKAXE(),
                    VanillaItems::DIAMOND_PICKAXE()
                ],
                "amount" => [
                    "min" => 1,
                    "max" => 1
                ],
                "drop" => [
                    "what" => VanillaItems::ENDER_PEARL(),
                    "from" => VanillaBlocks::STONE()
                ]
            ],

            5 => [
                "name" => "Wegiel",
                "dropName" => "coal",
                "chance" => 15,
                "default" => false,
                "exp" => 9,
                "fortune" => true,
                "turbo" => true,
                "color" => "§8",
                "deposit" => [
                    "depositItem" => false,
                    "depositName" => ""
                ],
                "slot" => 16,
                "bonuses" => [
                    "drop.vip" => 0.2,
                    "drop.svip" => 0.3,
					"drop.sponsor" => 0.4
                ],
                "message" => "§8({COLOR}+{COUNT}§8) {COLOR}{NAME}",
                "tool" => [
                    VanillaItems::STONE_PICKAXE(),
                    VanillaItems::GOLDEN_PICKAXE(),
                    VanillaItems::IRON_PICKAXE(),
                    VanillaItems::DIAMOND_PICKAXE()
                ],
                "amount" => [
                    "min" => 1,
                    "max" => 3
                ],
                "drop" => [
                    "what" => VanillaItems::COAL(),
                    "from" => VanillaBlocks::STONE()
                ]
            ],

            6 => [
                "name" => "TNT",
                "dropName" => "tnt",
                "chance" => 2,
                "default" => false,
                "exp" => 30,
                "fortune" => false,
                "turbo" => false,
                "color" => "§c",
                "deposit" => [
                    "depositItem" => false,
                    "depositName" => ""
                ],
                "slot" => 19,
                "bonuses" => [
                    "drop.vip" => 0.2,
                    "drop.svip" => 0.3,
					"drop.sponsor" => 0.4
                ],
                "message" => "§8({COLOR}+{COUNT}§8) {COLOR}{NAME}",
                "tool" => [
                    VanillaItems::STONE_PICKAXE(),
                    VanillaItems::GOLDEN_PICKAXE(),
                    VanillaItems::IRON_PICKAXE(),
                    VanillaItems::DIAMOND_PICKAXE()
                ],
                "amount" => [
                    "min" => 1,
                    "max" => 3
                ],
                "drop" => [
                    "what" => VanillaBlocks::TNT()->asItem(),
                    "from" => VanillaBlocks::STONE()
                ]
            ],

            7 => [
                "name" => "Ksiazka",
                "dropName" => "book",
                "chance" => 5,
                "default" => false,
                "exp" => 10,
                "fortune" => true,
                "turbo" => true,
                "color" => "§6",
                "deposit" => [
                    "depositItem" => false,
                    "depositName" => ""
                ],
                "slot" => 20,
                "bonuses" => [
                    "drop.vip" => 0.2,
                    "drop.svip" => 0.3,
					"drop.sponsor" => 0.4
                ],
                "message" => "§8({COLOR}+{COUNT}§8) {COLOR}{NAME}",
                "tool" => [
                    VanillaItems::STONE_PICKAXE(),
                    VanillaItems::GOLDEN_PICKAXE(),
                    VanillaItems::IRON_PICKAXE(),
                    VanillaItems::DIAMOND_PICKAXE()
                ],
                "amount" => [
                    "min" => 1,
                    "max" => 3
                ],
                "drop" => [
                    "what" => VanillaItems::BOOK(),
                    "from" => VanillaBlocks::STONE()
                ]
            ],

            8 => [
                "name" => "Jablko",
                "dropName" => "apple",
                "chance" => 4,
                "default" => false,
                "exp" => 17,
                "fortune" => true,
                "turbo" => true,
                "color" => "§c",
                "deposit" => [
                    "depositItem" => false,
                    "depositName" => ""
                ],
                "slot" => 21,
                "bonuses" => [
                    "drop.vip" => 0.2,
                    "drop.svip" => 0.3,
					"drop.sponsor" => 0.4
                ],
                "message" => "§8({COLOR}+{COUNT}§8) {COLOR}{NAME}",
                "tool" => [
                    VanillaItems::STONE_PICKAXE(),
                    VanillaItems::GOLDEN_PICKAXE(),
                    VanillaItems::IRON_PICKAXE(),
                    VanillaItems::DIAMOND_PICKAXE()
                ],
                "amount" => [
                    "min" => 1,
                    "max" => 3
                ],
                "drop" => [
                    "what" => VanillaItems::APPLE(),
                    "from" => VanillaBlocks::STONE()
                ]
            ],

            9 => [
                "name" => "Obsydian",
                "dropName" => "obsidian",
                "chance" => 7,
                "default" => false,
                "exp" => 30,
                "fortune" => true,
                "turbo" => true,
                "color" => "§1",
                "deposit" => [
                    "depositItem" => false,
                    "depositName" => ""
                ],
                "slot" => 22,
                "bonuses" => [
                    "drop.vip" => 0.2,
                    "drop.svip" => 0.3,
					"drop.sponsor" => 0.4
                ],
                "message" => "§8({COLOR}+{COUNT}§8) {COLOR}{NAME}",
                "tool" => [
                    VanillaItems::STONE_PICKAXE(),
                    VanillaItems::GOLDEN_PICKAXE(),
                    VanillaItems::IRON_PICKAXE(),
                    VanillaItems::DIAMOND_PICKAXE()
                ],
                "amount" => [
                    "min" => 1,
                    "max" => 3
                ],
                "drop" => [
                    "what" => VanillaBlocks::OBSIDIAN()->asItem(),
                    "from" => VanillaBlocks::STONE()
                ]
            ],

            10 => [
                "name" => "Sniezka",
                "dropName" => "snowball",
                "chance" => 1.3,
                "default" => false,
                "exp" => 12,
                "fortune" => false,
                "turbo" => false,
                "color" => "§f",
                "deposit" => [
                    "depositItem" => true,
                    "depositName" => "snowball"
                ],
                "slot" => 15,
                "bonuses" => [
                    "drop.vip" => 0.2,
                    "drop.svip" => 0.3,
					"drop.sponsor" => 0.4
                ],
                "message" => "§8({COLOR}+{COUNT}§8) {COLOR}{NAME}",
                "tool" => [
                    VanillaItems::IRON_PICKAXE(),
                    VanillaItems::DIAMOND_PICKAXE()
                ],
                "amount" => [
                    "min" => 1,
                    "max" => 1
                ],
                "drop" => [
                    "what" => VanillaItems::SNOWBALL(),
                    "from" => VanillaBlocks::STONE()
                ]
            ],

            11 => [
                "name" => "Cobblestone",
                "dropName" => "cobblestone",
                "chance" => 100,
                "default" => true,
                "exp" => 1,
                "fortune" => false,
                "turbo" => false,
                "color" => "§7",
                "deposit" => [
                    "depositItem" => false,
                    "depositName" => ""
                ],
                "slot" => 42,
                "bonuses" => [],
                "message" => "",
                "tool" => [
                    VanillaItems::WOODEN_PICKAXE(),
                    VanillaItems::STONE_PICKAXE(),
                    VanillaItems::GOLDEN_PICKAXE(),
                    VanillaItems::IRON_PICKAXE(),
                    VanillaItems::DIAMOND_PICKAXE()
                ],
                "amount" => [
                    "min" => 1,
                    "max" => 1
                ],
                "drop" => [
                    "what" => VanillaBlocks::STONE()->asItem(),
                    "from" => VanillaBlocks::STONE()
                ]
            ]
        ];

        self::$STATS = [
            self::$STAT_POINTS => self::$STAT_DEFAULT_POINTS,
            self::$STAT_KILLS => 0,
            self::$STAT_DEATHS => 0,
            self::$STAT_ASSISTS => 0,
            self::$STAT_BREAK_BLOCKS => 0,
            self::$STAT_PLACE_BLOCKS => 0,
            self::$STAT_SPEND_TIME => 0,
            self::$STAT_LAST_JOIN_TIME => 0,
            self::$STAT_ENDER_PEARLS => 0,
            self::$STAT_GOLDEN_APPLES => 0,
            self::$STAT_ENCHANTED_APPLES => 0,
            self::$STAT_ARROWS => 0,
            self::$STAT_SNOWBALLS => 0,
            self::$STAT_THROWABLE_TNT => 0
        ];

        self::$TERRAIN_SETTINGS = [
            self::$TERRAIN_BREAK_BLOCK => "Niszczenie blokow",
            self::$TERRAIN_PLACE_BLOCK => "Stawianie blokow",
            self::$TERRAIN_INTERACT => "Interakcja",
            self::$TERRAIN_FIGHTING => "Bicie sie",
            self::$TERRAIN_USE_COMMAND => "Uzywanie komend",
            self::$TERRAIN_DAMAGE => "Obrazenia",
            self::$TERRAIN_LOSE_FOOD => "Utrata glodu"
        ];

        self::$BORDER_DATA = [
            "border" => self::$BORDER,
            "knock" => true,
            "damage" => true
        ];

        self::$EFFECT_LIST_DATA = [
            4 => ["item" => $itemFactory->get(ItemIds::BUCKET, 1),
                "name" => "CZYSZCZENIE EFEKTOW",
                "available" => true,
                "cost" => 5,
                "effectId" => -1,
                "effectLevel" => -1
            ],

            19 => ["item" => VanillaItems::IRON_PICKAXE(),
                "name" => "EFEKT HASTE 1",
                "available" => Settings::$EFFECT_HASTE_1,
                "cost" => 32,
                "effectId" => EffectIds::HASTE,
                "effectLevel" => 0
            ],

            28 => ["item" => VanillaItems::GOLDEN_PICKAXE(),
                "name" => "EFEKT HASTE 2",
                "available" => Settings::$EFFECT_HASTE_2,
                "cost" => 64,
                "effectId" => EffectIds::HASTE,
                "effectLevel" => 1
            ],

            21 => ["item" => VanillaItems::IRON_SWORD(),
                "name" => "EFEKT SILA 1",
                "available" => Settings::$EFFECT_STRENGTH_1,
                "cost" => 32,
                "effectId" => EffectIds::STRENGTH,
                "effectLevel" => 0
            ],

            30 => ["item" => VanillaItems::GOLDEN_SWORD(),
                "name" => "EFEKT SILA 2",
                "available" => Settings::$EFFECT_STRENGTH_2,
                "cost" => 64,
                "effectId" => EffectIds::STRENGTH,
                "effectLevel" => 1
            ],

            23 => ["item" => VanillaItems::IRON_BOOTS(),
                "name" => "EFEKT JUMP 1",
                "available" => Settings::$EFFECT_JUMP_BOOST_1,
                "cost" => 32,
                "effectId" => EffectIds::JUMP_BOOST,
                "effectLevel" => 0
            ],

            32 => ["item" => VanillaItems::GOLDEN_BOOTS(),
                "name" => "EFEKT JUMP 2",
                "available" => Settings::$EFFECT_JUMP_BOOST_2,
                "cost" => 64,
                "effectId" => EffectIds::JUMP_BOOST,
                "effectLevel" => 1
            ],

            25 => ["item" => VanillaItems::SUGAR(),
                "name" => "EFEKT SPEED 1",
                "available" => Settings::$EFFECT_SPEED_1,
                "cost" => 32,
                "effectId" => EffectIds::SPEED,
                "effectLevel" => 0
            ],

            34 => ["item" => VanillaItems::SUGAR(),
                "name" => "EFEKT SPEED 2",
                "available" => Settings::$EFFECT_SPEED_2,
                "cost" => 64,
                "effectId" => EffectIds::SPEED,
                "effectLevel" => 1
            ],
        ];

        self::$KITS = [
            "Gracz" => ["inventoryItem" => $itemFactory->get(ItemIds::CHAIN_HELMET),
                "slot" => 20,
                "time" => 60 * 5,
                "permission" => null,
                "items" => [$stonePickaxe,
                    $itemFactory->get(ItemIds::PLANKS, 0, 16),
                    $itemFactory->get(ItemIds::COOKED_PORKCHOP, 0, 64),
                    $itemFactory->get(ItemIds::ENDER_CHEST)
                ]
            ],

            "Vip" => ["inventoryItem" => $itemFactory->get(ItemIds::IRON_HELMET),
                "slot" => 21,
                "time" => 60 * 60 * 3,
                "permission" => self::$PERMISSION_TAG . "kit.vip",
                "items" => [$helmet,
                    $chestPlate,
                    $leggings,
                    $boots,
                    $sword,
                    $knockBack,
                    $itemFactory->get(ItemIds::ENCHANTED_GOLDEN_APPLE, 0, Settings::$ENCHANTED_LIMIT),
                    $itemFactory->get(ItemIds::GOLDEN_APPLE, 0, Settings::$REFILE_LIMIT),
                    $itemFactory->get(ItemIds::ENDER_PEARL, 0, Settings::$PEARL_LIMIT),
                    $itemFactory->get(ItemIds::SNOWBALL, 0, Settings::$SNOWBALL_LIMIT),
                    $itemFactory->get(ItemIds::ARROW, 0, Settings::$ARROW_LIMIT),
                    $itemFactory->get(ItemIds::BUCKET, 8),
                    $bow,
                    $diamondPickaxe
                ]
            ],

            "Svip" => ["inventoryItem" => $itemFactory->get(ItemIds::GOLD_HELMET),
                "slot" => 23,
                "time" => 60 * 60 * 3,
                "permission" => self::$PERMISSION_TAG . "kit.svip",
                "items" => [$sword,
                    $knockBack,
                    $itemFactory->get(ItemIds::ENCHANTED_GOLDEN_APPLE, 0, Settings::$ENCHANTED_LIMIT * 3),
                    $itemFactory->get(ItemIds::GOLDEN_APPLE, 0, Settings::$REFILE_LIMIT * 3),
                    $itemFactory->get(ItemIds::ENDER_PEARL, 0, Settings::$PEARL_LIMIT * 3),
                    $itemFactory->get(ItemIds::SNOWBALL, 0, Settings::$SNOWBALL_LIMIT),
                    $itemFactory->get(ItemIds::ARROW, 0, Settings::$ARROW_LIMIT * 3),
                    $itemFactory->get(ItemIds::BUCKET, 8),
                    $itemFactory->get(ItemIds::BUCKET, 8),
                    $helmet,
                    $chestPlate,
                    $leggings,
                    $boots,
                    $sword,
                    $knockBack,
                    $bow,
                    $diamondPickaxe,
                    $bow,
                    $diamondPickaxe
                ]
            ],

            "Sponsor" => ["inventoryItem" => $itemFactory->get(ItemIds::DIAMOND_HELMET),
                "slot" => 24,
                "time" => 60 * 60 * 3,
                "permission" => self::$PERMISSION_TAG . "kit.sponsor",
                "items" => [$sword,
                    $knockBack,
                    $itemFactory->get(ItemIds::ENCHANTED_GOLDEN_APPLE, 0, Settings::$ENCHANTED_LIMIT * 4),
                    $itemFactory->get(ItemIds::GOLDEN_APPLE, 0, Settings::$REFILE_LIMIT * 4),
                    $itemFactory->get(ItemIds::ENDER_PEARL, 0, Settings::$PEARL_LIMIT * 4),
                    $itemFactory->get(ItemIds::SNOWBALL, 0, Settings::$SNOWBALL_LIMIT * 2),
                    $itemFactory->get(ItemIds::ARROW, 0, Settings::$ARROW_LIMIT * 4),
                    $itemFactory->get(ItemIds::BUCKET, 8),
                    $itemFactory->get(ItemIds::BUCKET, 8),
                    $helmet,
                    $chestPlate,
                    $leggings,
                    $boots,
                    $helmet,
                    $chestPlate,
                    $leggings,
                    $boots,
                    $sword,
                    $knockBack,
                    $bow,
                    $diamondPickaxe,
                    $bow,
                    $diamondPickaxe
                ]
            ],

            "EnderChest" => ["inventoryItem" => $itemFactory->get(ItemIds::ENDER_CHEST),
                "slot" => 22,
                "time" => 60 * 5,
                "permission" => null,
                "items" => [$itemFactory->get(ItemIds::ENDER_CHEST)]
            ],

            "Jedzenie" => ["inventoryItem" => $itemFactory->get(ItemIds::COOKED_PORKCHOP),
                "slot" => 31,
                "time" => 60,
                "permission" => null,
                "items" => [$itemFactory->get(ItemIds::COOKED_PORKCHOP, 0, 64)]
            ],
        ];

        self::$VIP_DESCRIPTION = [
            "§e§lOPIS RANGI",
            self::$VIP_DROP_CHANCE . "% Wiecej do dropu",
            "Wieksza ilosc homow (4)",
            "Wieksza ilosc aukcji na bazar (4)",
            "permisja do /enderchest",
            "permisja do /kit vip",
            "Unikatowy prefix na chacie",
            "Mozliwosc zablokowania §e".self::$VIP_LOCK_LIMIT." skrzynek",
            "",
            "§e§lZAKUP RANGI",
            "Zakupu mozesz dokonac na stronie www.lajtpvp.pl",
            "Po zakupie nalezy wpisac /is na serwerze w celu odebrania uslugi"
        ];

        self::$SVIP_DESCRIPTION = ["§e§lOPIS RANGI",
            self::$SVIP_DROP_CHANCE . "% Wiecej do dropu",
            "Wieksza ilosc homow (5)",
            "Wieksza ilosc aukcji na bazar (5)",
            "permisja do /enderchest",
            "permisja do /repair",
            "permisja do /repair all",
            "permisja do /kit svip",
            "Unikatowy prefix na chacie",
            "Mozliwosc zablokowania §e".self::$SVIP_LOCK_LIMIT." skrzynek",
            "",
            "§e§lZAKUP RANGI",
            "Zakupu mozesz dokonac na stronie www.lajtpvp.pl",
            "Po zakupie nalezy wpisac /is na serwerze w celu odebrania uslugi"
        ];

        self::$SPONSOR_DESCRIPTION = ["§e§lOPIS RANGI",
            self::$SPONSOR_DROP_CHANCE . "% Wiecej do dropu",
            "Wieksza ilosc homow (8)",
            "Wieksza ilosc aukcji na bazar (6)",
            "permisja do /enderchest",
            "permisja do /enchant",
            "permisja do /repair",
            "permisja do /repair all",
            "permisja do /kit sponsor",
            "Unikatowy prefix na chacie",
            "Nielimitowany plecak",
            "Mozliwosc zablokowania §e".self::$SPONSOR_LOCK_LIMIT." skrzynek",
            "",
            "§e§lZAKUP RANGI",
            "Zakupu mozesz dokonac na stronie www.lajtpvp.pl",
            "Po zakupie nalezy wpisac /is na serwerze w celu odebrania uslugi"
        ];

        self::$TOP_INVENTORY = [0 => ["name" => "TOP SMIERCI",
            "item" => VanillaItems::TOTEM(),
            "slot" => 2,
            "callback" => fn($a, $b) => $a->getStatManager()->getStat(self::$STAT_DEATHS) - $b->getStatManager()->getStat(self::$STAT_DEATHS),
            "result" => fn($user) => $user->getStatManager()->getStat(self::$STAT_DEATHS),
        ],

            1 => ["name" => "TOP ZABOJSTWA",
                "item" => VanillaItems::DIAMOND_SWORD(),
                "slot" => 6,
                "callback" => fn($a, $b) => $a->getStatManager()->getStat(self::$STAT_KILLS) - $b->getStatManager()->getStat(self::$STAT_KILLS),
                "result" => fn($user) => $user->getStatManager()->getStat(self::$STAT_KILLS),
            ],

            2 => ["name" => "TOP ASYSTY",
                "item" => VanillaItems::GOLDEN_SWORD(),
                "slot" => 16,
                "callback" => fn($a, $b) => $a->getStatManager()->getStat(self::$STAT_ASSISTS) - $b->getStatManager()->getStat(self::$STAT_ASSISTS),
                "result" => fn($user) => $user->getStatManager()->getStat(self::$STAT_ASSISTS),
            ],

            3 => ["name" => "TOP PUNKTY",
                "item" => $itemFactory->get(ItemIds::ENCHANTED_BOOK),
                "slot" => 13,
                "callback" => fn($a, $b) => $a->getStatManager()->getStat(self::$STAT_POINTS) - $b->getStatManager()->getStat(self::$STAT_POINTS),
                "result" => fn($user) => "§e" . $user->getStatManager()->getStat(self::$STAT_POINTS)
            ],

            4 => ["name" => "TOP SPEDZONY CZAS",
                "item" => VanillaItems::CLOCK(),
                "slot" => 10,
                "callback" => fn($a, $b) => ($a->getStatManager()->getStat(self::$STAT_SPEND_TIME) + (Server::getInstance()->getPlayerExact($a->getName()) ? (time() - $a->getStatManager()->getStat(self::$STAT_LAST_JOIN_TIME)) : 0)) - ($b->getStatManager()->getStat(self::$STAT_SPEND_TIME) + (Server::getInstance()->getPlayerExact($b->getName()) ? (time() - $b->getStatManager()->getStat(self::$STAT_LAST_JOIN_TIME)) : 0)),
                "result" => fn($user) => TimeUtil::convertIntToStringTime(($user->getStatManager()->getStat(self::$STAT_SPEND_TIME) + ($user->getStatManager()->getStat(self::$STAT_SPEND_TIME) + (Server::getInstance()->getPlayerExact($user->getName()) ? (time() - $user->getStatManager()->getStat(self::$STAT_LAST_JOIN_TIME)) : 0))), "§7", "§7", true, false),
            ],

            5 => ["name" => "TOP WYKOPANE BLOKI",
                "item" => VanillaItems::DIAMOND_PICKAXE(),
                "slot" => 24,
                "callback" => fn($a, $b) => $a->getStatManager()->getStat(self::$STAT_BREAK_BLOCKS) - $b->getStatManager()->getStat(self::$STAT_BREAK_BLOCKS),
                "result" => fn($user) => $user->getStatManager()->getStat(self::$STAT_BREAK_BLOCKS),
            ],
        ];

        self::$WHITELIST_MESSAGE = str_repeat(" ", 2) . "§8<§7==========§8[§l§e WHITELIST §r§8]§7==========§8>" . "\n\n" . str_repeat(" ", 6) . "§r§7Discord§8: §e" . self::$DISCORD_LINK . "\n" . str_repeat(" ", 6) . "§r§7Strona§8: §ewww.lajtpvp.pl" . "\n" . str_repeat(" ", 6) . "§r§7Informacja§8: §ePrzerwa techniczna";

        self::$PLAYER_SKIN_PATH = Main::getInstance()->getDataFolder() . "/playersSkins/" . DIRECTORY_SEPARATOR;

        self::$GUILD_DATA_FOLDER = Main::getInstance()->getDataFolder()."data/guilds";
    }

    // GENERAL

    public static string $SERVER_NAME = "LajtPVP.EU";
    public static string $PERMISSION_TAG = "core.";

    // SERVICES

    public static array $SERVICES = ["1" => ["commandName" => "vip",
        "name" => "VIP na edycje",
        "command" => "pex user {nick} group set VIP"
    ],
        "2" => ["commandName" => "svip",
            "name" => "SVIP na edycje",
            "command" => "pex user {nick} group set SVIP"
        ],
        "3" => ["commandName" => "sponsor",
            "name" => "SPONSOR na edycje",
            "command" => "pex user {nick} group set SPONSOR"
        ],
        "4" => ["commandName" => "case16",
            "name" => "PremiumCase x16",
            "command" => "pcase {nick} 16"
        ],
        "5" => ["commandName" => "case32",
            "name" => "PremiumCase x32",
            "command" => "pcase {nick} 32"
        ],
        "6" => ["commandName" => "case64",
            "name" => "PremiumCase x64",
            "command" => "pcase {nick} 64"
        ],
        "7" => ["commandName" => "case128",
            "name" => "PremiumCase x128",
            "command" => "pcase {nick} 128"
        ],
        "8" => ["commandName" => "case256",
            "name" => "PremiumCase x256",
            "command" => "pcase {nick} 256"
        ],
        "9" => ["commandName" => "case512",
            "name" => "PremiumCase x512",
            "command" => "pcase {nick} 512"
        ],
        "10" => ["commandName" => "safe",
            "name" => "Sejf",
            "command" => "safe add {nick}"
        ],
        "11" => ["commandName" => "crowbar",
            "name" => "Lom",
            "command" => "lom {nick}"
        ],
        "12" => ["commandName" => "turbodrop1",
            "name" => "TurboDrop 1h",
            "command" => "turbodrop player 1h {nick}"
        ],
        "13" => ["commandName" => "turbodrop3",
            "name" => "TurboDrop 3h",
            "command" => "turbodrop player 3h {nick}"
        ],
        "14" => ["commandName" => "turbodrop5",
            "name" => "TurboDrop 5h",
            "command" => "turbodrop player 5h {nick}"
        ],
    ];

    // DEPOSIT

    public static int $REFILE_LIMIT = 8;
    public static int $ENCHANTED_LIMIT = 1;
    public static int $PEARL_LIMIT = 3;

    public static int $SNOWBALL_LIMIT = 8;
    public static int $ARROW_LIMIT = 32;
    public static int $THROWABLE_TNT_LIMIT = 2;

    // PHP CONSTANTS

    public static int $INT32_MIN = -0x80000000;
    public static int $INT32_MAX = 0x7fffffff;

    // VERIFY

    public const VERIFICATION_COORDINATES = [
        "x" => -13,
        "y" => 69,
        "z" => -43
    ];

    public static array $VERIFY = [];

    // ANTI LOGOUT

    public static int $DEFAULT_POINTS = 500;
    public static int $ANTYLOGOUT_TIME = 30;
    public static array $ANTYLOGOUT_COMMANDS = ["depozyt",
        "baza",
        "spawn",
        "home",
        "warp",
        "tpa",
        "tpaccept",
        "sethome",
        "delhome",
        "bazar",
        "ec",
        "skarbiec",
        "zaloz",
        "kit",
        "repair",
        "otchlan"
    ];

    public static int $TNT_MINIMUM_LEVEL = 50;
    public static int $LAST_KILL_TIME = 60 * 5;

    public static int $RESET_RANK_COST = 32;

    // MARKET

    public const MAX_PLAYER_OFFER = 1;
    public const MAX_VIP_OFFER = 2;
    public const MAX_SVIP_OFFER = 2;
    public const MAX_SPONSOR_OFFER = 2;

    public const MAX_ITEM_COST = 1728;

    // EFFECTS

    public static bool $EFFECT_HASTE_1 = true;
    public static bool $EFFECT_HASTE_2 = true;

    public static bool $EFFECT_STRENGTH_1 = false;
    public static bool $EFFECT_STRENGTH_2 = false;

    public static bool $EFFECT_SPEED_1 = false;
    public static bool $EFFECT_SPEED_2 = false;

    public static bool $EFFECT_JUMP_BOOST_1 = true;
    public static bool $EFFECT_JUMP_BOOST_2 = true;

    public static array $EFFECT_LIST_DATA = [];

    // HELP

    public static array $HELP_GENERAL_COMMANDS = ["spawn" => "Teleportuje na spawn serwera",
        "youtube" => "Informacje o randze YouTube",
        "vip" => "Informacje o randze VIP",
        "svip" => "Informacje o randze SVIP",
        "sponsor" => "Informacje o randze SPONSOR",
        "tpa" => "Mozliwosc przeteleportowania sie do innego gracza",
        "tpaccept" => "Akceptuj teleportacje od innego gracza",
        "warp" => "Mozliwosc przeteleportowania na warpy",
        "gracz" => "Statystyki gracza",
        "topka" => "Dostepne topki na serwerze",
        "helpop" => "Wysyla prywatna wiadomosc do administracji",
        "resetujranking" => "Resetuje ranking gracza",
        "msg" => "Wysyla prywatna wiadomosc do gracza",
        "r" => "Wysyla wiadomosc msg do poprzedniego gracza",
        "pattern" => "Mozliwosc zmiany wygladu sejfu",
        "repair" => "Naprawia item trzymany w rece",
        "repair all" => "Naprawia wszystkie itemy w ekwipunku",
        "kit" => "Zestawy przedmiotow do odebrania",
        "ignore" => "Ignoruje wiadomosci msg od uzytkownika",
        "incognito" => "Mozliwosc ukrycia skina i nicku gracza",
        "bazar" => "Oferty kupna itemow wystawionych przez graczy",
        "home" => "Mozliwosc teleportacji i ustawienia homa",
        "enchant" => "Otwiera przenosny enchant",
        "opis" => "Zmiana opisu sejfu",
        "depozyt" => "Otwiera depozyt",
        "crafting" => "Dostepne castomowe craftingi na serwerze",
        "efekty" => "Mozliwosc kupienia efektow za emeraldy",
        "list" => "Gracze online na serwerze",
        "plecak" => "Magazyn na itemy z dropu",
        "ping" => "Wyswietla twoj obecny ping"
    ];

    public static array $HELP_GUILD_COMMANDS = ["baza" => "Teleportuje gracza do bazy gildii",
        "panel" => "Otwiera panel gildii",
        "ff" => "Wlacza friendlyfire w gildii",
        "af" => "Wlacz aliancefire w gildii",
        "zaloz" => "Zaklada gildie",
        "itemy" => "Pokazuje itemy potrzebne na zalozenie gildii",
        "zapros" => "zaprasza gracza do gildii",
        "wyrzuc" => "Wyrzuca gracza z gildii",
        "wojna" => "Wypowiada wojne gildii",
        "information" => "Wyswietla informacje o gildii",
        "przedluz" => "przedluza waznosc gildii",
        "powieksz" => "powieksza teren gildii",
        "ustawbaze" => "Ustawia baze w gildii",
        "regeneracja" => "Mozliwosc zarzadzania regeneracja",
        "skarbiec" => "Otwiera skarbiec gildii"
    ];

    // DISCORD

    public static string $DISCORD_LINK = "discord.gg/lajtpvp";
    public static string $BAN_WEBHOOK = "https://discord.com/api/webhooks/878976143141773412/0ogs5j87YUI01gtl3eKbEYdCVWUBLql3aNN_4WjmMqSbX7nXPnhnV-gYFzSy3JThwleD";
    public static string $MUTE_WEBHOOK = "https://discord.com/api/webhooks/878976143141773412/0ogs5j87YUI01gtl3eKbEYdCVWUBLql3aNN_4WjmMqSbX7nXPnhnV-gYFzSy3JThwleD";
    public static string $ANTICHEAT_WEBHOOK = "https://discord.com/api/webhooks/879475469102239764/xZB4PsXaJ2bHfrb_b-hucGWJaAFgcmmOvgumh0SKcIPDJc9vA_htPZdifV0oqxU3eyNu";
    public static string $INCOGNITO_WEBHOOK = "https://discord.com/api/webhooks/877921522797408336/LIQPjoKhja6uij2di1oGU-1QKJkWhXb9kIprdmU6OqvIu_FWLpLJgwko5M39fiCeAavZ";
    public static string $HELPOP_WEBHOOK = "https://discord.com/api/webhooks/877921293788401704/frrc5EMSIQrIlrk1weq8lF35jizfqGVp63vtpLeArqK5gGHs3reftQtVAHrIPMsKu8av";
    public static string $ITEM_SHOP_WEBHOOK = "https://discord.com/api/webhooks/878978465607610388/0UPElBRLYnc3xvlsNFWDFQd9BLkM1D4BL0R7aPH-atp0EMsbk6cOxRxKm9IxgvoEN-7n";
    public static string $GUILD_REPORT_WEBHOOK = "https://discord.com/api/webhooks/878978337454850048/KWKFB9-C8qJKdfJtbwvY0oebLu8D5tV5M9MZsSQy_a2vgq-OZ6lyVaR5aKKQ20bocZD3";

    public static string $ADMIN_LOGGER_WEBHOOK = "";

    // TNT

    public static int $TNT_START = 8; // TODO: zmienic! (18)
    public static int $TNT_END = 20; // TODO: zmienic! (20)
    public static bool $TNTHASENABLED = false;

    // CRAFTING

    public static bool $DISABLE_DIAMOND_ITEMS = true;

    // CHAT

    public static bool $CHAT = true;
    public static int $ANTI_SPAM = 5;

    // TOP

    public static array $TOP_INVENTORY = [];

    // GUILDS

    public static string $GUILD_DATA_FOLDER;
    public static array $GUILD_ITEMS = [];

    public static int $CONQUER_TIME = 24; // godzina (24)
    public static int $EXPIRE_TIME = 72;
    public static int $DEFAULT_GUILD_SIZE = 30;
    public static int $MAX_GUILD_SIZE = 60;
    public static int $DEFAULT_GUILD_HEARTS = 3;
    public static int $DEFAULT_GUILD_HEALTH = 500;
    public static int $MAX_GUILD_HEALTH = 500;
    public static int $HEART_ATTACK_DELAY = 40;
    public static int $HEART_REGEN_START = 20 * 5;
    public static int $GOLEM_DEFAULT_HEALTH = 100;
    public static int $GOLEM_UPGRADE_HEALTH = 100;
    public static int $GOLEM_MAX_HEALTH = 1000;
    public static int $GOLEM_CLOSE_TIME = 60 * 5;
    public static int $INVITE_EXPIRE_TIME = 60;
    public static int $GUILD_TERRAIN_UPGRADE = 5;
    public static int $MAX_EXPIRE_TIME = 604800; // tydzien
    public static array $REGEN_BLOCK_IDS = [BlockLegacyIds::AIR,
        BlockLegacyIds::BEACON,
        BlockLegacyIds::TNT,
        BlockLegacyIds::FIRE,
        BlockLegacyIds::DIAMOND_BLOCK,
        BlockLegacyIds::EMERALD_BLOCK,
        BlockLegacyIds::COAL_BLOCK,
        BlockLegacyIds::IRON_BLOCK,
        BlockLegacyIds::GOLD_BLOCK
    ];
    public static int $GUILD_ALLIANCES_LIMIT = 3;
    public static int $GUILD_ALLIANCES_REQUEST_TIME = 60; // KARY
    public static int $GUILD_MEMBERS_LIMIT = 30;
    public static int $DEFAULT_GUILD_POINTS = 1000;
    public static int $WIN_WAR_POINTS = 200;
    public static int $LOSE_WAR_POINTS = 100;
    public static int $DESTROY_GUILD_POINTS = 300;
    public static int $BORDER_GUILD_PROTECTION = 100;
    public static int $GUILD_RENEWAL_COST = 64 * 5;
    public static int $GUILD_INCREASE_COST = 64;
    public static int $GUILD_DEFAULT_SLOTS = 15;
    public static int $GUILD_REGENERATION_COST = 10;
    public static int $BATTLE_TIME = 60 * 5;

    // PREMIUM DROP CHANCE

    public static int $VIP_DROP_CHANCE = 1;
    public static int $SVIP_DROP_CHANCE = 3;
    public static int $SPONSOR_DROP_CHANCE = 5;

    // KIT

    public static array $KITS = [];

    // INITIALIZED

    public static array $DROP = [];
    public static array $PREMIUMCASE_DROP = [];
    public static array $COBBLEX_DROP = [];
    public static array $HEAD_DROP = [];

    public static array $BLOCKS = [ItemIds::DIAMOND => BlockLegacyIds::DIAMOND_BLOCK,
        ItemIds::IRON_INGOT => BlockLegacyIds::IRON_BLOCK,
        ItemIds::GOLD_INGOT => BlockLegacyIds::GOLD_BLOCK,
        ItemIds::EMERALD => BlockLegacyIds::EMERALD_BLOCK,
        ItemIds::COAL => BlockLegacyIds::COAL_BLOCK
    ];

    // WORLDS

    public static string $LOBBY_WORLD = "lobby";
    public static string $DEFAULT_WORLD = "world";

    // TERRAIN

    public static array $TERRAIN_SETTINGS = [];

    public static string $TERRAIN_BREAK_BLOCK = "breakBreak";
    public static string $TERRAIN_PLACE_BLOCK = "placeBlock";
    public static string $TERRAIN_INTERACT = "interact";
    public static string $TERRAIN_FIGHTING = "fighting";
    public static string $TERRAIN_USE_COMMAND = "useCommand";
    public static string $TERRAIN_DAMAGE = "damage";
    public static string $TERRAIN_LOSE_FOOD = "loseFood";

    public static string $SPAWN_TERRAIN = "spawn";
    public static string $PVP_TERRAIN = "spawnpvp";

    // HOME

    public static int $HOME_LIMIT_PLAYER = 2;
    public static int $HOME_LIMIT_VIP = 4;
    public static int $HOME_LIMIT_SVIP = 5;
    public static int $HOME_LIMIT_SPONSOR = 8;

    // STONE GENERATOR

    public static int $STONE_REGENERATION = 2;

    // BOT

    public static array $BOT_MESSAGES = [
        "§8[§l§c@§r§8] §7Kontakt z administracja dostepny w grze poprzez §c/helpop",
        "§8[§l§c@§r§8] §7Jestes nowy? Koniecznie sprawdz §c/pomoc §7by poznac jak najwiecej swoich mozliwosci.",
        "§8[§l§c@§r§8] §7Chcesz zyskac wiecej mozliwosci i tym samym wesprzec serwer? Udaj sie na nasza strone www by zakupic interesujace cie przedmioty z naszego itemshopu.",
    ];

    public static int $BOT_MESSAGE_DELAY = 60;

    // RANKS

    public static array $VIP_DESCRIPTION = [];
    public static array $SVIP_DESCRIPTION = [];
    public static array $SPONSOR_DESCRIPTION = [];

    // WHITELIST

    public static string $WHITELIST_MESSAGE = "";

    // RANDOM TP

    public static array $RANDOM_TP = [];

    public const TYPE_SELF_TP = 0;
    public const TYPE_GROUP_1V1_TP = 1;
    public const TYPE_GROUP_TP = 2;

    // WORLD BORDER

    public static array $BORDER_DATA = [];
    public static int $BORDER = 800;

    // SPAWN

    public static int $SPAWN_PROTECT = 300;

    // STATS

    public static array $STATS = [];

    public static int $STAT_DEFAULT_POINTS = 500;

    public static string $STAT_POINTS = "points";
    public static string $STAT_KILLS = "kills";
    public static string $STAT_DEATHS = "deaths";
    public static string $STAT_ASSISTS = "assists";
    public static string $STAT_BREAK_BLOCKS = "breakBlocks";
    public static string $STAT_PLACE_BLOCKS = "placeBlocks";
    public static string $STAT_SPEND_TIME = "spendTime";
    public static string $STAT_LAST_JOIN_TIME = "lastJoinTime";
    public static string $STAT_ENDER_PEARLS = "pearls";
    public static string $STAT_GOLDEN_APPLES = "goldenApples";
    public static string $STAT_ENCHANTED_APPLES = "enchantedApples";
    public static string $STAT_ARROWS = "arrows";
    public static string $STAT_SNOWBALLS = "snowballs";
    public static string $STAT_THROWABLE_TNT = "throwableTnt";

    // TELEPORTATION

    public static int $TELEPORT = 10;
    public static int $RTP_TIME = 60 * 5;

    // ABYSS

    public static int $ABYSS_TIME = 60*4;

    // BACKPACK

    public static int $UPGRADE_BACKPACK_SIZE = 500;
    public static int $COST_UPGRADE_SIZE = 64;
    public static int $DEFAULT_BACKPACK_SIZE = 500;

    // INCOGNITO

    public static string $DATA_INCOGNITO_NAME = "incognitoName";
    public static string $DATA_NAME = "name";
    public static string $DATA_SKIN = "skin";
    public static string $DATA_GUILD_TAG = "tag";

    // JOIN BLOCK

    public static int $INCOGNITO_BLOCK = 60 * 60 * 2;
    public static int $CHEST_BLOCK_OPEN = 60 * 30;

    // CPS

    public static int $CPS_LIMIT = 12;
    public static int $CPS_COOL_DOWN = 3;

    // SKIN

    public static string $PLAYER_SKIN_PATH;

    // DEVICE IDS

    public static array $DEVICE_IDS = [-1 => "UNKNOWN",
        1 => "ANDROID",
        2 => "IOS",
        3 => "OSX",
        4 => "AMAZON",
        7 => "WINDOWS 10",
        8 => "WINDOWS 32",
        9 => "DEDICATED",
        10 => "TVOS",
        11 => "PLAYSTATION",
        12 => "NINTENDO",
        13 => "XBOX",
        14 => "WINDOWS PHONE"
    ];

    // CHEST LOCKER

    public static int $PLAYER_LOCK_LIMIT = 4;
    public static int $VIP_LOCK_LIMIT = 6;
    public static int $SVIP_LOCK_LIMIT = 8;
    public static int $SPONSOR_LOCK_LIMIT = 10;

    // LAST DATA

    public static string $TIME_TYPE = "time";

    public static int $WHO_TIME = 15;
    public static string $WHO = "who";

    public static int $THROWN_TNT_TIME = 5;
    public static string $THROWN_TNT = "thrownTnt";

    public static int $DROP_FASTPICKAXE_TIME = 5;
    public static string $DROP_FASTPICKAXE = "drop_fastpickaxe";

    public static int $LOW_DAMAGE_FASTPICKAXE_TIME = 5;
    public static string $LOW_DAMAGE_FASTPICKAXE = "low_damage_fastpickaxe";

    public static int $TNT_ON_TERRAIN_TIME = 5;
    public static string $TNT_ON_TERRAIN = "tntOnTerrain";

    public static int $TNT_ON_SELF_TERRAIN_TIME = 5;
    public static string $TNT_ON_SELF_TERRAIN = "tntOnSelfTerrain";

    public static int $SAFE_LAST_OPEN_TIME = 5;
    public static string $SAFE_LAST_OPEN = "safeLastOpen";

    public static int $GOLEM_DELAY_MESSAGE_TIME = 5;
    public static string $GOLEM_DELAY_MESSAGE = "golemDelayMessage";

    public static int $PROTECT_TERRAIN_INTERACT_TIME = 1;
    public static string $PROTECT_TERRAIN_INTERACT = "protectTerrainInteract";

    public static int $CHOOSE_SHOP_VILLAGER_TIME = 15;
    public static string $CHOOSE_SHOP_VILLAGER = "chooseShopVillager";

    public static int $BACKPACK_DROP_TIME = 30;
    public static string $BACKPACK_DROP = "backpackDrop";

    public static int $OPEN_SAFE_DELAY_TIME = 1;
    public static string $OPEN_SAFE_DELAY = "openSafeDelay";

    public static int $REPORT_DELAY_TIME = 30;
    public static string $REPORT_DELAY = "reportDelay";

    public static int $SAFE_TELEPORT_TIME = 3;
    public static string $SAFE_TELEPORT = "safeTeleport";

    public static int $INCOGNITO_CHANGE_TIME = 5;
    public static string $INCOGNITO_CHANGE = "incognitoChange";

    public static int $LAST_OPENED_CHEST_TIME = 5;
    public static string $LAST_OPENED_CHEST = "lastOpenedChestTime";
}