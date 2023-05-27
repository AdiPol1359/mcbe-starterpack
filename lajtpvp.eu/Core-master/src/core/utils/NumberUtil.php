<?php

declare(strict_types=1);

namespace core\utils;

final class NumberUtil {

    private function __construct() {}

    public static function numberToRoman(int $number) : string {
        $map = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
        $returnValue = '';
        while($number > 0) {
            foreach($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    public static function romanToNumber(string $number) : int {
        $romans = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        ];

        $result = 0;

        foreach ($romans as $key => $value) {
            while (str_starts_with($number, $key)) {
                $result += $value;
                $number = substr((string)$value, strlen($key));
            }
        }

        return $result;
    }
}