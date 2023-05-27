<?php

declare(strict_types=1);

namespace core\managers;

use core\items\custom\PremiumCase;
use core\Main;
use core\managers\drop\Drop;
use core\utils\BroadcastUtil;
use core\utils\Settings;
use core\utils\SoundUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;

class ServerManager {

    public const ITEMSHOP = "itemshop";
    public const PREMIUMCASE_STONE = "premiumcase";
    public const KIT = "kity";
    public const TNT = "tnt";
    public const EVENT = "event";
    public const GUILD_ITEMS = "guildItems";
    public const BACKUPS = "backups";
    public const RECORDER = "recorder";
    public const ENCHANTS = "enchants";
    public const WARS = "wars";
    
    private array $settings;

    public function __construct(private Main $plugin) {
        $itemFactory = ItemFactory::getInstance();

        $settings = [
            self::PREMIUMCASE_STONE => ["status" => false, "name" => "Drop PremiumCase", "item" => VanillaBlocks::CHEST()->asItem(), "slot" => 24, "message" => true],
            self::ITEMSHOP => ["status" => false, "name" => "ItemShop", "item" => VanillaBlocks::CHEST()->asItem(), "slot" => 21, "message" => true],
            self::KIT => ["status" => false, "name" => "Kity", "item" => VanillaItems::DIAMOND_SWORD(), "slot" => 20, "message" => true],
            self::TNT => ["status" => false, "name" => "TNT", "item" => VanillaBlocks::TNT()->asItem(), "slot" => 29, "message" => true],
            self::EVENT => ["status" => false, "name" => "Event jajka smoka", "item" => VanillaBlocks::DRAGON_EGG()->asItem(), "slot" => 22, "message" => false],
            self::GUILD_ITEMS => ["status" => false, "name" => "Itemy na gildie", "item" => $itemFactory->get(ItemIds::END_CRYSTAL), "slot" => 23, "message" => true],
            self::BACKUPS => ["status" => false, "name" => "System backopow", "item" => VanillaItems::TOTEM(), "slot" => 32, "message" => false],
            self::RECORDER => ["status" => false, "name" => "System nagran", "item" => $itemFactory->get(ItemIds::ENCHANTED_BOOK), "slot" => 33, "message" => false],
            self::ENCHANTS => ["status" => false, "name" => "Enchanty", "item" => VanillaBlocks::ENCHANTING_TABLE()->asItem(), "slot" => 30, "message" => true],
            self::WARS => ["status" => false, "name" => "Wojny", "item" => VanillaBlocks::ANVIL()->asItem(), "slot" => 31, "message" => true],
        ];

        foreach($plugin->getSettings()->getAll() as $name => $status) {
            if(!isset($settings[$name]))
                continue;

            $settings[$name]["status"] = $status;
        }

        $this->settings = $settings;

        if($this->settings[ServerManager::PREMIUMCASE_STONE]) {
            $this->premiumCaseInDrop(true);
        }
    }

    public function save() : void {
        foreach($this->settings as $settingName => $settingData)
            $this->plugin->getSettings()->set($settingName, $settingData["status"]);

        try {
            $this->plugin->getSettings()->save();
        } catch(\JsonException $e) {
        }
    }

    public function isSettingEnabled(string $name) : bool {
        return $this->settings[$name]["status"] ?? false;
    }

    public function setSetting(string $name, bool $status) : void {
        $this->settings[$name]["status"] = $status;

        if($name === self::PREMIUMCASE_STONE) {
            $this->premiumCaseInDrop($status);
        }
    }

    public function notify(string $name) : void {
        if(!isset($this->settings[$name]) || !$this->settings[$name]["message"])
            return;

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($name) : void {
            $onlinePlayer->sendTitle("§l§e" . strtoupper($this->settings[$name]["name"]), "§7zostal".($this->settings[$name]["name"][strlen($this->settings[$name]["name"]) - 1] === "y" ? "y" : "")." " . (self::isSettingEnabled($name) ? "§aWLACZONY" : "§cWYLACZONY"));
            SoundUtil::addSound([$onlinePlayer], $onlinePlayer->getPosition(), "firework.blast");
        });
    }

    public function premiumCaseInDrop(bool $status) : void {
        $founded = false;

        foreach(Settings::$DROP as $dataKey => $dropData) {
            if(isset($dropData["external"])) {
                $founded = true;
            }
        }

        $dropManager = Main::getInstance()->getDropManager();

        if($status) {
            if(!$founded) {
                $drop = new Drop(
                    count($dropManager->getDrop()),
                    "premiumcase",
                    "PremiumCase",
                    "§e",
                    0.2,
                    false,
                    12,
                    23,
                    false,
                    false,
                    "§8({COLOR}+{COUNT}§8) {COLOR}{NAME}",
                    ["depositItem" => false,
                        "depositName" => ""
                    ],
                    [],
                    [VanillaItems::STONE_PICKAXE(),
                        VanillaItems::GOLDEN_PICKAXE(),
                        VanillaItems::IRON_PICKAXE(),
                        VanillaItems::DIAMOND_PICKAXE()
                    ],
                    ["min" => 1,
                        "max" => 1
                    ],
                    ["what" => (new PremiumCase())->__toItem(),
                        "from" => VanillaBlocks::STONE()
                    ]
                );

                $dropManager->addDrop($drop->getDropId(), $drop);
            }
        }else {
            if($founded) {
                $dropManager->removeDrop(count($dropManager->getDrop()) - 1);
            }
        }
    }
    public function getSettings() : array {
        return $this->settings;
    }
}