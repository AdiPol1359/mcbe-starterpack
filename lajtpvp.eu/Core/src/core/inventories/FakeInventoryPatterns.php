<?php

declare(strict_types=1);

namespace core\inventories;

abstract class FakeInventoryPatterns {

    const PATTERN_FILL_CORNERS_SMALL = [0, 1, 7, 8, 9, 17, 18, 19, 25, 26];
    const PATTERN_FILL_CORNERS = [0, 1, 7, 8, 9, 17, 36, 44, 45, 46, 52, 53];
    const PATTERN_FILL_UP_AND_DOWN = [0, 1, 2, 3, 4, 5, 6, 7, 8, 45, 46, 47, 48, 49, 50, 51, 52, 53];
    const PATTERN_FILL_UP_AND_DOWN_WITH_ROWS = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 17, 36, 44, 45, 46, 47, 48, 50, 51, 52, 53];
}