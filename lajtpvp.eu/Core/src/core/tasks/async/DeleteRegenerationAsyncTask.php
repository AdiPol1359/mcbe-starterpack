<?php

declare(strict_types=1);

namespace core\tasks\async;

use core\utils\FileUtil;
use pocketmine\scheduler\AsyncTask;

class DeleteRegenerationAsyncTask extends AsyncTask {

    private string $guildTag;
    private string $dataFolder;

    public function __construct(string $guildTag, string $dataFolder) {
        $this->guildTag = $guildTag;
        $this->dataFolder = $dataFolder;
    }

    public function onRun() : void {
        FileUtil::deleteDir($this->dataFolder . $this->guildTag);
    }
}