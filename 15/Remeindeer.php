#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Ayesh\PHP_Timer\Timer;

function ReindeerHash(string $Input): int
{
    $Hash        = 0;
    $Incrementor = 0;

    $Len = strlen($Input);

    for ($i = 0; $i < $Len; $i++) {
        $Ord = ord($Input[$i]);

        $Hash += $Ord;
        $Hash *= 17;
        $Hash %= 256;
    }

    return $Hash;
}

$Deers = 0;
$Focus = 0;
$Boxen = [];

Timer::start('Application runtime');

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    $Hashes = explode(',', $Line);

    foreach ($Hashes as $Hash) {
        $Deers += ReindeerHash($Hash);

        $Label = substr($Hash, 0, 2);
        $Box   = ReindeerHash($Label);
        $Op    = $Hash[2];
        $Lens  = $Hash[3] ?? null;

        if (!isset($Boxen[$Box])) {
            $Boxen[$Box] = [];
        }

        if ($Op === '=') {
            $Boxen[$Box][$Label] = $Lens;
        }
        else if ($Op === '-') {
            if (isset($Boxen[$Box][$Label])) {
                $TempBox = [];

                foreach ($Boxen[$Box] as $Key => $Value) {
                    if ($Key !== $Label) {
                        $TempBox[$Key] = $Value;
                    }
                }

                $Boxen[$Box] = $TempBox;
            }
        }

//        print "After \"{$Hash}:\"\n";
        foreach ($Boxen as $Num => $Box) {
            $Ghast = 0;

            if (!empty($Box)) {
//                print "Box {$Num}: ";

                $Slot = 1;
                foreach ($Box as $Key => $Value) {
                    $Power = ((int)$Num + 1) * $Slot++ * (int)$Value;
                    $Ghast += $Power;

//                    print "[$Key $Value ($Power)] ";
                }
//                print " ($Ghast)\n";
            }
        }
//        print "\n";
    }

    foreach ($Boxen as $Num => $BoxL) {
        $Power = 0;

        if (!empty($BoxL)) {
            $Ghast = 0;

            if (!empty($BoxL)) {
                $Slot = 1;
                foreach ($BoxL as $Key => $Value) {
                    $Power = ((int)$Num + 1) * $Slot++ * (int)$Value;
                    $Ghast += $Power;
                }
            }

            $Focus += $Ghast;
        }
    }
}

fclose($Thing);

print "Deers: {$Deers}\n";
print "Focus: {$Focus}\n";

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
