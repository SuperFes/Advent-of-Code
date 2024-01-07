#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

require 'Spring.php';

use Ayesh\PHP_Timer\Timer;

ini_set('memory_limit', '32G');

$Springs = [];

$Wiggles  = 0;
$BWiggles = 0;

Timer::start('Application runtime');

Timer::start('Load file');

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    $Springs[] = new Spring($Line);
}

fclose($Thing);

Timer::stop('Load file');

Timer::start('Little wiggles');

foreach ($Springs as $Spring) {
    $Wiggles += $Spring->GetWiggles();
}

Timer::stop('Little wiggles');

Timer::start('Big wiggles');

while (!empty($Springs)) {
    $Spring = array_shift($Springs);

    $BWiggles += $Spring->GetBigWiggles();

    unset($Spring);
}

Timer::stop('Big wiggles');

print "Total wiggles: {$Wiggles}\n";
print "Total big wiggles: {$BWiggles}\n";

Timer::stop('Application runtime');

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
