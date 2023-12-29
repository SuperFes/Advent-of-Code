<?php

class MinPathHeap extends SplHeap
{
    final public function compare($value1, $value2): int
    {
        $Left = 0;

        foreach ($value1 as $Step) {
            $Left += $Step['r'];
        }

        $Right = 0;

        foreach ($value2 as $Step) {
            $Right += $Step['r'];
        }

        return $Right - $Left;
    }
}

class Convection
{
    private static array $Costs  = [];
    private static int   $Width  = 0;
    private static int   $Height = 0;

    public static function AddRow(string $Line): void
    {
        $Len = strlen($Line);

        $Row        = [];

        for ($i = 0; $i < $Len; $i++) {
            $Row[]        = (int)$Line[$i];
        }

        self::$Height++;
        self::$Width = count($Row);

        self::$Costs[]        = $Row;
    }

    private static function Distance(int $x, int $y, int $tx, int $ty): int
    {
        return abs($x - $tx) + abs($y - $ty);
//        return sqrt((($tx - $x) ** 2) + (($ty - $y) ** 2));
    }

    public static function GraphLowestCost(int $MinMoves, int $MaxMoves): int
    {
        $ToX = self::$Width - 1;
        $ToY = self::$Height - 1;

        $End = "{$ToX}x{$ToY}";

        $Chosened = [];

        $Count = 0;

        print "Running paths...";

        $Start = "0x0";

        $Cost = 0;

        $CheapestPath = PHP_INT_MAX;

        $Paths = new MinPathHeap();

        $Paths->insert([$Start => ['x' => 0, 'y' => 0, 'c' => $Cost, 'r' => 0, 'd' => 1, 'm' => 0]]);
        $Paths->insert([$Start => ['x' => 0, 'y' => 0, 'c' => $Cost, 'r' => 0, 'd' => 2, 'm' => 0]]);

        while (!$Paths->isEmpty()) {
            $Path = $Paths->extract();

            $Head = array_key_last($Path);

            $X    = $Path[$Head]['x'];
            $Y    = $Path[$Head]['y'];
            $Dir  = $Path[$Head]['d'];
            $Move = $Path[$Head]['m'];

            $PathCost = $Path[$Head]['c'];

            $Count++;

            if ($Count % 100000 === 0) {
                print ".";
            }

            $PathSeen = "{$Dir}:{$X}x{$Y}@{$Move}";

            $Seen = $Chosened[$PathSeen] ?? 0;

            if ($Seen > 0) {
                continue;
            }

            if ($Seen === 0) {
                $Chosened[$PathSeen] = 0;
            }

            $Chosened[$PathSeen]++;

            if ("{$X}x{$Y}" === $End && $Move >= $MinMoves) {
                if ($PathCost < $CheapestPath) {
                    $CheapestPath = $PathCost;
                }

                continue;
            }

            if ($Move >= $MinMoves) {
                // [ 0 => Up, 1 => Right, 2 => Down, 3 => Left ]
                $Possibles = [0 => [(int)$X, (int)$Y - 1], 1 => [(int)$X + 1, (int)$Y], 2 => [(int)$X, (int)$Y + 1], 3 => [(int)$X - 1, (int)$Y]];

                if ($Dir === 0 || $Dir === 2) {
                    unset($Possibles[0], $Possibles[2]);
                }
                else if ($Dir === 1 || $Dir === 3) {
                    unset($Possibles[1], $Possibles[3]);
                }

                foreach ($Possibles as $NextDir => [$NextX, $NextY]) {
                    if (!isset(self::$Costs[$NextY][$NextX])) {
                        continue;
                    }

                    $Name = "{$NextX}x{$NextY}";

                    if (isset($Path[$Name])) {
                        continue;
                    }

                    $NextCost     = self::$Costs[$NextY][$NextX] ?? null;
                    $NextRealCost = self::$Costs[$NextY][$NextX] ?? null;

                    if ($NextCost !== null) {
                        $NewPath = [$Name => ['x' => $NextX, 'y' => $NextY, 'c' => $PathCost + $NextCost, 'r' => $PathCost + $NextRealCost, 'd' => $NextDir, 'm' => 1]];

                        $Paths->insert($NewPath);
                    }
                }
            }

            if ($Move < $MaxMoves) {
                if ($Dir === 0) {
                    $Y--;
                }
                else if ($Dir === 1) {
                    $X++;
                }
                else if ($Dir === 2) {
                    $Y++;
                }
                else if ($Dir === 3) {
                    $X--;
                }

                $NextCost     = self::$Costs[$Y][$X] ?? null;
                $NextRealCost = self::$Costs[$Y][$X] ?? null;

                if ($NextCost === null) {
                    continue;
                }

                $NewPath = ["{$X}x{$Y}" => ['x' => $X, 'y' => $Y, 'c' => $PathCost + $NextCost, 'r' => $PathCost + $NextRealCost, 'd' => $Dir, 'm' => $Move + 1]];

                $Paths->insert($NewPath);
            }
        }

        print " done!\n";

        return $CheapestPath;
    }
}
