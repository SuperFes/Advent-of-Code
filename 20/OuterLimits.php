#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

require 'Modules.php';

use Ayesh\PHP_Timer\Timer;

Timer::start('Application runtime');

Timer::start('Load file');

$Button = new Button('Start');

$Map     = [];
$Modules = [];

$SwitchStatus = [];

$High = 0;
$Low  = 0;

$Thing = fopen('Data/Data.txt', 'rb+');

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    [$From, $To] = explode(' -> ', $Line);

    $To = explode(', ', $To);

    if ($From[0] === '%') {
        $From = substr($From, 1);

        $Map[$From] = $To;

        $Modules[$From] = new FlipFlop($From);
    }
    else if ($From[0] === '&') {
        $From = substr($From, 1);

        $Map[$From] = $To;

        $Modules[$From] = new Conjunction($From);
    }
    else {
        // Broadcaster
        $Map[$From] = $To;

        $Modules[$From] = new Broadcast($From);
    }
}

fclose($Thing);

Timer::stop('Load file');

Timer::start('Relationship goals');

foreach ($Map as $From => $Tos) {
    foreach ($Tos as $To) {
        if (!isset($Modules[$To])) {
            $Modules[$To] = new Output($To);
        }

        $Modules[$From]->AttachTo($Modules[$To]);
        $Modules[$To]->AttachFrom($Modules[$From], false);
    }
}

$Button->AttachTo($Modules['broadcaster']);
$Modules['broadcaster']->AttachFrom($Button, false);

Timer::stop('Relationship goals');

Timer::start('Test pulse');

for ($i = 0; $i < 1000; $i++) {
    $Button->Press();
}

$High = Module::GetHigh();
$Low  = Module::GetLow();

$HighLow = $High * $Low;

Timer::stop('Test pulse');

Timer::start('Find low low low');

$NumStepsToNewLow = $i;

while (!$Modules['rx']->IsLowLowLow()) {
    $AllSet = true;

    if ($Modules['zh']->GetStatus() !== 0) {
        $NumStepsToNewLow = $Modules['zh']->GetStatus();

        break;
    }

    $NumStepsToNewLow++;

    $Button->Press();
}

Timer::stop('Find low low low');

print "Pulses: {$HighLow} ($Low/$High)\n";
print "Maximum Parts: {$NumStepsToNewLow}\n\n";

Timer::stop('Application runtime');

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
