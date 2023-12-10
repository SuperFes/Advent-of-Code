<?php
const STUCK = 0;
const UP = 1;
const RIGHT = 2;
const DOWN = 3;
const LEFT = 4;

class Turtle
{
    private int $startX;
    private int $startY;

    private int $curX = 0;
    private int $curY = 0;

    private int $dir = 0;

    private ?array $map;

    private string $pipeMap;
    private string $prevPipeMap;

    private static array $pipeMaps = [
        'OFI' => [
            [0, 0, 0],
            [0, 1, 1],
            [0, 1, 2],
        ],
        'I7O' => [
            [0, 0, 0],
            [1, 1, 0],
            [2, 1, 0],
        ],
        'OLI' => [
            [0, 1, 2],
            [0, 1, 1],
            [0, 0, 0],
        ],
        'IJO' => [
            [2, 1, 0],
            [1, 1, 0],
            [0, 0, 0],
        ],
        'O/I' => [
            [0, 0, 0],
            [1, 1, 1],
            [2, 2, 2],
        ],
        'O|I' => [
            [0, 1, 2],
            [0, 1, 2],
            [0, 1, 2],
        ],
        'IFO' => [
            [2, 2, 2],
            [2, 1, 1],
            [2, 1, 0],
        ],
        'O7I' => [
            [2, 2, 2],
            [1, 1, 2],
            [0, 1, 2],
        ],
        'ILO' => [
            [2, 1, 0],
            [2, 1, 1],
            [2, 2, 2],
        ],
        'OJI' => [
            [0, 1, 2],
            [1, 1, 2],
            [2, 2, 2],
        ],
        'I/O' => [
            [2, 2, 2],
            [1, 1, 1],
            [0, 0, 0],
        ],
        'I|O' => [
            [2, 1, 0],
            [2, 1, 0],
            [2, 1, 0],
        ],
    ];

    public function __construct(array $Map, int $StartX, int $StartY)
    {
        $this->map    = $Map;
        $this->startX = $StartX;
        $this->startY = $StartY;

        $this->curX = $StartX;
        $this->curY = $StartY;

        $StartChar = $this->map[$this->startY][$this->startX];

        if ($StartChar === 'F') {
            $this->pipeMap = 'OFI';

            $this->dir = RIGHT;
        }
        else if ($StartChar === 'L') {
            $this->pipeMap = 'OLI';

            $this->dir = RIGHT;
        }
        else if ($StartChar === '7') {
            $this->pipeMap = 'I7O';

            $this->dir = LEFT;
        }
        else if ($StartChar === 'J') {
            $this->pipeMap = 'OJI';

            $this->dir = LEFT;
        }
        else if ($StartChar === '-') {
            $this->pipeMap = 'O/I';

            $this->dir = RIGHT;
        }
        else if ($StartChar === '|') {
            $this->pipeMap = 'O|I';

            $this->dir = UP;
        }
    }

    final public function walkies(): int
    {
        $this->checkFlooding();

        do {
            $PrevChar = $this->map[$this->curY][$this->curX];

            if ($this->dir === UP) {
                $this->curY--;
            }
            else if ($this->dir === RIGHT) {
                $this->curX++;
            }
            else if ($this->dir === DOWN) {
                $this->curY++;
            }
            else if ($this->dir === LEFT) {
                $this->curX--;
            }
            else {
                die('Mario loses!\n');
            }

            $CurChar = $this->map[$this->curY][$this->curX];

            print "$PrevChar -> $CurChar\n";

            $this->prevPipeMap = $this->pipeMap;

            if ($CurChar === 'F') {
                if ($this->pipeMap === 'O/I' ||
                    $this->pipeMap === 'IL7' ||
                    $this->pipeMap === 'OLI' ||
                    $this->pipeMap === 'OJI' ||
                    $this->pipeMap === 'O|I') {
                    $this->pipeMap = 'OFI';
                }
                else {
                    $this->pipeMap = 'IFO';
                }
            }
            else if ($CurChar === 'L') {
                if ($this->pipeMap === 'I/O' ||
                    $this->pipeMap === 'O7I' ||
                    $this->pipeMap === 'IJO' ||
                    $this->pipeMap === 'OFI' ||
                    $this->pipeMap === 'O|I') {
                    $this->pipeMap = 'OLI';
                }
                else {
                    $this->pipeMap = 'ILO';
                }
            }
            else if ($CurChar === '7') {
                if ($this->pipeMap === 'I/O' ||
                    $this->pipeMap === 'OJI' ||
                    $this->pipeMap === 'OLI' ||
                    $this->pipeMap === 'IFO' ||
                    $this->pipeMap === 'O|I') {
                    $this->pipeMap = 'O7I';
                }
                else {
                    $this->pipeMap = 'I7O';
                }
            }
            else if ($CurChar === 'J') {
                if ($this->pipeMap === 'O/I' ||
                    $this->pipeMap === 'OFI' ||
                    $this->pipeMap === 'O7I' ||
                    $this->pipeMap === 'ILO' ||
                    $this->pipeMap === 'O|I') {
                    $this->pipeMap = 'OJI';
                }
                else {
                    $this->pipeMap = 'IJO';
                }
            }
            else if ($CurChar === '-') {
                if ($this->pipeMap === 'O/I' ||
                    $this->pipeMap === 'OJI' ||
                    $this->pipeMap === 'ILO' ||
                    $this->pipeMap === 'I7O' ||
                    $this->pipeMap === 'OFI') {
                    $this->pipeMap = 'O/I';
                }
                else {
                    $this->pipeMap = 'I/O';
                }
            }
            else if ($CurChar === '|') {
                if ($this->pipeMap === 'O|I' ||
                    $this->pipeMap === 'OJI' ||
                    $this->pipeMap === 'OLI' ||
                    $this->pipeMap === 'O7I' ||
                    $this->pipeMap === 'OFI') {
                    $this->pipeMap = 'O|I';
                }
                else {
                    $this->pipeMap = 'I|O';
                }
            }

            $this->dir = self::GetNextDir($this->map, $this->dir, $this->curX, $this->curY);

            $this->checkFlooding();
        }
        while ($this->curX !== $this->startX || $this->curY !== $this->startY);

        $Exes = 0;

        foreach ($this->map as $Line) {
            $Exes += substr_count($Line, 'X');

            $Line = str_replace(['7', 'F', 'J', 'L', '-', '|'], ['╗', '╔', '╝', '╚', '═', '║'], $Line);

            print "$Line\n";
        }

        return $Exes;
    }

    private function checkFlooding(): void {
        $map = self::$pipeMaps[$this->pipeMap];

        for ($y = 0, $curY = $this->curY - 1; $y < 3; $y++, $curY++) {
            for ($x = 0, $curX = $this->curX - 1; $x < 3; $x++, $curX++) {
                print $map[$y][$x] === 2 ? 'X' : ' ';

                if ($map[$y][$x] === 2 && $this->map[$curY][$curX] === ' ') {
                    $this->floodFill($this->map, $curY, $curX, 'X');
                }
            }

            print "\n";
        }
    }

    private function floodFill(array &$map, $x, $y, $newVal, $oldVal = null): void
    {
        $mapHeight = count($map);
        $mapWidth  = strlen($map[0]);

        if ($x >= 0 && $x < $mapHeight && $y >= 0 && $y < $mapWidth) {
            if ($oldVal === null) {
                $oldVal = $map[$x][$y];
            }

            if ($map[$x][$y] === $oldVal) {
                $map[$x][$y] = $newVal;

                $this->floodFill($map, $x + 1, $y, $newVal, $oldVal);
                $this->floodFill($map, $x - 1, $y, $newVal, $oldVal);
                $this->floodFill($map, $x, $y + 1, $newVal, $oldVal);
                $this->floodFill($map, $x, $y - 1, $newVal, $oldVal);
            }
        }
    }

    public static function GetNextDir(array $Map, int $Dir, int $X, int $Y): int
    {
        // 1 = up, 2 = right, 3 = down, 4 = left
        if ($X < 0 || $Y < 0) {
            return STUCK;
        }

        if (!isset($Map[$Y][$X])) {
            return STUCK;
        }

        if ($Dir === 1) {
            if ($Map[$Y][$X] === '|') {
                return UP;
            }

            if ($Map[$Y][$X] === 'F') {
                return RIGHT;
            }

            if ($Map[$Y][$X] === '7') {
                return LEFT;
            }
        }
        else if ($Dir === 2) {
            if ($Map[$Y][$X] === '-') {
                return RIGHT;
            }

            if ($Map[$Y][$X] === '7') {
                return DOWN;
            }

            if ($Map[$Y][$X] === 'J') {
                return UP;
            }
        }
        else if ($Dir === 3) {
            if ($Map[$Y][$X] === '|') {
                return DOWN;
            }

            if ($Map[$Y][$X] === 'L') {
                return RIGHT;
            }

            if ($Map[$Y][$X] === 'J') {
                return LEFT;
            }
        }
        else if ($Dir === 4) {
            if ($Map[$Y][$X] === '-') {
                return LEFT;
            }

            if ($Map[$Y][$X] === 'L') {
                return UP;
            }

            if ($Map[$Y][$X] === 'F') {
                return DOWN;
            }
        }

        return STUCK;
    }
}
