<?php

declare(strict_types=1);

namespace Gildie\fakeinventory;

use Gildie\guild\Guild;
use pocketmine\Player;
use pocketmine\item\Item;

class SkarbiecInventory extends FakeInventory {

    private $guild;

    public function __construct(Player $player, Guild $guild) {
        $this->guild = $guild;
        parent::__construct($player, "§r§l§4SKARBIEC GILDYJNY");
        $this->setItems();
    }

    public function onTransaction(Player $player, ?Item $item) : void {
        foreach($this->guild->getSkarbiecViewers() as $nick => $inv) {
            if($nick == $player->getName())
                continue;

            $inv->setItems();
        }
    }

    public function onOpen(Player $who): void {
        $this->guild->addSkarbiecViewer($who, $this);
        parent::onOpen($who);
    }

    public function onClose(Player $who): void {
        $this->guild->removeSkarbiecViewer($who);
        parent::onClose($who);
    }

    public function setItems() : void {
        $guild = $this->guild;
        $this->setContents($guild->getSkarbiecItems());
    }

    public function getGuild() : Guild {
        return $this->guild;
    }
}