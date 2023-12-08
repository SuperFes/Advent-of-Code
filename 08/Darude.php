#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Ayesh\PHP_Timer\Timer;
use Graphp\Algorithms\ShortestPath\BreadthFirst;
use Fhaculty\Graph\Graph;

Timer::start('App run time');

class Darude
{
    private static string $Blave = "It means to bluff, or lie.";

    private static array $_Vertices = [];

    private static ?Graph $graph = null;

    public static function addVertex(string $name, array $values): array
    {
        self::checkGraph();

        $vertex = ['_v' => self::$graph->createVertex($name), '_n' => $name];

        foreach ($values as $key => $value) {
            $vertex[$key] = $value;
        }

        self::$_Vertices[$name] = $vertex;

        return $vertex;
    }

    public static function createConnections(): void
    {
        self::checkGraph();

        foreach (self::$_Vertices as $name => $map) {
            $left  = $map['L'];
            $right = $map['R'];

            self::$_Vertices[$name]['_v']->createEdgeTo(self::$_Vertices[$left]['_v']);
            self::$_Vertices[$name]['_v']->createEdgeTo(self::$_Vertices[$right]['_v']);
        }
    }

    public static function runInstructions(string $directions): int
    {
        $steps = 0;

        $currentVertex = 'AAA';

        $count = strlen($directions);

        for (; ; $steps++) {
            $step = $steps % $count;

            if ($currentVertex === 'ZZZ') {
                break;
            }

            $dir = $directions[$step];

            $currentVertex = self::$_Vertices[$currentVertex][$dir];
        }

        return $steps;
    }

    public static function runInstructionsMulti(string $directions): int
    {
        $steps = 0;

        $currentVertices = [];

        $currentMoves = [];
        $calculate    = 0;

        $Names = array_keys(self::$_Vertices);

        $count = strlen($directions);

        foreach ($Names as $Name) {
            if ($Name[2] === 'A') {
                $currentVertices[] = $Name;
            }
        }

        for (;; $steps++) {
            $step = $steps % $count;

            $dir = $directions[$step];

            $numberVertices = count($currentVertices);

            $currentVertex = '';

            foreach ($currentVertices as $i => $iValue) {
                if ($iValue[2] === 'Z') {
                    continue;
                }

                $currentVertices[$i] = self::$_Vertices[$iValue][$dir];

                $currentVertex .= $currentVertices[$i][2];

                if (str_ends_with($currentVertex, 'Z')) {
                    $currentMoves[] = ($steps + 1) / $count;

                    $calculate++;
                }
            }

            if ($calculate === $numberVertices) {
                // Time for math!
                break;
            }
        }

        $steps = 0;

        foreach ($currentMoves as $currentMove) {
            if ($steps === 0) {
                $steps = $currentMove;
            }
            else {
                $steps *= $currentMove;
            }
        }

        return $steps * $count;
    }

    public static function runInstructionsGraph(string $directions): int
    {
        $steps = 0;

        $currentVertices = [];
        $endVertices     = [];

        $Names = array_keys(self::$_Vertices);

        $count = strlen($directions);

        foreach ($Names as $Name) {
            if ($Name[2] === 'A') {
                $currentVertices[] = $Name;
            }
            else if ($Name[2] === 'Z') {
                $endVertices[] = $Name;
            }
        }

        foreach ($currentVertices as $start) {
            $search = new BreadthFirst(self::$_Vertices[$start]['_v']);

            foreach ($endVertices as $end) {
                try {
                    $distance = $search->getDistance(self::$_Vertices[$end]['_v']);

                    if ($steps === 0) {
                        $steps = $distance;
                    }
                    else {
                        $steps *= $distance;
                    }

                    unset($endVertices[$end]);

                    break;
                }
                catch (\Fhaculty\Graph\Exception\OutOfBoundsException) {
                    continue;
                }
            }
        }

        return $steps * $count;
    }

    private static function checkGraph(): void
    {
        if (self::$graph === null) {
            self::$graph = new Graph();
        }
    }

    public static function __callStatic(string $name, array $arguments): int
    {
        if ($name === 'trueLove') {
            print self::$Blave . "\n\n";
        }

        return 0;
    }
}

Darude::trueLove();

Timer::start('Load data');

$Thing = fopen('Data/Data.txt', 'rb+');

$Instructions = trim(fgets($Thing));

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    [$Node, $Connections] = explode(' = ', $Line, 2);

    $Connections = str_replace(['(', ')'], '', $Connections);

    [$Left, $Right] = explode(', ', $Connections, 2);

    Darude::addVertex($Node, ['L' => $Left, 'R' => $Right]);
}

fclose($Thing);

Darude::createConnections();

Timer::stop('Load data');

Timer::start('Steps taken');

$Steps = Darude::runInstructions($Instructions);

Timer::stop('Steps taken');

Timer::start('Multi steps taken');

$StepsMulti = Darude::runInstructionsMulti($Instructions);

Timer::stop('Multi steps taken');

Timer::start('Graph steps taken');

$StepsGraph = Darude::runInstructionsGraph($Instructions);

Timer::stop('Graph steps taken');

print "Steps taken: $Steps\n";
print "Multi steps taken: $StepsMulti\n";
print "Graph steps taken: $StepsGraph\n\n";

Timer::stop('App run time');

foreach (Timer::getTimers() as $Timer) {
    $Took = Timer::read($Timer);

    print "{$Timer}: took {$Took}ms\n";
}
