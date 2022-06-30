<?php

namespace core\task\tasks;

use pocketmine\scheduler\AsyncTask;

class LogAsyncTask extends AsyncTask{

    private string $nick;
    private string $message;
    private string $path;

    public function __construct(string $nick, string $message, string $path){
        $this->nick = $nick;
        $this->message = $message;
        $this->path = $path;
    }

    public function onRun() {
        $data = date("d.m.Y | H:i");
        $message = "[".$data."] [".$this->nick."] > $this->message"."\n";

        if(!is_file($this->path))
            return;

        file_put_contents($this->path, $message, FILE_APPEND | LOCK_EX);
    }
}