#!/usr/bin/env php
<?php
require 'Boat.php';

$Thing = fopen('Data/Data.txt', 'rb+');

$TimeLine     = trim(fgets($Thing));
$DistanceLine = trim(fgets($Thing));

fclose($Thing);

$Times     = [];
$Distances = [];

preg_match_all('/\d+/', $TimeLine, $Times);
preg_match_all('/\d+/', $DistanceLine, $Distances);

$Times     = $Times[0];
$Distances = $Distances[0];

$BigTime     = 0;
$BigDistance = 0;

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

$BigTime     = (int)implode('', $Times);
$BigDistance = (int)implode('', $Distances);

$Boat = new Boat($BigTime);

$BigRecords = $Boat->findWins($BigDistance);

print "Records beaten: {$Records}\n";
print "Big records beaten: {$BigRecords}\n";
