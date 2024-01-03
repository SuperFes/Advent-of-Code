<?php

use Fhaculty\Graph\Set\Vertices;

class Constantinople
{
    // Arrows: LURD
    private static array $Paths          = [];
    private static array $CompletedPaths = [];
    private static array $Map            = [];

    private static int $CompletedLength = 0;

    private static int $Width  = 0;
    private static int $Height = 0;

    private static int $StartX = 0;
    private static int $StartY = 0;
    private static int $EndX   = 0;
    private static int $EndY   = 0;

    private static array $Forks = [];

    private static array $ForkMap   = [];
    private static array $Vertices  = [];
    private static array $ForkCosts = [];
    private static array $ForkPaths = [];

    public static function AddRow(string $Line): void
    {
        self::$Map[] = $Line;

        self::$Height++;
        self::$Width = strlen($Line);
    }

    public static function GetStart(): array
    {
        for ($i = 0; $i < self::$Width; $i++) {
            if (self::$Map[0][$i] === '.') {
                return [$i, 0];
            }
        }

        return [0, 0];
    }

    public static function GetEnd(): array
    {
        for ($i = 0; $i < self::$Width; $i++) {
            if (self::$Map[self::$Height - 1][$i] === '.') {
                return [$i, self::$Height - 1];
            }
        }

        return [0, 0];
    }

    private static function Distance(int $x, int $y, int $tx, int $ty): int
    {
        return abs($x - $tx) + abs($y - $ty);
    }

    public static function SteppedHighestCost(bool $CanClimb = false): int
    {
        [$StartX, $StartY] = self::GetStart();
        [$ToX, $ToY] = self::GetEnd();

        self::$Paths = [["{$StartX}x{$StartY}" => 0]];

        $Count = 0;

        print "Running paths...";

        while (!empty(self::$Paths)) {
            $Count++;

            $Path = array_shift(self::$Paths);

            $PathDepth = count($Path);

            $Head = array_key_last($Path);

            [$X, $Y] = explode('x', $Head);

            // [ 0 => Right, 1 => Down, 2 => Left, 3 => Up ]
            $Possibles = ['Right' => [(int)$X + 1, (int)$Y], 'Down' => [(int)$X, (int)$Y + 1], 'Left' => [(int)$X - 1, (int)$Y], 'Up' => [(int)$X, (int)$Y - 1]];

            if ($Count % 100000 === 0) {
                print ".";
            }

            foreach ($Possibles as $Dir => [$NextX, $NextY]) {
                $Name = "{$NextX}x{$NextY}";

                if (isset($Path[$Name])) {
                    continue;
                }

                $MapChar = self::$Map[$NextY][$NextX] ?? '#';

                if ($MapChar === '#') {
                    continue;
                }

                if (!$CanClimb && $Dir === 'Up' && $MapChar === 'v') {
                    continue;
                }

                if (!$CanClimb && $Dir === 'Left' && $MapChar === '>') {
                    continue;
                }

                if (!$CanClimb && $Dir === 'Right' && $MapChar === '<') {
                    continue;
                }

                if (!$CanClimb && $Dir === 'Down' && $MapChar === '^') {
                    continue;
                }

                $NewPath = $Path;

                $NewPath[$Name] = $PathDepth + 1;

                $NewPathDepth = count($NewPath) - 1;

                if ($NextX === $ToX && $NextY === $ToY) {
                    if ($NewPathDepth > self::$CompletedLength) {
                        self::$CompletedLength = $NewPathDepth;

                        print " ({$NewPathDepth}) ";
                    }

                    self::$CompletedPaths[] = $NewPath;
                }
                else {
                    array_unshift(self::$Paths, $NewPath);
                }
            }
        }

        print " done!\n";

        $HighestCost = 0;
        $PathString  = "";
        $PathCost    = 0;

        foreach (self::$CompletedPaths as $CompletePath) {
            $Taken = array_keys($CompletePath);

            $PathString = implode('->', $Taken);
            $PathCost   = count($CompletePath) - 1;

            if ($PathCost > $HighestCost) {
                $HighestCost = $PathCost;
            }
        }

        return $HighestCost;
    }


    public static function SteppedHighestCostPriority(bool $CanClimb = false): int
    {
        [$StartX, $StartY] = self::GetStart();
        [$ToX, $ToY] = self::GetEnd();

        $Queue = new SplPriorityQueue();

        $Queue->insert(["{$StartX}x{$StartY}" => 0], 0);

        $Count = 0;

        print "Running paths...";

        while (!$Queue->isEmpty()) {
            $Count++;

            $Path = $Queue->extract();

            $PathDepth = count($Path);

            $Head = array_key_last($Path);

            [$X, $Y] = explode('x', $Head);

            if ($Count % 100000 === 0) {
                print ".";
            }

            $NewPath = $Path;

            while (self::NoSplit($X, $Y)) {
                // [ 0 => Right, 1 => Down, 2 => Left, 3 => Up ]
                $Possibles = ['Right' => [(int)$X + 1, (int)$Y], 'Down' => [(int)$X, (int)$Y + 1], 'Left' => [(int)$X - 1, (int)$Y], 'Up' => [(int)$X, (int)$Y - 1]];

                foreach ($Possibles as $Dir => [$NextX, $NextY]) {
                    $Name = "{$NextX}x{$NextY}";

                    if (isset($NewPath[$Name])) {
                        continue;
                    }

                    $MapChar = self::$Map[$NextY][$NextX] ?? '#';

                    if ($MapChar === '#') {
                        continue;
                    }

                    if (!$CanClimb && $Dir === 'Up' && $MapChar === 'v') {
                        continue;
                    }

                    if (!$CanClimb && $Dir === 'Left' && $MapChar === '>') {
                        continue;
                    }

                    if (!$CanClimb && $Dir === 'Right' && $MapChar === '<') {
                        continue;
                    }

                    if (!$CanClimb && $Dir === 'Down' && $MapChar === '^') {
                        continue;
                    }

                    $NewPath[$Name] = count($NewPath);

                    $X = $NextX;
                    $Y = $NextY;

                    if ($NextX === $ToX && $NextY === $ToY) {
                        break;
                    }
                }
                if ($NextX === $ToX && $NextY === $ToY) {
                    break;
                }
            }

            $HeadNext = array_key_last($NewPath);

            $NewPathDepth = count($NewPath);

            [$NextX, $NextY] = explode('x', $HeadNext);

            if ($NextX === $ToX && $NextY === $ToY) {
                if ($NewPathDepth > self::$CompletedLength) {
                    self::$CompletedLength = $NewPathDepth;

                    print " ({$NewPathDepth}) ";
                }

                self::$CompletedPaths[] = $NewPath;
            }
            else {
                $Queue->insert($NewPath, $NewPathDepth);
            }

            $Possibles = ['Right' => [(int)$X + 1, (int)$Y], 'Down' => [(int)$X, (int)$Y + 1], 'Left' => [(int)$X - 1, (int)$Y], 'Up' => [(int)$X, (int)$Y - 1]];

            foreach ($Possibles as $Dir => [$NextX, $NextY]) {
                $Name = "{$NextX}x{$NextY}";

                if (isset($Path[$Name])) {
                    continue;
                }

                $MapChar = self::$Map[$NextY][$NextX] ?? '#';

                if ($MapChar === '#') {
                    continue;
                }

                if (!$CanClimb && $Dir === 'Up' && $MapChar === 'v') {
                    continue;
                }

                if (!$CanClimb && $Dir === 'Left' && $MapChar === '>') {
                    continue;
                }

                if (!$CanClimb && $Dir === 'Right' && $MapChar === '<') {
                    continue;
                }

                if (!$CanClimb && $Dir === 'Down' && $MapChar === '^') {
                    continue;
                }

                $NextPath = $NewPath;

                $NextPath[$Name] = count($NextPath);

                $NewPathDepth = count($NextPath);

                $HeadNext = array_key_last($NextPath);

                [$NextX, $NextY] = explode('x', $HeadNext);

                if ($NextX === $ToX && $NextY === $ToY) {
                    if ($NewPathDepth > self::$CompletedLength) {
                        self::$CompletedLength = $NewPathDepth;

                        print " ({$NewPathDepth}) ";
                    }

                    self::$CompletedPaths[] = $NextPath;
                }
                else {
                    $Queue->insert($NextPath, $NewPathDepth);
                }
            }
        }

        print " done!\n";

        $HighestCost = 0;
        $PathString  = "";
        $PathCost    = 0;

        foreach (self::$CompletedPaths as $CompletePath) {
            $Taken = array_keys($CompletePath);

            $PathString = implode('->', $Taken);
            $PathCost   = count($CompletePath) - 1;

            if ($PathCost > $HighestCost) {
                $HighestCost = $PathCost;
            }
        }

        return $HighestCost;
    }

    public static function &GetMap(): array
    {
        return self::$Map;
    }

    private static function NoSplit(int $X, int $Y): bool
    {
        $Paths = 0;

        for ($SY = $Y - 1; $SY <= $Y + 1; $SY++) {
            for ($SX = $X - 1; $SX <= $X + 1; $SX++) {
                if ($SX === $X || $SY === $Y) {
                    if ($SX === $X && $SY === $Y) {
                        continue;
                    }

                    $Point = self::$Map[$SY][$SX] ?? '#';

                    if ($Point !== '#') {
                        $Paths++;
                    }
                }
            }
        }

        return $Paths < 3;
    }

    private static function FindForks(): void
    {
        [$X, $Y] = self::GetStart();

        self::$Forks["{$X}x{$Y}"] = [$X, $Y];

        for ($Y = 0; $Y < self::$Height; $Y++) {
            for ($X = 0; $X < self::$Width; $X++) {
                if (self::$Map[$Y][$X] !== '.') {
                    continue;
                }

                $Point = self::$Map[$Y][$X - 1] ?? '#';
                $Point .= self::$Map[$Y][$X + 1] ?? '#';
                $Point .= self::$Map[$Y - 1][$X] ?? '#';
                $Point .= self::$Map[$Y + 1][$X] ?? '#';

                if (substr_count($Point, '#') < 2) {
                    self::$Forks["{$X}x{$Y}"] = [$X, $Y];
                }
            }
        }

        [$X, $Y] = self::GetEnd();

        self::$Forks["{$X}x{$Y}"] = [$X, $Y];
    }

    private static function FindPath(int $StartX, int $StartY, int $ToX, int $ToY, bool $StopAtFork = true): array
    {
        $Queue = new SplPriorityQueue();

        $Queue->insert(["{$StartX}x{$StartY}" => 0], 0);

        $Count = 0;

        $Paths = [];

        while (!$Queue->isEmpty()) {
            $Count++;

            $Path = $Queue->extract();

            $PathDepth = count($Path);

            $Head = array_key_last($Path);

            [$X, $Y] = explode('x', $Head);

            // [ 0 => Right, 1 => Down, 2 => Left, 3 => Up ]
            $Possibles = ['Right' => [(int)$X + 1, (int)$Y], 'Down' => [(int)$X, (int)$Y + 1], 'Left' => [(int)$X - 1, (int)$Y], 'Up' => [(int)$X, (int)$Y - 1]];

            foreach ($Possibles as $Dir => [$NextX, $NextY]) {
                $NewPath = $Path;

                $Name = "{$NextX}x{$NextY}";

                if (isset($NewPath[$Name])) {
                    continue;
                }

                $MapChar = self::$Map[$NextY][$NextX] ?? '#';

                if ($MapChar === '#') {
                    continue;
                }

                $NewPath[$Name] = count($NewPath);

                if ($NextX === $ToX && $NextY === $ToY) {
                    $Paths[] = $NewPath;

                    continue;
                }

                if ($StopAtFork && !self::NoSplit($NextX, $NextY)) {
                    continue;
                }

                $Queue->insert($NewPath, $NewPath[$Name]);
            }
        }

        if (count($Paths) > 1) {
            $Selected = null;
            $Longest  = 0;

            foreach ($Paths as $Key => $Path) {
                if (count($Path) > $Longest) {
                    $Selected = $Key;

                    $Longest = count($Path);
                }
            }

            return $Paths[$Selected];
        }

        return $Paths[0] ?? [];
    }

    public static function ForkedDistanceHighestCost(): int
    {
        self::FindForks();

        print "Finding paths...";

        foreach (self::$Forks as $Name => $Fork) {
            foreach (self::$Forks as $ToName => $ToFork) {
                if ($Name === $ToName) {
                    continue;
                }

                if (isset(self::$ForkMap[$ToName][$Name])) {
                    self::$ForkMap[$Name][$ToName] = self::$ForkMap[$ToName][$Name];

                    self::$ForkCosts["{$Name}->{$ToName}"] = self::$ForkMap[$ToName][$Name]['c'];

                    self::$ForkPaths["{$Name}->{$ToName}"] = array_reverse(self::$ForkMap[$ToName][$Name]['p']);

                    continue;
                }

                $Path = self::FindPath($Fork[0], $Fork[1], $ToFork[0], $ToFork[1], true);

                if (!empty($Path)) {
                    // Save the fork to our forkmap
                    if (!isset(self::$ForkMap[$Name])) {
                        self::$ForkMap[$Name] = [];
                    }

                    $Length = count($Path);

                    self::$ForkMap[$Name][$ToName] = ['c' => $Length, 'p' => $Path];

                    self::$ForkCosts["{$Name}->{$ToName}"] = $Length;

                    self::$ForkPaths["{$Name}->{$ToName}"] = $Path;
                }
            }
        }

        print " done.\n";

        $Graph = new Fhaculty\Graph\Graph();

        foreach (self::$Forks as $Name => $Fork) {
            self::$Vertices[$Name] = $Graph->createVertex($Name);
        }

        foreach (self::$ForkMap as $Name => $Fork) {
            foreach ($Fork as $ToName => $ToFork) {
                self::$Vertices[$Name]->createEdgeTo(self::$Vertices[$ToName])->setWeight($ToFork['c'])->setFlow($ToFork['c']);
                self::$Vertices[$ToName]->createEdgeTo(self::$Vertices[$Name])->setWeight($ToFork['c'])->setFlow($ToFork['c']);
            }
        }

        [$X, $Y] = self::GetStart();

        $First = "{$X}x{$Y}";

        [$X, $Y] = self::GetEnd();

        $Last = "{$X}x{$Y}";

        $HighestCost = 0;

        $VertexList = [[$First => 0]];

        print "Running paths...";

        while (!empty($VertexList)) {
            $Path = array_shift($VertexList);

            $Name = array_key_last($Path);

            $Vertex = self::$Vertices[$Name];

            $Did = [];

            foreach ($Vertex->getVerticesEdge() as $ToVertex) {
                $NewPath = $Path;

                $ToName = $ToVertex->getId();

                if (isset($NewPath[$ToName]) || in_array($ToName, $Did, true)) {
                    continue;
                }

                $Did[] = $ToName;

                $NewPath[$ToName] = self::$ForkCosts["{$Name}->{$ToName}"];

                $NewCost = array_sum($NewPath);

                if ($ToName === $Last) {
                    if ($HighestCost < $NewCost) {
                        print " (" . $NewCost . ") ";

                        $HighestCost = $NewCost;
                    }
                }
                else {
                    $VertexList[] = $NewPath;
                }
            }
        }

        print " done.\n";

        return $HighestCost;
    }

    public static function FindHighestCostPath(): int
    {
        $HighestCost = 0;

        self::FindForks();

        print "Finding paths...";

        foreach (self::$Forks as $Name => $Fork) {
            foreach (self::$Forks as $ToName => $ToFork) {
                if ($Name === $ToName) {
                    continue;
                }

                $Path = self::FindPath($Fork[0], $Fork[1], $ToFork[0], $ToFork[1], true);

                if (!empty($Path)) {
                    // Save the fork to our forkmap
                    if (!isset(self::$ForkMap[$Name])) {
                        self::$ForkMap[$Name] = [];
                    }

                    $Length = count($Path);

                    self::$ForkMap[$Name][$ToName] = ['c' => $Length, 'p' => $Path];

                    self::$ForkCosts["{$Name}->{$ToName}"] = $Length;

                    self::$ForkPaths["{$Name}->{$ToName}"] = $Path;
                }
            }
        }

        print " done.\n";

        $ForkNames = array_keys(self::$Forks);

        $Queue = new SplMaxHeap();

        $Start = self::GetStart();

        $StartName = "{$Start[0]}x{$Start[1]}";

        $Queue->insert([array_key_first(self::$Forks) => ['c' => 0, 'p' => [], 'f' => 0]]);

        $End = self::GetEnd();

        $EndName = "{$End[0]}x{$End[1]}";

        $Done = [];

        $Count = 0;

        print "Running paths...";

        while (!$Queue->isEmpty()) {
            $Count++;

            $Path = $Queue->extract();

            if ($Count % 10000 === 0) {
                print ".";
            }

            foreach ($Path as $Name => $Attr) {
                $ForksTaken = $Attr['f'];

                $Head = array_key_last($Attr['p']) ?? $Name;

                foreach (self::$ForkMap[$Name] as $ToName => $Attributes) {
                    $FromTo = "{$Name}->{$ToName}";

                    $DoneKey = "{$Head}:{$FromTo}";

                    if (self::Intersects($Path, self::$ForkPaths[$FromTo])) {
                        continue;
                    }

                    if (isset($Attr['p'][$ToName])) {
                        continue;
                    }

                    if (isset($Done[$DoneKey])) {
                        continue;
                    }

                    $Done[$DoneKey] = true;

                    $NewPathPath = array_merge($Attr['p'], self::$ForkPaths[$FromTo]);
                    $NewPathCost = count(array_keys($NewPathPath)) - 1;

                    // Remove the fork
                    if ($ToName === $EndName) {
                        if ($NewPathCost > $HighestCost) {
                            print " (" . $NewPathCost . ") ";

                            $HighestCost = $NewPathCost;
                        }
                    }
                    else {
                        array_pop($NewPathPath);

                        $NewPath = [$ToName => ['c' => $NewPathCost, 'p' => $NewPathPath, 'f' => $ForksTaken + 1]];

                        $Queue->insert($NewPath);
                    }
                }
            }
        }

        print " done.\n";

        return $HighestCost;
    }


    public static function FindHighestRecursePath(array $From = [], array $Ran = []): int
    {
        $HighestCost = 0;

        if (empty($From)) {
            self::FindForks();

            print "Finding paths...";

            foreach (self::$Forks as $Name => $Fork) {
                foreach (self::$Forks as $ToName => $ToFork) {
                    if ($Name === $ToName) {
                        continue;
                    }

                    $Path = self::FindPath($Fork[0], $Fork[1], $ToFork[0], $ToFork[1], true);

                    if (!empty($Path)) {
                        // Save the fork to our forkmap
                        if (!isset(self::$ForkMap[$Name])) {
                            self::$ForkMap[$Name] = [];
                        }

                        $Length = count($Path);

                        self::$ForkMap[$Name][$ToName] = ['c' => $Length, 'p' => $Path];

                        self::$ForkCosts["{$Name}->{$ToName}"] = $Length;

                        self::$ForkPaths["{$Name}->{$ToName}"] = $Path;
                    }
                }
            }

            print " done.\n";

            $From = self::GetStart();
        }

        $End = self::GetEnd();

        $EndName = "{$End[0]}x{$End[1]}";

        $Name = "{$From[0]}x{$From[1]}";

        $Biggerst = 0;

        if (!isset(self::$ForkMap[$Name])) {
            throw new RuntimeException('I can\'t believe it\'s butter... ' . $Name . ' after ' . implode(' -> ', $Ran));
        }

        foreach (self::$ForkMap[$Name] as $ToName => $Attributes) {
            if (!self::IntersectsSimple($Ran, $Attributes['p'])) {
                $NewPath = $Ran;

                foreach ($Attributes['p'] as $Key => $Val) {
                    $NewPath[$Key] = $Val;
                }

                $NextPop = array_key_last($Attributes['p']);

                if ($NextPop === $EndName) {
                    $Length = count($NewPath) - 1;

                    if ($Length > 6404) {
                        print " (" . $Length . ") ";
                    }

                    return $Length;
                }

                $Next = explode('x', $NextPop);

                $RunResult = self::FindHighestRecursePath($Next, $NewPath);

                if ($RunResult > $Biggerst) {
                    $Biggerst = $RunResult;
                }
            }
        }

        return $Biggerst;
    }

    private static function ForkBomb(array $elements, array $permutations = []): Generator
    {
        if (empty($elements)) {
            yield $permutations;
        }
        else {
            for ($i = 0, $iMax = count($elements); $i < $iMax; $i++) {
                $new_items = $elements;
                $new_perms = $permutations;

                [$foo] = array_splice($new_items, $i, 1);

                array_unshift($new_perms, $foo);

                foreach (self::ForkBomb($new_items, $new_perms) as $permutation) {
                    yield $permutation;
                }
            }
        }
    }

    private static function Intersects(array $Path, array $NewPath): bool
    {
        $Intersections = 0;

        $NewPathNames = array_keys($NewPath);

        foreach ($Path as $Attr) {
            $Intersection = array_intersect(array_keys($Attr['p']), $NewPathNames);

            $Intersections += count($Intersection);
        }

        return $Intersections > 1;
    }

    private static function IntersectsSimple(array $Path, array $NewPath): bool
    {
        $PathNames    = array_keys($Path);
        $NewPathNames = array_keys($NewPath);

        $Intersection = array_intersect($PathNames, $NewPathNames);

        return count($Intersection) > 1;
    }

    private static function GetPathCost(array $Path): int
    {
        $Cost = 0;

        foreach ($Path as $Attributes) {
            $Cost += $Attributes['c'];
        }

        return $Cost;
    }
}
