#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

require 'Garden.php';

use Ayesh\PHP_Timer\Timer;

Timer::start('Application runtime');

$ShortWalk = 0;
$LongWalk  = 0;

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    Garden::AddToMap($Line);
}

fclose($Thing);

Timer::start('XOR');

try {
    $ShortWalk = Garden::SeekAndDestroy(64);
}
catch (Exception $e) {
    print "Exception =( " . $e->getMessage() . ")=\n";
}

Timer::stop('XOR');

Timer::start('XORXOR');

$ElfSteps = 26_501_365;

Garden::Reset();

try {
    $LongWalk = Garden::SeekAndDestroy($ElfSteps);
}
catch (Exception $e) {
    print "Exception =( " . $e->getMessage() . ")=\n";
}

Timer::stop('XORXOR');

Timer::stop('Application runtime');

print "Short Steps: {$ShortWalk}\n";
print "Long Walk: {$LongWalk}\n\n";

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
