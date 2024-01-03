#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

ini_set('memory_limit', '32G');

require 'Constantinople.php';

use Ayesh\PHP_Timer\Timer;

$Hottest      = 0;
$HottestClimb = 0;

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

    Constantinople::AddRow($Line);

    $Height++;
    $Width = strlen($Line);
}

fclose($Thing);

Timer::stop('Load file');

Timer::start('Why\'d they change it?');

$Hottest = Constantinople::SteppedHighestCost();

Timer::stop('Why\'d they change it?');

Timer::start('People just liked it better that way');

$HottestClimb = Constantinople::FindHighestRecursePath();

Timer::stop('People just liked it better that way');

print "Hottest path: {$Hottest}\n";
print "Longest hike: {$HottestClimb}\n";

Timer::stop('Application runtime');

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
