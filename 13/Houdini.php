#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Ayesh\PHP_Timer\Timer;

$Mirror = [];
$Flump  = 0;
$Flumgp = 0;

Timer::start('Application runtime');

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        $Length = count($Mirror);

        if ($Length) {
            $Width = strlen($Mirror[0]);

            $Floomp = 0;
            $Floomgp = 0;

            for ($x = 0; $x < $Width; $x++) {
                $Corrections = 0;
                $Found       = 0;

                for ($y = 0; $y < $Length; $y++) {
                    $String = $Mirror[$y];

                    $StrLeft  = strrev(substr($String, 0, $x));
                    $StrRight = substr($String, $x);
                    $StrWidth = min(strlen($StrLeft), strlen(($StrRight)));

                    if ($StrWidth === 0) {
                        break;
                    }

                    for ($i = 0; $i < $StrWidth; $i++) {
                        $CharL = $StrLeft[$i] ?? null;
                        $CharR = $StrRight[$i] ?? null;

                        if (empty($CharL) || empty($CharR)) {
                            break;
                        }

                        $Found++;

                        if ($CharL !== $CharR) {
                            $Corrections++;
                        }
                    }
                }

                if ($Found > 0 && $Corrections === 1) {
                    $Floomgp += $x;
                }

                if ($Found > 0 && $Corrections === 0) {
                    $Floomp += $x;
                }
            }

            for ($y = 0; $y < $Length; $y++) {
                $Corrections = 0;
                $Found       = 0;

                for ($x = 0; $x < $Width; $x++) {
                    $String = '';

                    for ($i = 0; $i < $Length; $i++) {
                        $String .= $Mirror[$i][$x];
                    }

                    $StrLeft  = strrev(substr($String, 0, $y));
                    $StrRight = substr($String, $y);
                    $StrWidth = min(strlen($StrLeft), strlen(($StrRight)));

                    for ($i = 0; $i < $StrWidth; $i++) {
                        $CharL = $StrLeft[$i] ?? null;
                        $CharR = $StrRight[$i] ?? null;


                        if (empty($CharL) || empty($CharR)) {
                            break;
                        }

                        $Found++;

                        if ($CharL !== $CharR) {
                            $Corrections++;
                        }
                    }
                }

                if ($Found > 0 && $Corrections === 1) {
                    $Floomgp = $y * 100;
                }

                if ($Found > 0 && $Corrections === 0) {
                    $Floomp = $y * 100;
                }
            }

            $Mirror = [];

            $Flumgp += $Floomgp;
            $Flump  += $Floomp;

            continue;
        }

        continue;
    }

    $Mirror[] = $Line;
}

fclose($Thing);

Timer::stop('Application runtime');

print "Flump: {$Flump}\n";
print "Flumgp: {$Flumgp}\n\n";

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
