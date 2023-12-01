#!/usr/bin/php -q
<?php
function Number2Number(string $Numbers, bool $NumberBowl = false): array
{
    static $Forward  = ['one' => '1', 'two' => '2', 'three' => '3', 'four' => '4', 'five' => '5', 'six' => '6', 'seven' => '7', 'eight' => '8', 'nine' => '9'];
    static $Backward = ['enin' => '9', 'thgie' => '8', 'neves' => '7', 'xis' => '6', 'evif' => '5', 'ruof' => '4', 'eerht' => '3', 'owt' => '2', 'eno' => '1'];

    $Reverse = strrev($Numbers);

    $Left  = [];
    $Right = [];

    if ($NumberBowl) {
        $Numbers = strtr($Numbers, $Forward);
        $Reverse = strtr($Reverse, $Backward);
    }

    preg_match_all('/(\d)/', $Numbers, $Left);
    preg_match_all('/(\d)/', $Reverse, $Right);

    return [$Left[1], $Right[1]];
}

$File = 'Data/Data.txt';

$Total1 = 0;
$Total2 = 0;

$Thing = fopen($File, 'rb');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    $Left  = 0;
    $Right = 0;

    $Len = strlen($Line);

    if ($Len === 0) {
        continue;
    }

    [$Left, $Right] = Number2Number($Line);

    $LeftNRight = (int)"{$Left[0]}{$Right[0]}";

    $Total1 += $LeftNRight;

    [$Left, $Right] = Number2Number($Line, true);

    $LeftNRight = (int)"{$Left[0]}{$Right[0]}";

    $Total2 += $LeftNRight;
}

fclose($Thing);

printf("Total 1: %u\n", $Total1);
printf("Total 2: %u\n", $Total2);
