#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

require 'Blocks.php';

use Ayesh\PHP_Timer\Timer;
use Tetris\Bricks;

Timer::start('Application runtime');

$CanNotFall      = 0;
$MostDestruction = 0;

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    Bricks::AddBrick($Line);
}

fclose($Thing);

Timer::start('Huffing Glue');

$CanNotFall = Bricks::FindRemovableBricks();

Timer::stop('Huffing Glue');

Timer::start('Over Macho Grande?');

$MostDestruction = Bricks::FindLargestCascade();

Timer::stop('Over Macho Grande?');

Timer::stop('Application runtime');

print "Removable bricks: {$CanNotFall}\n";
print "Most destructive: {$MostDestruction}\n\n";

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
