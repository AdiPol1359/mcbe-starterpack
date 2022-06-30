<?php

namespace core\util\utils;

class RandomUtil {

    public static function randomDraw(array $drops) {

        $results = [];
        $chances = [];

        for($i = 0; $i <= 100; $i++) {
            $numbers = [];

            for($y = 0; $y <= 10; $y++)
                $numbers[] = $y;

            $chances[$i] = $numbers;
        }

        $randomChance = mt_rand(0, 1000);

        if(isset($chances[intval(round($randomChance / 10))])) {
            if(($key = array_search(intval(round($randomChance / 1000)), $chances[intval(round($randomChance / 10))])) !== false) {
                foreach($drops as $drop => $dropChance) {
                    if(($dropChance * 10) >= $randomChance)
                        $results[] = $drop;
                }
            }
        }

        if(($count = count($results)) > 1)
            $results = $results[mt_rand(0, ($count - 1))];
        else
            $results = implode("", $results);

        return $results ?? null;
    }
}