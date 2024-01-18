#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Ayesh\PHP_Timer\Timer;

$Map = [];

$Weight = 0;
$Flumgp = 0;

Timer::start('Application runtime');

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    $Map[] = $Line;
}

fclose($Thing);

$Spins = [];

$CycleFound = 0;

$Height = count($Map);

for ($Spin = 0; $Spin < 1000000000; $Spin++) {
    for ($i = 0; $i < $Height; $i++) {
        $Width = strlen($Map[$i]);

        for ($j = 0; $j < $Width; $j++) {
            if ($Map[$i][$j] === 'O') {
                $k = $i;

                for (; ;) {
                    if ($k === 0) {
                        break;
                    }

                    if ($Map[$k - 1][$j] === '.') {
                        $Map[$k - 1][$j] = 'O';
                        $Map[$k--][$j]   = '.';
                    }
                    else {
                        break;
                    }
                }
            }
        }
    }

    for ($i = 0; $i < $Height; $i++) {
        $Width = strlen($Map[$i]);

        for ($j = 0; $j < $Width; $j++) {
            if ($Map[$i][$j] === 'O') {
                $k = $j;

                for (; ;) {
                    if ($k === 0) {
                        break;
                    }

                    if ($Map[$i][$k - 1] === '.') {
                        $Map[$i][$k - 1] = 'O';
                        $Map[$i][$k--]   = '.';
                    }
                    else {
                        break;
                    }
                }
            }
        }
    }

    for ($i = $Height - 1; $i >= 0; $i--) {
        $Width = strlen($Map[$i]);

        for ($j = 0; $j < $Width; $j++) {
            if ($Map[$i][$j] === 'O') {
                $k = $i;

                for (; ;) {
                    if ($k + 1 === $Height) {
                        break;
                    }

                    if ($Map[$k + 1][$j] === '.') {
                        $Map[$k + 1][$j] = 'O';
                        $Map[$k++][$j]   = '.';
                    }
                    else {
                        break;
                    }
                }
            }
        }
    }

    for ($i = 0; $i < $Height; $i++) {
        $Width = strlen($Map[$i]);

        for ($j = $Width - 1; $j >= 0; $j--) {
            if ($Map[$i][$j] === 'O') {
                $k = $j;

                for (; ;) {
                    if ($k + 1 === $Width) {
                        break;
                    }

                    if ($Map[$i][$k + 1] === '.') {
                        $Map[$i][$k + 1] = 'O';
                        $Map[$i][$k++]   = '.';
                    }
                    else {
                        break;
                    }
                }
            }
        }
    }

    $SpinWeight = 0;

    $Stuffers = $Height;

    foreach ($Map as $Line) {
        $Width = strlen($Line);

        for ($j = 0; $j < $Width; $j++) {
            if ($Line[$j] === 'O') {
                $SpinWeight += $Stuffers;
            }
        }

        $Stuffers--;
    }

    if ($CycleFound === 0) {
        $Spins[] = $SpinWeight;

        if ($Spin && $Spin % 300 === 0) {
            $StartCycle = 0;

            $FirstAgain = $Spins[$Spin - 1];

            for ($i = $Spin - 1, $h = 0; $i > 2; $i--, $h++) {
                if ($Spins[$i] === $FirstAgain && $Spins[$i - $h] === $FirstAgain) {
                    print "Search for cycle @ $i\n";

                    for ($j = $Spin - 1, $k = $i; $j > $i; $j--, $k--) {
                        if ($Spins[$k] === $Spins[$j]) {
                            $Top          = array_slice($Spins, $k, $h);
                            $Intermediate = array_slice($Spins, $j, $h);

                            $Diff = 0;

                            for ($m = 0; $m < $h; $m++) {
                                $TipTop          = $Top[$m] ?? null;
                                $TipIntermediate = $Intermediate[$m] ?? null;

                                if ($TipTop === null || $TipIntermediate === null) {
                                    $Diff++;

                                    break;
                                }

                                if ($Top[$m] !== $Intermediate[$m]) {
                                    $Diff++;
                                }
                            }

                            if ($Diff === 0) {
                                echo "Pattern found @ " . ($h) . "\n";

                                $CycleFound = $h;

                                break;
                            }
                        }

                        if ($CycleFound) {
                            break;
                        }
                    }
                }

                if ($CycleFound) {
                    break;
                }
            }

            if ($CycleFound !== 0) {
                print "Cycle repeats at {$CycleFound}.\n";

                while ($Spin + $CycleFound < 1000000000) {
                    $Spin += $CycleFound;
                }
            }
        }
    }

    if ($Spin && $Spin % 100000 === 0) {
        print "$Spin of 1000000000 (" . number_format($Spin / 1000000000, 3) . "%)\n";
    }
}

$Stuffers = $Height;

foreach ($Map as $Line) {
    print $Line . "\n";

    $Width = strlen($Line);

    for ($j = 0; $j < $Width; $j++) {
        if ($Line[$j] === 'O') {
            $Weight += $Stuffers;
        }
    }

    $Stuffers--;
}

print "\n";

Timer::stop('Application runtime');

print "Weight: {$Weight}\n";
print "Flumgp: {$Flumgp}\n\n";

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
