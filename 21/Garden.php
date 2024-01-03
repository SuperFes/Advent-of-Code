<?php

class Point
{
    public int $X;
    public int $Y;

    public function __construct(int $X, int $Y)
    {
        $this->X = $X;
        $this->Y = $Y;
    }
}

class Garden
{
    private static array   $_OrigMap;

    private static int   $_Width  = 0;
    private static int   $_Height = 0;
    private static array $MapGrid = [];

    private static ?Point $Started = null;

    public static function Print(): void
    {
        $Garden = self::$MapGrid['0x0']['Map'];

        foreach ($Garden as $Row) {
            print "{$Row}\n";
        }

        print "\n";
    }

    private static function FindMapOs(string $MapCoord): int
    {
        $Map = implode('', self::$MapGrid[$MapCoord]['Map']);

        return substr_count($Map, 'O') + substr_count($Map, 'S');
    }

    private static function FindOs(): int
    {
        $Os = 0;

        foreach (self::$MapGrid as $Map => $Item) {
            $Os += self::FindMapOs($Map);
        }

        return $Os;
    }

    private static function Plop(int $StartX, int $StartY): void
    {
        for ($Y = $StartY - 1; $Y <= $StartY + 1; $Y++) {
            for ($X = $StartX - 1; $X <= $StartX + 1; $X++) {
                if ($Y === $StartY && $X === $StartX) {
                    self::SetPoint($X, $Y, '.');
                }
                else if ($Y === $StartY || $X === $StartX) {
                    $Point = self::GetPoint($X, $Y);

                    if ($Point === '.') {
                        self::SetPoint($X, $Y, 'O');
                    }
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    public static function SeekAndDestroy(int $Depth = 1): int
    {
        $Offset = (int)floor(self::$_Height / 2);

        $RecordIntervals = [
            $Offset,
            $Offset + self::$_Height,
            $Offset + self::$_Height * 2,
            $Offset * self::$_Height * 3,
        ];

        $Counter = [];

        if ($Depth !== 26_501_365) {
            $RecordIntervals = [0];
        }

        if (self::$Started === null) {
            for ($Y = 0; $Y < self::$_Height; $Y++) {
                for ($X = 01; $X < self::$_Width; $X++) {
                    $Point = self::GetPoint($X, $Y);

                    if (self::$Started === null && $Point === 'S') {
                        self::$Started = new Point($X, $Y);

                        break;
                    }
                }

                if (self::$Started !== null) {
                    break;
                }
            }

            self::SetPoint(self::$Started->X, self::$Started->Y, '.');

            self::$_OrigMap = self::$MapGrid['0x0']['Map'];

            self::SetPoint(self::$Started->X, self::$Started->Y, 'S');
        }

        $Points = [];

        for ($i = 1; $i <= $Depth; $i++) {
            foreach (self::$MapGrid as $Name => $Item) {
                [$MapX, $MapY] = explode('x', $Name);

                $MinX = $MapX * self::$_Width;
                $MaxX = $MinX + self::$_Width;
                $MinY = $MapY * self::$_Height;
                $MaxY = $MinY + self::$_Height;

                for ($Y = $MinY; $Y < $MaxY; $Y++) {
                    for ($X = $MinX; $X < $MaxX; $X++) {
                        $Point = self::GetPoint($X, $Y);

                        if ($Point === 'O' || $Point === 'S') {
                            $Points[] = new Point($X, $Y);
                        }
                    }
                }
            }

            while (!empty($Points)) {
                $Point = array_shift($Points);

                self::Plop($Point->X, $Point->Y);
            }

            $Plotted = false;

            if (self::GetPoint(self::$Started->X, self::$Started->Y) === '.') {
                self::SetPoint(self::$Started->X, self::$Started->Y, 'S');

                $Plotted = true;
            }

            if ($Plotted) {
                self::SetPoint(self::$Started->X, self::$Started->Y, '.');
            }

            if ($i === $RecordIntervals[0]) {
                $Counter[] = self::FindOs();

                array_shift($RecordIntervals);

                if (count($Counter) === 3) {
                    $NumSteps = (int)floor($Depth / self::$_Height);

                    $NextNum = self::CalculateInterval($Counter, $NumSteps);

                    return $NextNum;
                }
            }
        }

        return self::FindOs();
    }

    private static function GetPoint(int $X, int $Y): ?string
    {
        $AX = self::Mod($X, self::$_Width);
        $AY = self::Mod($Y, self::$_Height);

        $MapCoord = self::GetMapCoord($X, $Y);

        if (!isset(self::$MapGrid[$MapCoord])) {
            return self::$_OrigMap[$AY][$AX];
        }

        return self::$MapGrid[$MapCoord]['Map'][$AY][$AX] ?? null;
    }

    private static function SetPoint(int $X, int $Y, string $Char): void
    {
        $AX = self::Mod($X, self::$_Width);
        $AY = self::Mod($Y, self::$_Height);

        $MapCoord = self::GetMapCoord($X, $Y, true);

        self::$MapGrid[$MapCoord]['Map'][$AY][$AX] = $Char;
    }

    private static function GetMapCoord(int $X, int $Y, bool $CreateMap = false): string
    {
        $MapX = 0;
        $MapY = 0;

        if (self::$_Width && self::$_Height) {
            $Dir = 1;

            if ($X < 0) {
                $MapX--;

                $Dir = -1;
            }

            while (abs($X) >= self::$_Width) {
                $X -= self::$_Width * $Dir;

                $MapX += $Dir;
            }

            $Dir = 1;

            if ($Y < 0) {
                $MapY--;

                $Dir = -1;
            }

            while (abs($Y) >= self::$_Height) {
                $Y -= self::$_Height * $Dir;

                $MapY += $Dir;
            }
        }

        $MapCoord = "{$MapX}x{$MapY}";

        if ($CreateMap && !isset(self::$MapGrid[$MapCoord])) {
            self::CreateMap($MapCoord);
        }

        return $MapCoord;
    }

    private static function Mod(int $L, int $N): int
    {
        return (($L %= $N) < 0) ? $L + $N : $L;
    }

    private static function CreateMap(string $MapCoord): void
    {
        $Map = [];

        if (!empty(self::$_OrigMap)) {
            $Map = self::$_OrigMap;
        }

        self::$MapGrid[$MapCoord] = ['Map' => $Map, 'Next' => [], 'Loop' => false, 'Cur' => null];
    }

    public static function AddToMap(string $Line): void
    {
        $MapCoord = self::GetMapCoord(0, 0, true);

        self::$MapGrid[$MapCoord]['Map'][] = $Line;

        self::$_Width = strlen($Line);
        self::$_Height++;
    }

    public static function Reset(): void
    {
        self::$MapGrid = ['0x0' => ['Map' => self::$_OrigMap, 'Next' => [], 'Loop' => false, 'Cur' => null]];

        self::SetPoint(self::$Started->X, self::$Started->Y, 'S');
    }

    private static function CalculateInterval(array $FoundAt, int $ToLength = 5): int
    {
        $Row = 0;

        $RowTotal = count(array_unique($FoundAt));

        $History = [$FoundAt];

        while (count($History[0]) <= $ToLength) {
            while ($RowTotal > 1) {
                $Row++;

                if (!isset($History[$Row])) {
                    $History[$Row] = [];
                }

                $Length = count($History[$Row - 1]);

                for ($i = 1; $i < $Length; $i++) {
                    $Diff = $History[$Row - 1][$i] - $History[$Row - 1][$i - 1];

                    $History[$Row][] = $Diff;

                }

                $RowTotal = count(array_unique($History[$Row]));
            }

            for ($Row = count($History) - 1; $Row > 0; $Row--) {
                $Last   = array_key_last($History[$Row]);
                $UpLast = array_key_last($History[$Row - 1]);

                $History[$Row - 1][] = $History[$Row][$Last] + $History[$Row - 1][$UpLast];
            }
        }

        return end($History[0]);
    }
}
