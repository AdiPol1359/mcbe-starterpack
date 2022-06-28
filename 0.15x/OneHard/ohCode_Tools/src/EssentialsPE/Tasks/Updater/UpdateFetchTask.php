<?php
namespace EssentialsPE\Tasks\Updater;

use EssentialsPE\Loader;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;

class UpdateFetchTask extends AsyncTask{
    /** @var string */
    private $build;
    /** @var bool */
    private $install;

    /**
     * @param $build
     * @param $install
     */
    public function __construct($build, $install){
        $this->build = $build;
        $this->install = $install;
    }

    public function onRun(){
        if($this->build === "beta"){
            $url = "https://api.github.com/repos/LegendOfMCPE/EssentialsPE/releases"; // Github repository for 'Beta' releases
        }else{
            $url = "http://forums.pocketmine.net/api.php?action=getResource&value=886"; // PocketMine repository for 'Stable' releases
        }
        $i = json_decode(Utils::getURL($url), true);

        $r = [];
        switch(strtolower($this->build)){
            case "stable":
            default:
                $r["version"] = $i["version_string"];
                $r["downloadURL"] = "http://forums.pocketmine.net/plugins/essentialspe.886/download?version=" . $i["current_version_id"];
                break;
            case "beta":
                $i = $i[0]; // Grab the latest version from Github releases... Doesn't matter if it's Beta or Stable :3
                $r["version"] = substr($i["name"], 13);
                $r["downloadURL"] = $i["assets"][0]["browser_download_url"];
                break;
        }
        $this->setResult($r);
    }

    /**
     * @param Server $server
     */
}