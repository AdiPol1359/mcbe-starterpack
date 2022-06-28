<?php
namespace EssentialsPE\Tasks\Updater;

use EssentialsPE\Loader;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class UpdateInstallTask extends AsyncTask{
    /** @var string */
    private $url;
    /** @var string */
    private $pluginPath;
    /** @var string */
    private $newVersion;
    /** @var Loader */
    private $plugin;

    /**
     * @param Loader $plugin
     * @param $url
     * @param $pluginPath
     * @param $newVersion
     */
    public function __construct(Loader $plugin, $url, $pluginPath, $newVersion){
        $this->url = $url;
        $this->pluginPath = $pluginPath;
        $this->newVersion = $newVersion;
        $this->plugin = $plugin;
    }

    public function onRun(){
        if(file_exists($this->pluginPath . "EssentialsPE.phar")){
            unlink($this->pluginPath . "EssentialsPE.phar");
        }
        $file = fopen($this->pluginPath . "EssentialsPE.phar", 'w+');
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0 PocketMine-MP"]);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $file);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        file_put_contents($this->pluginPath . "EssentialsPE.phar", curl_exec($ch));
        curl_close($ch);
        fclose($file);
    }

    /**
     * @param Server $server
     */
    public function onCompletion(Server $server){
        $server->getLogger()->info(TextFormat::AQUA . "[EssentialsPE]" . TextFormat::YELLOW . " Successfully updated to version " . TextFormat::GREEN . $this->newVersion . TextFormat::YELLOW . ". To start using the new features, please fully restart your server.");
        $this->plugin->scheduleUpdaterTask();
    }
}