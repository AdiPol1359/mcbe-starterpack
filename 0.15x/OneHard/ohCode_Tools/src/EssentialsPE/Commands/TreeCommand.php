<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\block\Sapling;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TreeCommand extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "tree", "Spawns a tree", "/tree <tree|birch|redwood|jungle>", false);
        $this->setPermission("essentials.tree");
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
        if(count($args) !== 1){
            $sender->sendMessage($this->getUsage());
            return false;
        }
        $block = $sender->getTargetBlock(100, [0, 8, 9, 10, 11]);
        if($block === null){
            $sender->sendMessage(TextFormat::RED . "There isn't a reachable block");
            return false;
        }
        switch(strtolower($args[0])){
            case "tree":
                $type = Sapling::OAK;
                break;
            case "birch":
                $type = Sapling::BIRCH;
                break;
            case "redwood":
                $type = Sapling::SPRUCE;
                break;
            case "jungle":
                $type = Sapling::JUNGLE;
                break;
            /*case "redmushroom":
                $type = Sapling::RED_MUSHROOM;
                break;
            case "brownmushroom":
                $type = Sapling::BROWN_MUSHROOM;
                break;
            case "swamp":
                $type = Sapling::SWAMP;
                break;*/
            default:
                $sender->sendMessage(TextFormat::RED . "Invalid tree type, try with:\n<tree|birch|redwood|jungle>");
                return false;
                break;
        }
        if($sender->getLevel()->setBlock($block->add(0, 1), new Sapling($type), true, true)){
            $sender->getLevel()->getBlock($block->add(0, 1))->onActivate(new Item(Item::DYE, 15));
            $sender->sendMessage(TextFormat::GREEN . "Tree spawned!");
        }
        return true;
    }
} 