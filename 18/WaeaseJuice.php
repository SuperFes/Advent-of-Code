#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

ini_set('memory_limit', '8G');

function floodFill(array &$map, $x, $y, $newVal, $oldVal = null): void
{
    $mapMinY = array_key_first($map);
    $mapMaxY = array_key_last($map);
    $mapMinX = array_key_first($map[0]);
    $mapMaxX = array_key_last($map[0]);

    if ($x >= $mapMinX && $x < $mapMaxX && $y >= $mapMinY && $y < $mapMaxY) {
        if ($oldVal === null) {
            $oldVal = $map[$y][$x];
        }

        if ($map[$y][$x] === $oldVal) {
            $map[$y][$x] = $newVal;

            floodFill($map, $x + 1, $y, $newVal, $oldVal);
            floodFill($map, $x - 1, $y, $newVal, $oldVal);
            floodFill($map, $x, $y + 1, $newVal, $oldVal);
            floodFill($map, $x, $y - 1, $newVal, $oldVal);
        }
    }
}

use Ayesh\PHP_Timer\Timer;

$Cubes = 0;

$ExtraCubes = gmp_init(0);

$Point  = [0, 0];
$Points = [0, 0, 0, 0];

$Width  = 0;
$Height = 0;

$Rules   = [];
$Colours = [];

$Map   = [];
$Polys = [];

Timer::start('Application runtime');

Timer::start('Load file');

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    $Rules[] = $Line;
}

fclose($Thing);

Timer::stop('Load file');

Timer::start('Parse pool');

foreach ($Rules as $Rule) {
    [$Dir, $Length, $Colour] = explode(' ', $Rule);

    $CDir    = $Colour[strlen($Colour) - 2];
    $CLength = base_convert("0x" . substr($Colour, 2, 5), 16, 10);

    $Colours[$Colour] = [$CDir, $CLength];

    switch ($Dir) {
        case 'R':
            $Point[0] += (int)$Length;
            break;
        case 'L':
            $Point[0] -= (int)$Length;
            break;
        case 'D':
            $Point[1] += (int)$Length;
            break;
        case 'U':
            $Point[1] -= (int)$Length;
            break;
    }

    if ($Point[0] < $Points[0]) {
        $Points[0] = $Point[0];
    }
    else if ($Point[0] > $Points[2]) {
        $Points[2] = $Point[0];
    }

    if ($Point[1] < $Points[1]) {
        $Points[1] = $Point[1];
    }
    else if ($Point[1] > $Points[3]) {
        $Points[3] = $Point[1];
    }
}

$Height = abs($Points[1] - $Points[3]);
$Width  = abs($Points[0] - $Points[2]);

for ($i = $Points[1]; $i <= $Points[3]; $i++) {
    if (!isset($Map[$i])) {
        $Map[$i] = [];
    }

    for ($j = $Points[0]; $j <= $Points[2]; $j++) {
        $Map[$i][$j] = '#000000';
    }
}

$AtX = 0;
$AtY = 0;
$ToX = 0;
$ToY = 0;

foreach ($Rules as $Rule) {
    [$Dir, $Length, $Colour] = explode(' ', $Rule);

    if ($Dir === 'R') {
        $ToX += (int)$Length;
    }
    else if ($Dir === 'L') {
        $ToX -= (int)$Length;
    }
    else if ($Dir === 'D') {
        $ToY += (int)$Length;
    }
    else if ($Dir === 'U') {
        $ToY -= (int)$Length;
    }

    $XDist = abs($AtX - $ToX);
    $YDist = abs($AtY - $ToY);

    $Map[$AtY][$AtX] = $Colour;

    for ($i = 0; $i < $YDist; $i++) {
        if ($Dir === 'U') {
            $AtY--;
        }
        else {
            $AtY++;
        }

        $Map[$AtY][$AtX] = $Colour;
    }

    for ($i = 0; $i < $XDist; $i++) {
        if ($Dir === 'L') {
            $AtX--;
        }
        else {
            $AtX++;
        }

        $Map[$AtY][$AtX] = $Colour;
    }
}

for ($x = $Points[0], $y = ceil($Height / 2) + $Points[1]; ; $x++) {
    if ($Map[$y][$x] !== '#000000') {
        break;
    }
}

floodFill($Map, $x + 1, $y, '#666666');

foreach ($Map as $Row) {
    foreach ($Row as $Col) {
        if ($Col !== '#000000') {
            $Cubes++;
        }
    }
}

Timer::stop('Parse pool');

Timer::start('Parse big pool');

$Polys[] = [0, 0];

$AtX = 0;
$AtY = 0;
$ToX = 0;
$ToY = 0;

foreach ($Colours as [$Dir, $Length]) {
    if ($Dir === '0') {
        $ToX += (int)$Length;
    }
    else if ($Dir === '2') {
        $ToX -= (int)$Length;
    }
    else if ($Dir === '1') {
        $ToY += (int)$Length;
    }
    else if ($Dir === '3') {
        $ToY -= (int)$Length;
    }

    $XDist = abs($AtX - $ToX);
    $YDist = abs($AtY - $ToY);

    if ($Dir === 'U') {
        $AtY -= $YDist;
    }
    else {
        $AtY += $YDist;
    }

    if ($Dir === 'L') {
        $AtX -= $XDist;
    }
    else {
        $AtX += $XDist;
    }

    $Polys[] = [$ToX, $ToY];
}

$NumPolys = count($Polys);

$Area      = 0;
$Perimeter = 0;

for ($i = 0; $i < $NumPolys; $i++) {
    $j = ($i + 1) % $NumPolys;

//    $Area += ($Polys[$i][0] * $Polys[$j][1]) - ($Polys[$i][1] * $Polys[$j][0]);

    $AreaHeight = ($Polys[$i][1] + $Polys[$j][1]) / 2;
    $AreaWidth  = $Polys[$i][0] - $Polys[$j][0];

    $Area += $AreaHeight * $AreaWidth;

    $Perimeter += sqrt((($Polys[$j][0] - $Polys[$i][0]) ** 2) + (($Polys[$j][1] - $Polys[$i][1]) ** 2));
}

$ExtraCubes = $Area + ceil($Perimeter / 2) + 1;

Timer::stop('Parse big pool');

print "Cubes: {$Cubes}\n";
print "Extra Cubes: {$ExtraCubes}\n\n";

Timer::stop('Application runtime');

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
