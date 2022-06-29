<?php

namespace core\fakeinventory\inventory;

use core\fakeinventory\FakeInventory;
use core\Main;
use core\manager\managers\bossbar\BossbarManager;
use core\manager\managers\item\LoreCreator;
use core\manager\managers\PacketManager;
use core\manager\managers\quest\QuestManager;
use core\manager\managers\ScoreboardManager;
use core\manager\managers\SettingsManager;
use core\manager\managers\SoundManager;
use core\user\UserManager;
use core\util\utils\StringUtil;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;

class SettingsInventory extends FakeInventory {

    private array $information = [
        SettingsManager::SCOREBOARD => "SCOREBOARD",
        SettingsManager::FULL_EQ => "INFORMACJE O PELNYM EQ",
        SettingsManager::BOT_NOTIFICATION => "WIADOMOSCI BOTA",
        SettingsManager::TRADE_REQUEST => "PROSBY O WYMIANE",
        SettingsManager::QUEST_BOSSBAR => "BOSSBAR Z QUESTAMI",
        SettingsManager::HAZARD_INFO => "INFORMACJE O HAZARDZIE"
    ];

    private array $convenience = [
        SettingsManager::AUTO_SPRINT => "AUTO SPRINT",
        SettingsManager::NIGHT_VISION => "WIDZENIE W CIEMNOSCI",
        SettingsManager::ITEM_STATUS => "STATUS PRZEDMIOTU NA PASKU EXPA",
        SettingsManager::COORDINATES => "KORDYNATY"
    ];

    private array $optimization = [
        SettingsManager::BLOCK_PARTICLE => "PARTICLESY PODCZAS KOPANIA",
        SettingsManager::PARTICLES => "OGOLNE PARTICLESY",
        SettingsManager::SOUNDS => "DZWIEKI PO WPISANIU KOMEND",
        SettingsManager::PLAYER_PARTICLES => "PARTICLESY GRACZY"
    ];

    public function __construct(Player $player) {
        parent::__construct($player, "§l§9USTAWIENIA", self::SMALL);
        $this->setItems();
    }

    public function setItems() : void{

        $this->fillBars();

        $user = UserManager::getUser($this->player->getName());

        if(!$user)
            return;

        $info = Item::get(Item::WRITABLE_BOOK);
        $info->setCustomName(StringUtil::correctText("§r§7[§8---===§7[ §9§lINFORMACJE§r§7 ]§8===---§7]", 44));

        $loreCreator = new LoreCreator();
        $loreCreator->setCustomName($info->getCustomName(), true);

        $infoLore = [" "];

        foreach($this->information as $settingName => $str)
            $infoLore[] = "§r§7".$str."§8: §9".($user->isSettingEnabled($settingName) ? "§aON" : "§cOFF");

        $infoLore = array_map(function (string $entry) : string{
            return $entry;
        }, array_values($infoLore));

        $loreCreator->setLore($infoLore);
        $loreCreator->alignCustomName(64);
        $loreCreator->alignLore();

        $info->setCustomName($loreCreator->getCustomName());
        $info->setLore($loreCreator->getLore());

        $facilitation = Item::get(Item::COMPASS);
        $facilitation->setCustomName(StringUtil::correctText("§r§7[§8---===§7[ §9§lULATWIENIA§r§7 ]§8===---§7]", 44));

        $loreCreator = new LoreCreator();
        $loreCreator->setCustomName($facilitation->getCustomName(), true);

        $facilitationLore = [" "];

        foreach($this->convenience as $settingName => $str)
            $facilitationLore[] = "§r§7".$str."§8: §9".($user->isSettingEnabled($settingName) ? "§aON" : "§cOFF");

        $facilitationLore = array_map(function (string $entry) : string{
            return $entry;
        }, array_values($facilitationLore));

        $loreCreator->setLore($facilitationLore);
        $loreCreator->alignCustomName(64);
        $loreCreator->alignLore();

        $facilitation->setCustomName($loreCreator->getCustomName());
        $facilitation->setLore($loreCreator->getLore());

        $optimization = Item::get(Item::NETHER_STAR);
        $optimization->setCustomName(StringUtil::correctText("§r§7[§8---===§7[ §9§lOPTYMALIZACJA§r§7 ]§8===---§7]", 44));

        $loreCreator = new LoreCreator();
        $loreCreator->setCustomName($optimization->getCustomName(), true);

        $optimizationLore = [" "];

        foreach($this->optimization as $settingName => $str)
            $optimizationLore[] = "§r§7".$str."§8: §9".($user->isSettingEnabled($settingName) ? "§aON" : "§cOFF");

        $optimizationLore = array_map(function (string $entry) : string{
            return $entry;
        }, array_values($optimizationLore));

        $loreCreator->setLore($optimizationLore);
        $loreCreator->alignCustomName(64);
        $loreCreator->alignLore();

        $optimization->setCustomName($loreCreator->getCustomName());
        $optimization->setLore($loreCreator->getLore());

        $this->setItemAt(1, 1, $info);
        $this->setItemAt(1, 2, $facilitation);
        $this->setItemAt(1, 3, $optimization);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {
        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        $user = UserManager::getUser($player->getName());
        $slots = [11, 12, 13, 14, 15, 16, 17];

        if($sourceItem->getId() !== Item::IRON_BARS){
            if($slot === $this->getSlotAt(1, 1)){

                foreach($slots as $slot)
                    $this->setItem($slot, Item::get(Item::IRON_BARS)->setCustomName(" "));

                $x = 3;

                foreach($this->information as $info => $description){

                    if($user->isSettingEnabled($info)) {
                        $book = Item::get(Item::ENCHANTED_BOOK);
                        $book->setCustomName("§7".$description." §l§aWLACZONE");
                    }else {
                        $book = Item::get(Item::BOOK);
                        $book->setCustomName("§7".$description." §l§cWYLACZONE");
                    }

                    $book->getNamedTag()->setString("setting", $info);
                    $book->getNamedTag()->setString("description", $description);

                    $this->setItemAt($x, 2, $book);
                    $x++;
                }
            }

            if($slot === $this->getSlotAt(1, 2)){

                foreach($slots as $slot)
                    $this->setItem($slot, Item::get(Item::IRON_BARS)->setCustomName(" "));

                $x = 3;

                foreach($this->convenience as $info => $description){

                    if($user->isSettingEnabled($info)) {
                        $book = Item::get(Item::ENCHANTED_BOOK);
                        $book->setCustomName("§7".$description." §l§aWLACZONE");
                    }else {
                        $book = Item::get(Item::BOOK);
                        $book->setCustomName("§7".$description." §l§cWYLACZONE");
                    }

                    $book->getNamedTag()->setString("setting", $info);
                    $book->getNamedTag()->setString("description", $description);

                    $this->setItemAt($x, 2, $book);
                    $x++;
                }
            }

            if($slot === $this->getSlotAt(1, 3)){

                foreach($slots as $slot)
                    $this->setItem($slot, Item::get(Item::IRON_BARS)->setCustomName(" "));

                $x = 3;

                foreach($this->optimization as $info => $description){

                    if($user->isSettingEnabled($info)) {
                        $book = Item::get(Item::ENCHANTED_BOOK);
                        $book->setCustomName("§7".$description." §l§aWLACZONE");
                    }else {
                        $book = Item::get(Item::BOOK);
                        $book->setCustomName("§7".$description." §l§cWYLACZONE");
                    }

                    $book->getNamedTag()->setString("setting", $info);
                    $book->getNamedTag()->setString("description", $description);

                    $this->setItemAt($x, 2, $book);
                    $x++;
                }
            }

            $namedTag = $sourceItem->getNamedTag();

            if($namedTag->hasTag("setting")) {

                $setting = $namedTag->getString("setting");
                $description = $namedTag->getString("description");

                $user->switchSetting($setting);

                switch($setting){

                    case SettingsManager::NIGHT_VISION:

                        if($user->isSettingEnabled($setting)) {
                            $effect = new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION));
                            $effect->setVisible(false);
                            $effect->setDuration(INT32_MAX);
                            $player->addEffect($effect);
                            $book = Item::get(Item::ENCHANTED_BOOK);
                            $book->setCustomName("§7".$description." §l§aWLACZONE");
                        }else{
                            $player->removeEffect(Effect::NIGHT_VISION);
                            $book = Item::get(Item::BOOK);
                            $book->setCustomName("§7".$description." §l§cWYLACZONE");
                        }

                        break;

                    case SettingsManager::COORDINATES:

                        if($user->isSettingEnabled($setting)) {
                            $pk = new GameRulesChangedPacket();
                            $pk->gameRules = ["showcoordinates" => [1, true]];
                            $player->dataPacket($pk);
                            $book = Item::get(Item::ENCHANTED_BOOK);
                            $book->setCustomName("§7".$description." §l§aWLACZONE");
                        }else{
                            $pk = new GameRulesChangedPacket();
                            $pk->gameRules = ["showcoordinates" => [1, false]];
                            $player->dataPacket($pk);
                            $book = Item::get(Item::BOOK);
                            $book->setCustomName("§7".$description." §l§cWYLACZONE");
                        }

                        break;

                    case SettingsManager::SCOREBOARD:
                        if($user->isSettingEnabled($setting)) {
                            $book = Item::get(Item::ENCHANTED_BOOK);
                            $book->setCustomName("§7".$description." §l§aWLACZONE");
                        }else{
                            ScoreboardManager::removeScoreboard($player);
                            $book = Item::get(Item::BOOK);
                            $book->setCustomName("§7".$description." §l§cWYLACZONE");
                        }
                        break;

                    case SettingsManager::QUEST_BOSSBAR:
                        if($user->isSettingEnabled($setting)) {
                            if(BossbarManager::getBossbar($player) === null)
                                QuestManager::send($player);
                            $book = Item::get(Item::ENCHANTED_BOOK);
                            $book->setCustomName("§7".$description." §l§aWLACZONE");
                        }else{

                            if(BossbarManager::getBossbar($player) !== null) {
                                BossbarManager::getBossbar($player)->hideFrom($player);
                                BossbarManager::unsetBossbar($player);
                            }

                            $book = Item::get(Item::BOOK);
                            $book->setCustomName("§7".$description." §l§cWYLACZONE");
                        }
                        break;

                    case SettingsManager::PLAYER_PARTICLES:
                        if($user->isSettingEnabled($setting)) {
                            $book = Item::get(Item::ENCHANTED_BOOK);
                            $book->setCustomName("§7".$description." §l§aWLACZONE");

                            if(($key = array_search($player->getName(), Main::$playerParticles)) === false)
                                Main::$playerParticles[] = $player->getName();

                        }else{
                            $book = Item::get(Item::BOOK);
                            $book->setCustomName("§7".$description." §l§cWYLACZONE");

                            if(($key = array_search($player, Main::$playerParticles)) !== false)
                                unset(Main::$playerParticles[$key]);
                        }
                        break;

                    default:
                        if($user->isSettingEnabled($setting)) {
                            $book = Item::get(Item::ENCHANTED_BOOK);
                            $book->setCustomName("§7".$description." §l§aWLACZONE");
                        }else{
                            $book = Item::get(Item::BOOK);
                            $book->setCustomName("§7".$description." §l§cWYLACZONE");
                        }
                        break;
                }

                $book->getNamedTag()->setString("setting", $setting);
                $book->getNamedTag()->setString("description", $description);

                $this->setItems();
                $this->setItem($slot, $book);
            }

            PacketManager::unClickButton($player);
        }

        return true;
    }
}