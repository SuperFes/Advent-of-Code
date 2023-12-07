#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Ayesh\PHP_Timer\Timer;

Timer::start('App run time');

require 'Boat.php';

Timer::start('Load file');

$Thing = fopen('Data/Data.txt', 'rb+');

$TimeLine     = trim(fgets($Thing));
$DistanceLine = trim(fgets($Thing));

fclose($Thing);

Timer::stop('Load file');

$Times     = [];
$Distances = [];

Timer::start('Parse file');

preg_match_all('/\d+/', $TimeLine, $Times);
preg_match_all('/\d+/', $DistanceLine, $Distances);

$Times     = $Times[0];
$Distances = $Distances[0];

Timer::stop('Parse file');

$BigTime     = 0;
$BigDistance = 0;

Timer::start('Small races');

$NumTimes = count($Times);

$Records    = 0;
$BigRecords = 0;

for ($Race = 0; $Race < $NumTimes; $Race++) {
    $Time     = (int)$Times[$Race];
    $Distance = (int)$Distances[$Race];

    $Boat = new Boat($Time);

    $Beaten = $Boat->findWins($Distance);

    if ($Records === 0 && $Beaten !== 0) {
        $Records = $Beaten;
    }
    else {
        $Records *= $Beaten;
    }
}

Timer::stop('Small races');

Timer::start('Big race');

$BigTime     = (int)implode('', $Times);
$BigDistance = (int)implode('', $Distances);

$Boat = new Boat($BigTime);

$BigRecords = $Boat->findWins($BigDistance, true);

Timer::stop('Big race');

print "Records beaten: {$Records}\n";
print "Big records beaten: {$BigRecords}\n\n";

Timer::stop('App run time');

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
