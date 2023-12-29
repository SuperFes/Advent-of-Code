<?php
const Floor    = 1;
const Mirror   = 2;
const Splitter = 3;

class Laser
{
    public const Left  = 0;
    public const Up    = 1;
    public const Right = 2;
    public const Down  = 3;

    private int  $X;
    private int  $Y;
    private int  $Dir;
    private int  $Length = 0;
    private bool $Ends   = false;

    private int $Width;
    private int $Height;

    private array $_Grid;
    private array $_Lasers;

    private bool $FirstMove;

    public function __construct(int $X, int $Y, int $Dir, array &$Grid, array &$Lasers, bool $FirstMove = false)
    {
        $this->X   = $X;
        $this->Y   = $Y;
        $this->Dir = $Dir;

        $this->_Grid   = &$Grid;
        $this->_Lasers = &$Lasers;

        $this->Width  = count($Grid[0]);
        $this->Height = count($Grid);

        $Grid[$Y][$X]['_l'][] = $this;

        $this->FirstMove = $FirstMove;

        if (isset($Grid[$Y][$X]['_e'])) {
            $Grid[$Y][$X]['_e']++;
        }

        $Lasers[] = $this;
    }

    final public function IsSolved(): bool
    {
        return $this->Ends;
    }

    final public function Solve(): bool
    {
        while (!$this->IsSolved()) {
            if (!$this->FirstMove) {
                $MoveX = 0;
                $MoveY = 0;

                if ($this->Dir === self::Left) {
                    $MoveX = -1;
                }
                else if ($this->Dir === self::Up) {
                    $MoveY = -1;
                }
                else if ($this->Dir === self::Right) {
                    $MoveX = 1;
                }
                else if ($this->Dir === self::Down) {
                    $MoveY = 1;
                }

                if ($this->X + $MoveX < 0 ||
                    $this->X + $MoveX >= $this->Width ||
                    $this->Y + $MoveY < 0 ||
                    $this->Y + $MoveY >= $this->Height) {
                    $this->Ends = true;

                    break;
                }

                $this->X += $MoveX;
                $this->Y += $MoveY;

                $this->Length++;

                if (!isset($this->_Grid[$this->Y][$this->X])) {
                    throw new RuntimeException("{$this->X}x{$this->Y} going {$this->Dir} {$this->Width}x{$this->Height} is not on the grid!");
                }

                $this->_Grid[$this->Y][$this->X]['_e']++;
            }

            if ($this->FirstMove) {
                $this->FirstMove = false;
            }

            $HasLeft  = false;
            $HasRight = false;
            $HasUp    = false;
            $HasDown  = false;

            foreach ($this->_Grid[$this->Y][$this->X]['_l'] as $Shark) {
                if ($Shark === $this) {
                    continue;
                }

                switch ($Shark->GetDir()) {
                    case self::Left:
                        $HasLeft = true;
                        break;
                    case self::Up:
                        $HasUp = true;
                        break;
                    case self::Right:
                        $HasRight = true;
                        break;
                    case self::Down:
                        $HasDown = true;
                        break;
                }
            }

            $Char = $this->_Grid[$this->Y][$this->X]['_c'];

            if (($this->Dir === self::Up || $this->Dir === self::Down) && $Char === '-') {
                $this->Ends = true;

                if (!$HasLeft) {
                    $Laser = new Laser($this->X, $this->Y, self::Left, $this->_Grid, $this->_Lasers);

                    $Laser->Solve();
                }

                if (!$HasRight) {
                    $Laser = new Laser($this->X, $this->Y, self::Right, $this->_Grid, $this->_Lasers);

                    $Laser->Solve();
                }
            }
            else if (($this->Dir === self::Left || $this->Dir === self::Right) && $Char === '|') {
                $this->Ends = true;

                if (!$HasUp) {
                    $Laser = new Laser($this->X, $this->Y, self::Up, $this->_Grid, $this->_Lasers);

                    $Laser->Solve();
                }

                if (!$HasDown) {
                    $Laser = new Laser($this->X, $this->Y, self::Down, $this->_Grid, $this->_Lasers);

                    $Laser->Solve();
                }
            }
            else if ($Char === '\\') {
                $this->Ends = true;

                if ($this->Dir === self::Up && !$HasLeft) {
                    $Laser = new Laser($this->X, $this->Y, self::Left, $this->_Grid, $this->_Lasers);

                    $Laser->Solve();
                }
                else if ($this->Dir === self::Down && !$HasRight) {
                    $Laser = new Laser($this->X, $this->Y, self::Right, $this->_Grid, $this->_Lasers);

                    $Laser->Solve();
                }
                else if ($this->Dir === self::Left && !$HasUp) {
                    $Laser = new Laser($this->X, $this->Y, self::Up, $this->_Grid, $this->_Lasers);

                    $Laser->Solve();
                }
                else if ($this->Dir === self::Right && !$HasDown) {
                    $Laser = new Laser($this->X, $this->Y, self::Down, $this->_Grid, $this->_Lasers);

                    $Laser->Solve();
                }
            }
            else if ($Char === '/') {
                $this->Ends = true;

                if ($this->Dir === self::Down && !$HasLeft) {
                    $Laser = new Laser($this->X, $this->Y, self::Left, $this->_Grid, $this->_Lasers);

                    $Laser->Solve();
                }
                else if ($this->Dir === self::Up && !$HasRight) {
                    $Laser = new Laser($this->X, $this->Y, self::Right, $this->_Grid, $this->_Lasers);

                    $Laser->Solve();
                }
                else if ($this->Dir === self::Right && !$HasUp) {
                    $Laser = new Laser($this->X, $this->Y, self::Up, $this->_Grid, $this->_Lasers);

                    $Laser->Solve();
                }
                else if ($this->Dir === self::Left && !$HasDown) {
                    $Laser = new Laser($this->X, $this->Y, self::Down, $this->_Grid, $this->_Lasers);

                    $Laser->Solve();
                }
            }
        }

        return $this->Ends;
    }

    final public function GetDir(): int
    {
        return $this->Dir;
    }
}

class Map
{
    private static array $Grid = [];

    private static array $Lasers = [];

    public static function AddRowToGrid(string $Line): void
    {
        $Xes = [];

        $Len = strlen($Line);

        for ($i = 0; $i < $Len; $i++) {
            $Char = $Line[$i];
            $Type = Floor;

            if ($Char === '/' || $Char === '\\') {
                $Type = Mirror;
            }
            else if ($Char === '|' || $Char === '-') {
                $Type = Splitter;
            }

            $Xes[$i] = ['_c' => $Char, '_t' => $Type, '_l' => [], '_e' => 0];
        }

        self::$Grid[] = $Xes;
    }

    public static function Reset(): void
    {
        foreach (self::$Grid as $Row => $RowData) {
            foreach (self::$Grid[$Row] as $Col => $ColData) {
                self::$Grid[$Row][$Col]['_l'] = [];
                self::$Grid[$Row][$Col]['_e'] = 0;
            }
        }
    }

    public static function IsSolved(): bool
    {
        foreach (self::$Lasers as $Laser) {
            if (!$Laser->IsSolved()) {
                return false;
            }
        }

        return true;
    }

    public static function PrintMap(): void
    {
        foreach (self::$Grid as $Row) {
            $RowStr = '';

            foreach ($Row as $Col) {
                $RowStr .= $Col['_c'];
            }

            print "{$RowStr}\n";
        }
    }

    public static function PrintEnergizedMap(): void
    {
        foreach (self::$Grid as $Row) {
            $RowStr = '';

            foreach ($Row as $Col) {
                if ($Col['_e'] === 0) {
                    $RowStr .= '.';
                }
                else if ($Col['_e'] > 9) {
                    $RowStr .= '#';
                }
                else {
                    $RowStr .= $Col['_e'];
                }
            }

            print "{$RowStr}\n";
        }

        print count(self::$Lasers) . " lasers.\n";
    }

    public static function GetEnergized(): int
    {
        $Energized = 0;

        foreach (self::$Grid as $Y => $Row) {
            foreach ($Row as $X => $Col) {
                if (!isset($Col['_e'])) {
                    throw new RuntimeException("Unset array element '_e' at {$X}x{$Y}");
                }

                if ($Col['_e'] > 0) {
                    $Energized++;
                }
            }
        }

        return $Energized;
    }

    public static function Solve(): void
    {
        foreach (self::$Lasers as $Laser) {
            if (!$Laser->IsSolved()) {
                $Laser->Solve();
            }
        }
    }

    public static function AddLaser(int $X, int $Y, int $Dir): void
    {
        new Laser($X, $Y, $Dir, self::$Grid, self::$Lasers, true);
    }
}
