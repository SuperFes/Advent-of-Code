<?php
$File = fopen("Calories.data", "rb");

$Elf   = 0;
$Elves = [];

$Calories = 0;

while (!feof($File)) {
    $Line = trim(fgets($File));

    if (empty($Line)) {
        $Elves[$Elf] = $Calories;

        ++$Elf;
        $Calories = 0;
    }
    else {
        $Calories += (int)$Line;
    }
}

sort($Elves);

echo array_pop($Elves) + array_pop($Elves) + array_pop($Elves) . PHP_EOL;
