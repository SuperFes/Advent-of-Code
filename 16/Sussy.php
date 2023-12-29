#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

require 'FrickinLasers.php';

use Ayesh\PHP_Timer\Timer;

$Powered    = 0;
$MaxPowered = 0;

$Height = 0;
$Width  = 0;

Timer::start('Application runtime');

Timer::start('Load file');

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    Map::AddRowToGrid($Line);

    $Height++;

    if (!$Width) {
        $Width = strlen($Line);
    }
}

fclose($Thing);

Map::PrintMap();

Timer::stop('Load file');

Timer::start('Energized');

Map::AddLaser(0, 0, Laser::Right);

while (!Map::IsSolved()) {
    Map::Solve();
}

Map::PrintEnergizedMap();

$Powered = Map::GetEnergized();

Timer::stop('Energized');

Timer::start('Frickin\' Lasers');

for ($Y = 0; $Y < $Height; $Y++) {
    for ($X = 0; $X < $Width; $X++) {
        if ($X === 0 ||
            $Y === 0 ||
            $X === $Width - 1 ||
            $Y === $Height - 1
        ) {
            Map::Reset();

            $Lasers = [];

            if ($X === 0) {
                $Lasers[] = Laser::Right;
            }
            else if ($X === $Width - 1) {
                $Lasers[] = Laser::Left;
            }

            if ($Y === 0) {
                $Lasers[] = Laser::Down;
            }
            else if ($Y === $Height - 1) {
                $Lasers[] = Laser::Up;
            }

            foreach ($Lasers as $Dir) {
                Map::AddLaser($X, $Y, $Dir);

                while (!Map::IsSolved()) {
                    Map::Solve();
                }

                $Energized = Map::GetEnergized();

                if ($Energized > $MaxPowered) {
                    $MaxPowered = $Energized;
                }
            }
        }
    }
}

Timer::stop('Frickin\' Lasers');

print "Powered: {$Powered}\n";
print "Max powered!: {$MaxPowered}\n";

Timer::stop('Application runtime');

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
