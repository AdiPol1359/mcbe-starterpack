<?php

declare(strict_types=1);

namespace core\tasks\async;

use core\Main;
use core\managers\wing\Wings;
use core\utils\Settings;
use core\utils\SkinUtil;
use pocketmine\entity\Skin;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class SkinCheckAsyncTask extends AsyncTask {

    private string $playerName;
    private string $skinId;
    private string $defaultGeometryName;
    private string $defaultGeometryData;
    private string $skinsPath;
    private ?Wings $playerWings;

    private ?Skin $clearSkin = null;
    private ?Skin $skin = null;

    public function __construct(string $playerName, string $skinId, string $defaultGeometryName, string $defaultGeometryData, string $skinPath, ?Wings $playerWings = null) {
        $this->playerName = $playerName;
        $this->skinId = $skinId;
        $this->defaultGeometryName = $defaultGeometryName;
        $this->defaultGeometryData = $defaultGeometryData;
        $this->skinsPath = $skinPath;
        $this->playerWings = $playerWings;
    }

    public function onRun() : void {
        $resource = imagecreatefrompng(($this->skinsPath ? : Settings::$PLAYER_SKIN_PATH) . $this->playerName . ".png");
        imagecolortransparent($resource, imagecolorallocatealpha($resource, 0, 0, 0, 127));

        $this->clearSkin = new Skin($this->skinId, SkinUtil::skinImageToBytes($resource), "", $this->defaultGeometryName, $this->defaultGeometryData);
        $this->skin = $this->clearSkin;
    }

    public function onCompletion() : void {
        $player = Server::getInstance()->getPlayerByPrefix($this->playerName);

        if(!$player) {
            return;
        }

        if($this->playerWings) {
            $resource = imagecreatefrompng(($this->skinsPath ? : Settings::$PLAYER_SKIN_PATH) . $this->playerName . ".png");
            imagecolortransparent($resource, imagecolorallocatealpha($resource, 0, 0, 0, 127));
            $playerSkin = $resource;

            $linkedImage = imagecopymerge($this->playerWings->getImage(), $playerSkin, 0, 0, 0, 0, imagesx($playerSkin), imagesy($playerSkin), 100);
            $this->skin = new Skin($this->skinId, SkinUtil::skinImageToBytes($linkedImage), "", $this->playerWings->getGeometryName(), $this->playerWings->getGeometryData());
        }

        $player->setSkin($this->skin);
        $player->sendSkin();

        Main::getInstance()->getSkinManager()->setPlayerSkin($player, $this->clearSkin);
    }
}