#!/usr/bin/env php
<?php
require 'Map.php';

use Almanac\Map;
use Almanac\Maps;

$Maps = new Maps();

$Thing = fopen('Data/Data.txt', 'rb+');

$CurMap = false;

$Seeds = [];
$SeedPairs = [];

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    if (str_starts_with($Line, 'seeds:')) {
        [, $rSeeds] = explode(': ', $Line, 2);

        $Seeds = explode(" ", $rSeeds);

        $SeedCount = count($Seeds);

        for ($s = 0; $s < $SeedCount - 1;) {
            $Seed  = (int)$Seeds[$s++];
            $Range = $Seed + (int)$Seeds[$s++];

            $SeedPairs[] = [$Seed, $Range];
        }
    }
    else if (str_contains($Line, 'map:')) {
        [$CurMap,] = explode(' ', $Line, 2);

        $Maps[$CurMap] = new Map();
    }
    else {
        [$Dest, $Source, $Length] = explode(' ', $Line);

        $Maps[$CurMap]->add($Dest, $Source, $Length);
    }
}

fclose($Thing);

function FindLowMap(int $Seed, Maps $Maps): int {
    $Soil = $Maps['seed-to-soil']->get($Seed);
    $Fertilizer = $Maps['soil-to-fertilizer']->get($Soil);
    $Water = $Maps['fertilizer-to-water']->get($Fertilizer);
    $Light = $Maps['water-to-light']->get($Water);
    $Tempurature = $Maps['light-to-temperature']->get($Light);
    $Humidity = $Maps['temperature-to-humidity']->get($Tempurature);
    return $Maps['humidity-to-location']->get($Humidity);
}

$Lowest = false;
$Lowester = false;

foreach ($Seeds as $Seed) {
    $Res = FindLowMap($Seed, $Maps);

    if ($Lowest === false || $Lowest > $Res) {
       $Lowest = $Res;
    }
}

$LowSeed  = 0;
$HighSeed = 0;

$LowLocation    = $Maps['humidity-to-location']->getLowest();
$LowHumidity    = $Maps['temperature-to-humidity']->getRanges([$LowLocation]);
$LowTempurature = $Maps['light-to-temperature']->getRanges($LowHumidity);
$LowLight       = $Maps['water-to-light']->getRanges($LowTempurature);
$LowWater       = $Maps['fertilizer-to-water']->getRanges($LowLight);
$LowFertilizer  = $Maps['soil-to-fertilizer']->getRanges($LowWater);
$LowSoil        = $Maps['seed-to-soil']->getRanges($LowFertilizer);

foreach ($SeedPairs as $SeedRange) {
    [$Seed, $Range] = $SeedRange;

    foreach ($LowSoil as $SoilRange) {
        [$BottomSoil, $TopSoil] = $SoilRange;

        $InRange = max($Seed, $BottomSoil) <= min($Range, $TopSoil);

        if (!$InRange) {
            continue;
        }

        $Res = FindLowMap($Seed, $Maps);

        if ($Lowester === false || $Lowester > $Res) {
            $Lowester = $Res;
            $LowSeed = $Seed;
        }

        $Step = ceil(($Range - $Seed) / 50);

        for ($e = $Seed; $e < $Range; $e += $Step) {
            $Res = FindLowMap($e, $Maps);

            if ($Lowester === false || $Lowester > $Res) {
                if ($Step > 2) {
                    $Step = ceil($Step / 2);
                }

                $Lowester = $Res;
                $LowSeed = $Seed;
                $HighSeed = $e;
            }
        }
    }
}

for ($e = $HighSeed; $e >= $LowSeed; $e -= 500) {
    $Res = FindLowMap($e, $Maps);

    if ($Lowester > $Res) {
        $Lowester = $Res;
    }

    if ($Res > $Lowester) {
        $LowSeed  = $e - 500;
        $HighSeed = $e + 500;

        break;
    }
}

for ($e = $LowSeed; $e < $HighSeed; $e++) {
    $Res = FindLowMap($e, $Maps);

    if ($Lowester === false || $Lowester > $Res) {
        $Lowester = $Res;
    }
}

print "Lowest: {$Lowest}\n";
print "Lowester: {$Lowester}\n";
