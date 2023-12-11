#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Ayesh\PHP_Timer\Timer;

function MrManhattan(int $x, int $y, int $tx, int $ty): int
{
    return abs($x - $tx) + abs($y - $ty);
}

function Distance(int $x, int $y, int $tx, int $ty): int
{
    return sqrt((($tx - $x) ** 2) + (($ty - $y) ** 2));
}

function StepDistance(int $x, int $y, int $tx, int $ty): int
{
    $Steps = 0;

    while ($x !== $tx || $y !== $ty) {
        if ($x > $tx) {
            $x--;

            $Steps++;
        }
        else if ($x < $tx) {
            $x++;

            $Steps++;
        }

        if ($y > $ty) {
            $y--;

            $Steps++;
        }
        else if ($y < $ty) {
            $y++;

            $Steps++;
        }
    }

    return $Steps;
}

Timer::start('App run time');

$Shorts = 0;
$Longs  = 0;

$CleanMap = [];

$StarMap = [];

$EmptyCols = [];
$EmptyRows = [];

$Galaxies = [];

Timer::start('Load data');

$Thing = fopen('Data/Data.txt', 'rb+');

$Row = 0;

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    $Width = strlen($Line);

    if (empty($EmptyCols)) {
        for ($i = 0; $i < $Width; $i++) {
            $EmptyCols[] = true;
        }
    }

    $EmptyRows[$Row] = true;

    for ($i = 0; $i < $Width; $i++) {
        if ($Line[$i] !== '.') {
            $EmptyCols[$i]   = false;
            $EmptyRows[$Row] = false;
        }
    }

    $CleanMap[] = $Line;
    $StarMap[]  = $Line;

    $Row++;
}

fclose($Thing);

Timer::stop('Load data');

Timer::start('Calculate distances');

$Rows    = count($StarMap);
$OffsetY = 0;

for ($Row = 0; $Row < $Rows; $Row++) {
    $RowLen = strlen($StarMap[$Row]);

    if ($EmptyRows[$Row]) {
        $OffsetY += 1;
    }

    $OffsetX = 0;

    for ($Col = 0; $Col < $RowLen; $Col++) {
        if ($EmptyCols[$Col]) {
            $OffsetX += 1;
        }

        if ($StarMap[$Row][$Col] === '#') {
            $Galaxies[count($Galaxies) + 1] = [$Col + $OffsetX, $Row + $OffsetY];
        }
    }
}

$GalaxyCount = count($Galaxies);
$GalaxyQuest = [];

for ($Outer = 1; $Outer <= $GalaxyCount; $Outer++) {
    for ($Inner = 1; $Inner <= $GalaxyCount; $Inner++) {
        if ($Outer === $Inner) {
            continue;
        }

        $Low  = min($Outer, $Inner);
        $High = max($Outer, $Inner);

        $QuestId = "{$Low}-{$High}";

        if (isset($GalaxyQuest[$QuestId])) {
            continue;
        }

        $Distance = MrManhattan($Galaxies[$Outer][0], $Galaxies[$Outer][1], $Galaxies[$Inner][0], $Galaxies[$Inner][1]);

        $GalaxyQuest[$QuestId] = $Distance;

        $Shorts += $Distance;
    }
}

Timer::stop('Calculate distances');

Timer::start('Calculate bigger distances');

$GalaxyQuest2 = [];

$Galaxies = [];

$Rows    = count($StarMap);
$OffsetY = 0;

for ($Row = 0; $Row < $Rows; $Row++) {
    $RowLen = strlen($StarMap[$Row]);

    if ($EmptyRows[$Row]) {
        $OffsetY += 999_999;
    }

    $OffsetX = 0;

    for ($Col = 0; $Col < $RowLen; $Col++) {
        if ($EmptyCols[$Col]) {
            $OffsetX += 999_999;
        }

        if ($StarMap[$Row][$Col] !== '.') {
            $Galaxies[count($Galaxies) + 1] = [$Col + $OffsetX, $Row + $OffsetY];
        }
    }
}

for ($Outer = 1; $Outer <= $GalaxyCount; $Outer++) {
    for ($Inner = 1; $Inner <= $GalaxyCount; $Inner++) {
        if ($Outer === $Inner) {
            continue;
        }

        $Low  = min($Outer, $Inner);
        $High = max($Outer, $Inner);

        $QuestId = "{$Low}-{$High}";

        if (isset($GalaxyQuest2[$QuestId])) {
            continue;
        }

        $Distance = MrManhattan($Galaxies[$Outer][0], $Galaxies[$Outer][1], $Galaxies[$Inner][0], $Galaxies[$Inner][1]);

        $GalaxyQuest2[$QuestId] = $Distance;

        $Longs += $Distance;
    }
}

Timer::stop('Calculate bigger distances');

Timer::stop('App run time');

print "Sum of the shortest paths: $Shorts\n";
print "Sum of the longest paths: $Longs\n\n";

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
