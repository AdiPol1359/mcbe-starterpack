<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 21/11/2016
 * Time: 14:45
 */

namespace RWCORE;

use pocketmine\command\Command;
use onebone\economyapi\EconomyAPI;

use pocketmine\Player;

class Base{

    public $message = "";
    public $msg = "";

    public function sendError(Player $player, $erro, $price, $stack){
        switch($erro) {
            case 1:
                $this->message = $this->getPrefix()."§cSem acesso!";
                $player->sendMessage($this->message);
                break;
            case 2:
                $this->message = $this->getPrefix()."§cComando incorreto";
                $player->sendMessage($this->message);
                break;
            case 3:
                $this->message = $this->getPrefix()."§cDinheiro insuficiente! Preço: ".$price."$ Quantidade: ".$stack;
                $player->sendMessage($this->message);
                break;
            case 4:
                $this->message = $this->getPrefix()."§cInventario Cheio";
                $this->msg = $this->getPrefix()."§cOu pode estar com o Primeiro Slot Ocupado!";
                $player->sendMessage($this->message);
                $player->sendMessage($this->msg);
                break;
            case 5:
                $this->message = $this->getPrefix()."§cQuantidade não permitida: ".$stack;
                $player->sendMessage($this->message);
                break;
            case 6:
                $this->message = $this->getPrefix()."§cEste comando não existe, use /help";
                $player->sendMessage($this->message);
                break;
        }
    }

    public function getEconomy(){
        return EconomyAPI::getInstance();
    }

    public function sendCmd(Player $player, $msg){
        $cmd = $this->getPrefix()."§b/".$msg;
        $player->sendMessage($cmd);
    }

    public function getPrefix(){
        return Loader::Prefix;
    }

}