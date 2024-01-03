#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

require 'SortisFactory.php';

use Ayesh\PHP_Timer\Timer;

Timer::start('Application runtime');

Timer::start('Load file');

$PartsPart = false;

$Accepted = 0;

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        $PartsPart = true;

        continue;
    }

    if (!$PartsPart) {
        SortFactory::AddRule($Line);
    }
    else {
        SortFactory::AddPart($Line);
    }
}

fclose($Thing);

Timer::stop('Load file');

Timer::start('Test parts');

$Accepted = SortFactory::TestParts();

Timer::stop('Test parts');

Timer::start('Find max combinations');

$MaxAccepted = SortFactory::FindMaxParts();

Timer::stop('Find max combinations');

print "Accepted Parts: {$Accepted}\n";
print "Maximum Parts: {$MaxAccepted}\n\n";

Timer::stop('Application runtime');

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
