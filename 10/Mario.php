#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Ayesh\PHP_Timer\Timer;

require 'Turtle.php';

$Map  = [];
$Fill = [];
$Outs = [];
$Ins  = [];

Timer::start('App run time');

Timer::start('Load data');

$Thing = fopen('Data/Data.txt', 'rb+');

$StartX = 0;
$StartY = 0;

$Lines = 0;

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    if (str_contains($Line, 'S')) {
        $StartY = $Lines;

        $StartX = strpos($Line, 'S');
    }

    $Fill[$Lines]  = str_pad('', strlen($Line), ' ');
    $Map[$Lines++] = $Line;
}

fclose($Thing);

Timer::stop('Load data');

$CurX   = $StartX;
$CurY   = $StartY;
$CurDir = 0;

$Steps = 1;

$Moves = '-';

Timer::start('Walkabout');

$Fill[$CurY][$CurX] = '-';

if (Turtle::GetNextDir($Map, 1, $CurX, $CurY - 1)) {
    $CurY--;

    $CurDir = Turtle::GetNextDir($Map, 1, $CurX, $CurY);
}
else if (Turtle::GetNextDir($Map, 2, $CurX + 1, $CurY)) {
    $CurX++;

    $CurDir = Turtle::GetNextDir($Map, 2, $CurX, $CurY);
}
else if (Turtle::GetNextDir($Map, 3, $CurX, $CurY + 1)) {
    $CurY++;

    $CurDir = Turtle::GetNextDir($Map, 3, $CurX, $CurY);
}
else if (Turtle::GetNextDir($Map, 4, $CurX - 1, $CurY)) {
    $CurX--;

    $CurDir = Turtle::GetNextDir($Map, 4, $CurX, $CurY);
}

while ($Map[$CurY][$CurX] !== 'S') {
    $Moves .= $Map[$CurY][$CurX];

    $Fill[$CurY][$CurX] = $Map[$CurY][$CurX];

    if ($CurDir === UP) {
        $CurY--;
    }
    else if ($CurDir === RIGHT) {
        $CurX++;
    }
    else if ($CurDir === DOWN) {
        $CurY++;
    }
    else if ($CurDir === LEFT) {
        $CurX--;
    }
    else {
        die('Burn in hell!\n');
    }

    $CurDir = Turtle::GetNextDir($Map, $CurDir, $CurX, $CurY);

    $Steps++;
}

Timer::stop('Walkabout');

Timer::start('Turtle');

$CurX = 0;
$CurY = 0;

for (;;) {
    $CurX++;
    $CurY++;

    if ($Fill[$CurY][$CurX] !== ' ') {
        break;
    }
}

$Koopa = new Turtle($Fill, $CurX, $CurY);

$Insides = $Koopa->walkies();

Timer::stop('Turtle');

Timer::stop('App run time');

$HalfSteps = $Steps / 2;

print "Steps: $Steps\n";
print "Half steps: $HalfSteps\n";
print "Insides: $Insides\n\n";

//foreach ($Fill as $Line) {
//    $Line = str_replace(['7', 'F', 'J', 'L', '-', '|'], ['╗', '╔', '╝', '╚', '═', '║'], $Line);
//
//    print "$Line\n";
//}

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
