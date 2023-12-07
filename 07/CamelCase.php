#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Ayesh\PHP_Timer\Timer;

Timer::start('App run time');

class Hands
{
    private static array $Chars = [
        'A' => 0xF,
        'K' => 0xE,
        'Q' => 0xD,
        'J' => 0xC,
        'T' => 0xB,
        '9' => 0xA,
        '8' => 0x9,
        '7' => 0x8,
        '6' => 0x6,
        '5' => 0x5,
        '4' => 0x4,
        '3' => 0x3,
        '2' => 0x2,
    ];

    private static array $WildChars = [
        'A' => 0xF,
        'K' => 0xE,
        'Q' => 0xD,
        'T' => 0xC,
        '9' => 0xB,
        '8' => 0xA,
        '7' => 0x9,
        '6' => 0x8,
        '5' => 0x6,
        '4' => 0x5,
        '3' => 0x4,
        '2' => 0x3,
        'J' => 0x2,
    ];

    private static array $HandValues = [
        '5'         => 0xF00000000,
        '4-1'       => 0xA00000000,
        '3-2'       => 0x800000000,
        '3-1-1'     => 0x400000000,
        '2-2-1'     => 0x200000000,
        '2-1-1-1'   => 0x100000000,
        '1-1-1-1-1' => 0x0,
    ];

    private static array $CalculatedCamels = [0 => [], 1 => []];

    private static function getHandValue(string $hand, bool $wild = false): int {
        if (isset(self::$CalculatedCamels[(int)$wild][$hand])) {
           return self::$CalculatedCamels[(int)$wild][$hand];
        }

        $cards = self::$Chars;
        $wilds = 0;

        if ($wild) {
            $cards = self::$WildChars;
        }

        $handCounts = [];

        for ($i = 0; $i < 5; $i++) {
            $car = $hand[$i];

            if ($wild && $car === 'J') {
                $wilds++;
            }
            else if (isset($handCounts[$car])) {
                $handCounts[$car]++;
            }
            else {
                $handCounts[$car] = 1;
            }
        }

        $counts = array_values($handCounts);

        rsort($counts);

        if (empty($counts)) {
            $counts[] = 5;
        }
        else if ($wild) {
            $counts[0] += $wilds;
        }

        $handCount = implode('-', $counts);

        if (!isset(self::$HandValues[$handCount])) {
            throw new \RuntimeException('Chance of this happening, is zero!');
        }

        $score = self::$HandValues[$handCount];

        $score += $cards[$hand[0]] << 24;
        $score += $cards[$hand[1]] << 16;
        $score += $cards[$hand[2]] << 12;
        $score += $cards[$hand[3]] << 8;
        $score += $cards[$hand[4]];

        self::$CalculatedCamels[(int)$wild][$hand] = $score;

        return $score;
    }

    public static function sortHands(string $left, string $right): int
    {
        $lValue = self::getHandValue($left);
        $rValue = self::getHandValue($right);

        if ($lValue > $rValue) {
            return 1;
        }

        if ($lValue < $rValue) {
            return -1;
        }

        return 0;
    }

    public static function wildSortHands(string $left, string $right): int
    {
        $lValue = self::getHandValue($left, true);
        $rValue = self::getHandValue($right, true);

        if ($lValue > $rValue) {
            return 1;
        }

        if ($lValue < $rValue) {
            return -1;
        }

        return 0;
    }
}

$Hands = [];

Timer::start('Load data');

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    [$Hand, $Value] = explode(' ', $Line, 2);

    $Hands[$Hand] = (int)$Value;
}

fclose($Thing);

Timer::stop('Load data');

Timer::start('Run round 1');

uksort($Hands, 'Hands::sortHands');

$Total1 = 0;

$Mul = 1;

foreach ($Hands as $Hand => $Value) {
    $Total1 += $Value * $Mul++;
}

Timer::stop('Run round 1');

Timer::start('Run round 2');

uksort($Hands, 'Hands::wildSortHands');

$Total2 = 0;

$Mul = 1;

foreach ($Hands as $Hand => $Value) {
    $Total2 += $Value * $Mul++;
}

Timer::stop('Run round 2');

Timer::stop('App run time');

print "Total 1: {$Total1}\n";
print "Total 2: {$Total2}\n\n";

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
