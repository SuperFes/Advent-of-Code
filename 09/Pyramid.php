#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Ayesh\PHP_Timer\Timer;

Timer::start('App run time');

$Histories      = 0;
$EarlyHistories = 0;

Timer::start('Load file');

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    $History = [explode(' ', $Line)];

    $Row = 0;

    $RowTotal = count(array_unique($History[$Row]));

    while ($RowTotal > 1) {
        $Row++;

        if (!isset($History[$Row])) {
            $History[$Row] = [];
        }

        $Length = count($History[$Row - 1]);

        for ($i = 1; $i < $Length; $i++) {
            $Diff = $History[$Row - 1][$i] - $History[$Row - 1][$i - 1];

            $History[$Row][] = $Diff;

        }

        $RowTotal = count(array_unique($History[$Row]));
    }

    for ($Row = count($History) - 1; $Row > 0; $Row--) {
        $Last    = array_key_last($History[$Row]);
        $UpLast  = array_key_last($History[$Row - 1]);
        $First   = array_key_first($History[$Row]);
        $UpFirst = array_key_first($History[$Row - 1]);

        $History[$Row - 1][] = $History[$Row][$Last] + $History[$Row - 1][$UpLast];

        array_unshift($History[$Row - 1], $History[$Row - 1][$UpFirst] - $History[$Row][$First]);
    }

    $El = array_key_last($History[0]);

    $Histories += $History[0][$El];

    $El = array_key_first($History[0]);

    $EarlyHistories += $History[0][$El];
}

fclose($Thing);

Timer::stop('Load file');

print "Histories: {$Histories}\n";
print "Early histories: {$EarlyHistories}\n\n";

Timer::stop('App run time');

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
