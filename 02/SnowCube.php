#!/usr/bin/php -q
<?php
function Possible(array &$Max, array $Colors, array $Counts): bool {
    $Counter = count($Colors);

    for ($c = 0; $c < $Counter; $c++) {
        if (isset($Max[$Colors[$c]]) && (int)$Counts[$c] > $Max[$Colors[$c]]) {
            return false;
        }
    }

    return true;
}

function MinPossible(array &$Min, array $Colors, array $Counts): void {
    $Counter = count($Colors);

    for ($c = 0; $c < $Counter; $c++) {
        if (isset($Min[$Colors[$c]]) && (int)$Counts[$c] > $Min[$Colors[$c]]) {
            $Min[$Colors[$c]] = (int)$Counts[$c];
        }
    }
}

$File = 'Data/Data.txt';

$Total1 = 0;
$Total2 = 0;

$Thing = fopen($File, 'rb');

$MaxCubes = ['red' => 12, 'green' => 13, 'blue' => 14];

while (!feof($Thing)) {
    $MinCubes = ['red' => 0, 'green' => 0, 'blue' => 0];

    $Line = trim(fgets($Thing));

    $Matches = [];

    if (!strlen($Line)) {
        continue;
    }

    [$Left, $Right] = explode(': ', $Line, 2);

    $Game = (int)str_replace('Game ', '', $Left);

    $RightPulls = explode('; ', $Right);

    $Pulls = count($RightPulls);

    $p = 0;

    $Possible = true;

    for ($p; $p < $Pulls; $p++) {
        $Matches = [];

        if (preg_match_all('/(\d+) (red|green|blue)/', $RightPulls[$p], $Matches)) {
            [, $Count, $Color] = $Matches;

            MinPossible($MinCubes, $Color, $Count);

            if ($Possible && !Possible($MaxCubes, $Color, $Count)) {
                $Possible = false;
            }
        }
    }

    if ($Possible) {
        $Total1 += $Game;
    }

    $Total2 += $MinCubes['red'] * $MinCubes['green'] * $MinCubes['blue'];
}

fclose($Thing);

print "Total 1: {$Total1}\n";
print "Total 2: {$Total2}\n";
