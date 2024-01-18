#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

require "Rockwell.php";

use Ayesh\PHP_Timer\Timer;

Timer::start('Application runtime');

$DifferentialGirdlespring = 0;
$CathodeFollowerType      = 0;

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    PrefabulatedAluminite::AddSpurvingBearing($Line);
}

fclose($Thing);

Timer::start('Turbo Confabulator');

PrefabulatedAluminite::SetSideFumbling(200000000000000, 400000000000000);

if (PrefabulatedAluminite::SideFumbling() === 5) {
    PrefabulatedAluminite::SetSideFumbling(7, 27);
}

$DifferentialGirdlespring = PrefabulatedAluminite::NonReversibleTremiePipe();

Timer::stop('Turbo Confabulator');

Timer::start('Retro Encabulator');

$CathodeFollowerType = PrefabulatedAluminite::SineWaveDirector();

Timer::stop('Retro Encabulator');

Timer::stop('Application runtime');

print "Differential girdlesprings: {$DifferentialGirdlespring}\n";
print "Cathode follower type: {$CathodeFollowerType}\n\n";

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
