<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Jump extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "jump", "Teleport you to the block you're looking at", "/jump", false, ["j", "jumpto"]);
        $this->setPermission("essentials.jump");
    }

    /**
     * @param CommandSender $sender
     * @param string $alias
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, $alias, array $args){
        if(!$this->testPermission($sender)){
            return false;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage($this->getConsoleUsage());
            return false;
        }
        if(count($args) !== 0){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
        }
        $transparent = [6, 8, 9, 10, 11, 31, 32, 36, 37, 38, 39, 40, 50,
            51, 55, 59, 63, 68, 69, 75, 76, 77, 83, 90, 104, 105, 115,
            119, 120, 122, 127, 131, 132, 141, 142, 143, 175, 176, 177];
        $block = $sender->getTargetBlock(100, $transparent);
        if($block === null){
            $sender->sendMessage(TextFormat::RED . "There isn't a reachable block");
            return false;
        }
        if(!$sender->getLevel()->getBlock($block->add(0, 2))->isSolid()){
            $sender->teleport($block->add(0, 1));
            return true;
        }

        $side = $sender->getDirection();
        if($side === 0){
            $side = 3;
        }elseif($side === 1){
            $side = 4;
        }elseif($side === 2){
            $side = 2;
        }elseif($side === 3){
            $side = 5;
        }
        if(!$block->getSide($side)->isSolid()){
            $sender->teleport($block);
        }
        return true;
    }
}
