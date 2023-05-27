<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\Main;
use core\utils\RandomUtil;
use core\utils\Settings;
use core\utils\SkinUtil;
use core\utils\WebhookUtil;
use core\webhooks\types\Message;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class IncognitoInventory extends FakeInventory {

    public function __construct(private Player $player) {
        parent::__construct("§l§eINCOGNITO");
    }

    public function setItems() : void {
        $this->fill();

        $user = Main::getInstance()->getUserManager()->getUser($this->player->getName());

        if(!$user) {
            return;
        }

        $itemFactory = ItemFactory::getInstance();
        $incognitoManager = $user->getIncognitoManager();

        $nameTag = $itemFactory->get(ItemIds::NAME_TAG)->setCustomName("§7Nick §8(".($incognitoManager->getIncognitoData(Settings::$DATA_NAME) ? "§aUkryte" : "§cNie ukryte")."§8)");
        $head = $itemFactory->get(ItemIds::MOB_HEAD, 3)->setCustomName("§7Skin §8(".($incognitoManager->getIncognitoData(Settings::$DATA_SKIN) ? "§aUkryte" : "§cNie ukryte")."§8)");
        $totem = $itemFactory->get(ItemIds::TOTEM)->setCustomName("§7Tag gildii §8(".($incognitoManager->getIncognitoData(Settings::$DATA_GUILD_TAG) ? "§aUkryte" : "§cNie ukryte")."§8)");

        $this->setItem(11, $nameTag, true, true);
        $this->setItem(13, $head, true, true);
        $this->setItem(15, $totem, true, true);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());
        $incognitoManager = $user->getIncognitoManager();
        $skinManager = Main::getInstance()->getSkinManager();
        $wingsManager = Main::getInstance()->getWingsManager();

        if(!$user->hasLastData(Settings::$INCOGNITO_CHANGE)) {
            switch($sourceItem->getId()) {
                case ItemIds::NAME_TAG:

                    if(!$incognitoManager->getIncognitoData(Settings::$DATA_NAME)) {
                        $name = RandomUtil::randomIncognitoName();
                        $incognitoManager->setIncognitoData(Settings::$DATA_INCOGNITO_NAME, $name);
                        WebhookUtil::sendWebhook(new Message("**".$player->getName()."** » **".$name."**"), Settings::$INCOGNITO_WEBHOOK);
                    }

                    $incognitoManager->setIncognitoData(Settings::$DATA_NAME, !$incognitoManager->getIncognitoData(Settings::$DATA_NAME));
                    $this->setItems();

                    $user->setLastData(Settings::$INCOGNITO_CHANGE, (time() + Settings::$INCOGNITO_CHANGE_TIME), Settings::$TIME_TYPE);

                    break;

                case ItemIds::MOB_HEAD:

                    if(!$incognitoManager->getIncognitoData(Settings::$DATA_SKIN))
                        $player->setSkin(new Skin($player->getSkin()->getSkinId(), SkinUtil::getSkinFromPath(Main::getInstance()->getDataFolder() . "default/incognito.png"), "", $skinManager->getDefaultGeometryName(), $skinManager->getDefaultGeometryData()));
                    else {
                        $newSkin = new Skin($player->getSkin()->getSkinId(), SkinUtil::skinImageToBytes(Main::getInstance()->getSkinManager()->getPlayerSkinImage($player->getName())), "", $skinManager->getDefaultGeometryName(), $skinManager->getDefaultGeometryData());
                        $wings = $wingsManager->getPlayerWings($player->getName());

                        if($wings !== null)
                            $wingsManager->setWings($player, $wings);
                        else
                            $player->setSkin($newSkin);
                    }

                    $player->sendSkin();

                    $incognitoManager->setIncognitoData(Settings::$DATA_SKIN, !$incognitoManager->getIncognitoData(Settings::$DATA_SKIN));
                    $this->setItems();

                    $user->setLastData(Settings::$INCOGNITO_CHANGE, (time() + Settings::$INCOGNITO_CHANGE_TIME), Settings::$TIME_TYPE);
                    break;

                case ItemIds::TOTEM:
                    $incognitoManager->setIncognitoData(Settings::$DATA_GUILD_TAG, !$incognitoManager->getIncognitoData(Settings::$DATA_GUILD_TAG));
                    $this->setItems();

                    $user->setLastData(Settings::$INCOGNITO_CHANGE, (time() + Settings::$INCOGNITO_CHANGE_TIME), Settings::$TIME_TYPE);
                    break;
            }
        }

        $this->unClickItem($player);
        return true;
    }
}