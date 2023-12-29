#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

ini_set('memory_limit', '32G');

require 'Convection.php';

use Ayesh\PHP_Timer\Timer;

$Hottest    = 0;
$Hotterest = 0;

$Width  = 0;
$Height = 0;

Timer::start('Application runtime');

Timer::start('Load file');

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    Convection::AddRow($Line);

    $Height++;
    $Width = strlen($Line);
}

fclose($Thing);

Timer::stop('Load file');

Timer::start('Crucibles');

$Hottest = Convection::GraphLowestCost(1, 3);

Timer::stop('Crucibles');

Timer::start('Big crucibles');

$Hotterest = Convection::GraphLowestCost(4, 10);

Timer::stop('Big crucibles');

print "Hottest path: {$Hottest}\n";
print "Hotterest path: {$Hotterest}\n\n";

Timer::stop('Application runtime');

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
