#!/usr/bin/php -q
<?php
function Number2Number(string $Number): string {
    $Reverse = strrev($Number);

    if ($Number === 'one' || $Reverse === 'one') {
        return '1';
    }
    else if ($Number === 'two' || $Reverse === 'two') {
        return '2';
    }
    else if ($Number === 'three' || $Reverse === 'three') {
        return '3';
    }
    else if ($Number === 'four' || $Reverse === 'four') {
        return '4';
    }
    else if ($Number === 'five' || $Reverse === 'five') {
        return '5';
    }
    else if ($Number === 'six' || $Reverse === 'six') {
        return '6';
    }
    else if ($Number === 'seven' || $Reverse === 'seven') {
        return '7';
    }
    else if ($Number === 'eight' || $Reverse === 'eight') {
        return '8';
    }
    else if ($Number === 'nine' || $Reverse === 'nine') {
        return '9';
    }

    return $Number;
}

$File = 'Data/Data.txt';

$Total1 = 0;
$Total2 = 0;

$Thing = fopen($File, 'rb');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    $Left = 0;
    $Right = 0;

    $Len = strlen($Line);

    if ($Len === 0) continue;

    for ($l = 0; $l < $Len; $l++) {
        if (preg_match('/\d/', $Line[$l])) {
            $Left = $Line[$l];

            break;
        }
    }

    for ($r = $Len - 1; $r >= 0; $r--) {
        if (preg_match('/\d/', $Line[$r])) {
            $Right = $Line[$r];

            break;
        }
    }

    $LeftNRight = (int)"{$Left}{$Right}";

    $Total1 += $LeftNRight;
}

fclose($Thing);

printf("Total 1: %u\n", $Total1);

$Thing = fopen($File, 'rb');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));
    $RevLine = strrev($Line);

    $Left = 0;
    $Right = 0;

    $Len = strlen($Line);

    if ($Len === 0) continue;

    $PartsLeft = [];
    $PartsRight = [];

    if (!preg_match_all('/(\d|one|two|three|four|five|six|seven|eight|nine)/', $Line, $PartsLeft)) {
        printf("Error: %s\n", $Line);

        die();
    }
    if (!preg_match_all('/(\d|eno|owt|eerht|ruof|evif|xis|neves|thgie|enin)/', $RevLine, $PartsRight)) {
      printf("Error: %s\n", $Line);

      die();
    }

    $Left = Number2Number($PartsLeft[1][0]);
    $Right = Number2Number($PartsRight[1][0]);

    $LeftNRight = (int)"{$Left}{$Right}";

    $Total2 += $LeftNRight;
}

fclose($Thing);

printf("Total 2: %u\n", $Total2);
