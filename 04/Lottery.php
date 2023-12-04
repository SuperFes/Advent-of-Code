#!/usr/bin/env php
<?php
$Total1 = 0;
$Total2 = 0;

$Copies = [];
$Row    = 0;

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    [$Card, $Numbers] = explode(': ', $Line, 2);
    [$Left, $Right] = explode(' | ', $Numbers, 2);

    $LeftNos = [];
    $RightNos = [];

    preg_match_all('/(\d+)/', $Left, $LeftNos);
    preg_match_all('/(\d+)/', $Right, $RightNos);

    sort($LeftNos[1]);
    sort($RightNos[1]);

    $Intersection = array_intersect($LeftNos[1],$RightNos[1]);

    $Sections = count($Intersection);

    if (!isset($Copies[$Row])) {
        $Copies[$Row] = 1;
    }

    for ($r = $Row + 1; $r < $Row + 1 + $Sections; $r++) {
        if (!isset($Copies[$r])) {
            $Copies[$r] = 1;
        }

        for ($c = 0; $c < $Copies[$Row]; $c++) {
            $Copies[$r]++;
        }
    }

    $Points = 1;

    for ($s = 1; $s < $Sections; $s++) {
        $Points *= 2;
    }

    if ($Sections > 0) {
        $Total1 += $Points;
    }

    $Total2 += $Copies[$Row];

    $Row++;
}

fclose($Thing);

print "Total 1: {$Total1}\n";
print "Total 2: {$Total2}\n";
