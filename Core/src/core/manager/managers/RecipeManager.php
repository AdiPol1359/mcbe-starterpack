<?php

namespace core\manager\managers;

use core\manager\BaseManager;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\item\Item;

class RecipeManager extends BaseManager {

    public static function init() : void{
        $recipes = [
            new ShapedRecipe(["GGG", "GJG", "GGG"], ["G" => Item::get(41), "J" => Item::get(260)], [Item::get(466)])
        ];

        foreach($recipes as $recipe)
            self::getServer()->getCraftingManager()->registerRecipe($recipe);
    }
}