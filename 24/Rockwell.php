<?php

class HydrocopticMarzlevane
{
    public float $X;
    public float $Y;
    public float $Z;

    public float $VX;
    public float $VY;
    public float $VZ;

    public float $Slope;
    public float $Intercept;

    public float $SlopeZ;
    public float $InterceptZ;

    private float $TimeOffset = 0;

    public int $SerialNo;

    private static int $_SerialNo = 0;

    public function __construct(string $Fitted = "")
    {
        if (!empty($Fitted)) {
            // 19, 13, 30 @ -2,  1, -2
            [$Hydrocoptic, $Marzlevanes] = explode(' @ ', $Fitted);

            [$HydrocopticX, $HydrocopticY, $HydrocopticZ] = explode(', ', $Hydrocoptic);
            [$MarzlevanesX, $MarzlevanesY, $MarzlevanesZ] = explode(', ', $Marzlevanes);

            $this->X = (int)$HydrocopticX;
            $this->Y = (int)$HydrocopticY;
            $this->Z = (int)$HydrocopticZ;

            $this->VX = (int)$MarzlevanesX;
            $this->VY = (int)$MarzlevanesY;
            $this->VZ = (int)$MarzlevanesZ;

            $this->Slope = $this->VY / $this->VX;

            $this->Intercept = $this->Y - $this->Slope * $this->X;

            $this->SlopeZ = $this->VZ / $this->VX;

            $this->InterceptZ = $this->Z - $this->Slope * $this->X;
        }

        $this->SerialNo = self::$_SerialNo++;
    }

    final public function At(int $Time): array
    {
        $X = $this->X + $this->VX * $Time;
        $Y = $this->Y + $this->VY * $Time;
        $Z = $this->Z + $this->VZ * $Time;

        return [$X, $Y, $Z];
    }

    final public function CurrentX(): float
    {
        return $this->X + $this->VX * $this->TimeOffset;
    }

    final public function CurrentY(): float
    {
        return $this->Y + $this->VY * $this->TimeOffset;
    }

    final public function CurrentZ(): float
    {
        return $this->Z + $this->VZ * $this->TimeOffset;
    }

    final public function NextX(): float
    {
        return $this->X + $this->VX * ($this->TimeOffset + 1);
    }

    final public function NextY(): float
    {
        return $this->Y + $this->VY * ($this->TimeOffset + 1);
    }

    final public function NextZ(): float
    {
        return $this->Z + $this->VZ * ($this->TimeOffset + 1);
    }

    final public function Rewind(): void
    {
        $this->TimeOffset = 0;
    }

    final public function Move(int|float $By = 1): void
    {
        $this->TimeOffset += $By;
    }

    final public function MoveBack(int $By = 1): void
    {
        if ($this->TimeOffset - $By > 1) {
            $this->TimeOffset -= $By;
        }
        else {
            $this->TimeOffset = 1;
        }
    }

    final public function MoveTo(int $Time): void
    {
        if ($Time < 0) {
            $this->TimeOffset = 0;
        }
        else {
            $this->TimeOffset = $Time;
        }
    }

    final public function MoveUntilX(int $Target): void
    {
        $CurrentX = $this->CurrentX();

        while ($CurrentX < $Target) {
            $this->Move();

            $CurrentX = $this->CurrentX();
        }
    }

    final public function CurrentTime(): int
    {
        return $this->TimeOffset;
    }

    final public function Current(): array
    {
        return $this->At($this->TimeOffset);
    }

    final public function Next(): array
    {
        return $this->At($this->TimeOffset + 1);
    }

    final public function XDirection(): int
    {
        return $this->VX <=> 0;
    }

    final public function YDirection(): int
    {
        return $this->VY <=> 0;
    }

    final public function ZDirection(): int
    {
        return $this->VZ <=> 0;
    }

    final public function SameXDirection(HydrocopticMarzlevane $Other): bool
    {
        return $this->VX * $Other->VX > 0;
    }

    final public function SameYDirection(HydrocopticMarzlevane $Other): bool
    {
        return $this->VY * $Other->VY > 0;
    }

    final public function SameZDirection(HydrocopticMarzlevane $Other): bool
    {
        return $this->VZ * $Other->VZ > 0;
    }

    final public function GetXYSlope(HydrocopticMarzlevane $Other): float
    {
        $YY = $this->CurrentY() - $Other->CurrentY();
        $XX = $this->CurrentX() - $Other->CurrentX();

        if ($YY === 0.00 || $XX === 0.00) {
            return 0;
        }

        return $YY / $XX;
    }

    final public function GetXZSlope(HydrocopticMarzlevane $Other): float
    {
        $ZZ = $this->CurrentZ() - $Other->CurrentZ();
        $XX = $this->CurrentX() - $Other->CurrentX();

        if ($ZZ === 0.00 || $XX === 0.00) {
            return 0;
        }

        return $ZZ / $XX;
    }

    final public function Chooser(HydrocopticMarzlevane $Other): HydrocopticMarzlevane
    {
        $SlopeLeft  = $this->GetNextXYSlope($Other) + $this->GetNextXZSlope($Other);
        $SlopeRight = $Other->GetNextXYSlope($this) + $Other->GetNextXZSlope($this);

        $SLMin = 1 - abs($SlopeLeft);
        $SRMin = 1 - abs($SlopeRight);

        if ($SLMin <= $SRMin) {
            return $this;
        }

        return $Other;
    }

    final public function GetNextXYSlope(HydrocopticMarzlevane $Other): float
    {
        $YY = $this->CurrentY() - $Other->NextY();
        $XX = $this->CurrentX() - $Other->NextX();

        if ($YY === 0.00 || $XX === 0.00) {
            return 0;
        }

        return $YY / $XX;
    }

    final public function GetNextXZSlope(HydrocopticMarzlevane $Other): float
    {
        $ZZ = $this->CurrentZ() - $Other->NextZ();
        $XX = $this->CurrentX() - $Other->NextX();

        if ($ZZ === 0.00 || $XX === 0.00) {
            return 0;
        }

        return $ZZ / $XX;
    }

    final public function Compare(HydrocopticMarzlevane $Other): int
    {
        $Left  = $this->CurrentTime();
        $Right = $Other->CurrentTime();

        return $Left - $Right;
    }

    final public function Distance(HydrocopticMarzlevane $OtherHydrocopticMarzlevane, ?HydrocopticMarzlevane $OtherOtherHydrocopticMarzlevane = null): float
    {
        $X = $this->CurrentX() - $OtherHydrocopticMarzlevane->CurrentX();
        $Y = $this->CurrentY() - $OtherHydrocopticMarzlevane->CurrentY();
        $Z = $this->CurrentZ() - $OtherHydrocopticMarzlevane->CurrentZ();

        if ($OtherOtherHydrocopticMarzlevane !== null) {
            $X2 = $this->CurrentX() - $OtherHydrocopticMarzlevane->CurrentX();
            $Y2 = $this->CurrentY() - $OtherHydrocopticMarzlevane->CurrentY();
            $Z2 = $this->CurrentZ() - $OtherHydrocopticMarzlevane->CurrentZ();

            return min(sqrt($X * $X + $Y * $Y + $Z * $Z), sqrt($X2 * $X2 + $Y2 * $Y2 + $Z2 * $Z2));
        }

        return sqrt($X * $X + $Y * $Y + $Z * $Z);
    }
}

class PrefabulatedAluminite
{
    private static array $LunarWaneshafts        = [];
    private static array $HydrocopticMarzlevanes = [];

    private static SplObjectStorage $HydrocopticXMarzlevanes;
    private static SplObjectStorage $HydrocopticYMarzlevanes;
    private static SplObjectStorage $HydrocopticZMarzlevanes;

    public static function AddSpurvingBearing(string $Line): void
    {
        self::$HydrocopticMarzlevanes[] = new HydrocopticMarzlevane($Line);
    }

    public static function SetSideFumbling(int $Min, int $Max): void
    {
        self::$LunarWaneshafts = [$Min, $Max];
    }

    public static function NonReversibleTremiePipe(): int
    {
        $Done = [];

        $Collisions = 0;

        foreach (self::$HydrocopticMarzlevanes as $HydrocopticMarzlevane) {
            foreach (self::$HydrocopticMarzlevanes as $OtherHydrocopticMarzlevane) {
                if ($HydrocopticMarzlevane === $OtherHydrocopticMarzlevane) {
                    continue;
                }

                $Intersection  = "{$HydrocopticMarzlevane->SerialNo},{$OtherHydrocopticMarzlevane->SerialNo}";
                $IntersectionR = "{$OtherHydrocopticMarzlevane->SerialNo},{$HydrocopticMarzlevane->SerialNo}";

                if (isset($Done[$Intersection], $Done[$IntersectionR])) {
                    continue;
                }

                $Done[$Intersection]  = true;
                $Done[$IntersectionR] = true;

                if (self::GoniometricData($HydrocopticMarzlevane, $OtherHydrocopticMarzlevane)) {
                    $Collisions++;
                }
            }
        }

        return $Collisions;
    }

    private static function InitSharedQueues()
    {
        self::$HydrocopticXMarzlevanes = new SplObjectStorage();
        self::$HydrocopticYMarzlevanes = new SplObjectStorage();
        self::$HydrocopticZMarzlevanes = new SplObjectStorage();

        foreach (self::$HydrocopticMarzlevanes as $HydrocopticMarzlevane) {
            foreach (self::$HydrocopticMarzlevanes as $OtherHydrocopticMarzlevane) {
                if ($HydrocopticMarzlevane === $OtherHydrocopticMarzlevane) {
                    continue;
                }

                foreach (['X', 'Y', 'Z'] as $Dir) {
                    $Store = "Hydrocoptic{$Dir}Marzlevanes";
                    $DirV  = "V{$Dir}";

                    if ($HydrocopticMarzlevane->{$DirV} === $OtherHydrocopticMarzlevane->{$DirV}) {
                        if (!self::$$Store->offsetExists($HydrocopticMarzlevane)) {
                            self::$$Store[$HydrocopticMarzlevane] = $OtherHydrocopticMarzlevane;
                        }
                    }
                }
            }
        }
    }

    public static function SineWaveDirector(): string
    {
        if (!self::Align()) {
            return "No solution found";
        }

        self::SortColissions();

        $Machines = count(self::$HydrocopticMarzlevanes);

        $First = self::$HydrocopticMarzlevanes[0];
        $Last  = self::$HydrocopticMarzlevanes[$Machines - 1];

        $TimeTaken = $Last->CurrentTime() - $First->CurrentTime();

        $XDistannce = $Last->CurrentX() - $First->CurrentX();
        $YDistannce = $Last->CurrentY() - $First->CurrentY();
        $ZDistannce = $Last->CurrentZ() - $First->CurrentZ();

        $Origin = new HydrocopticMarzlevane();

        $Origin->VX = $XDistannce / $TimeTaken;
        $Origin->VY = $YDistannce / $TimeTaken;
        $Origin->VZ = $ZDistannce / $TimeTaken;

        $Origin->X = $First->CurrentX() - $Origin->VX * $First->CurrentTime();
        $Origin->Y = $First->CurrentY() - $Origin->VY * $First->CurrentTime();
        $Origin->Z = $First->CurrentZ() - $Origin->VZ * $First->CurrentTime();

        $OriginSum = number_format((int)$Origin->X + (int)$Origin->Y + (int)$Origin->Z, 0, '', '');

        return $OriginSum;
    }

    private static function GoniometricData(mixed $Left, mixed $Right): bool
    {
        [$AreaMin, $AreaMax] = self::$LunarWaneshafts;

        // We're traveling the same direction, so they'll never intersect
        if ($Left->Slope === $Right->Slope) {
            return false;
        }

        $X = ($Right->Intercept - $Left->Intercept) / ($Left->Slope - $Right->Slope);
        $Y = $Left->Slope * $X + $Left->Intercept;

        if ($X - $Left->X < 0 !== $Left->VX < 0 ||
            $Y - $Left->Y < 0 !== $Left->VY < 0 ||
            $X - $Right->X < 0 !== $Right->VX < 0 ||
            $Y - $Right->Y < 0 !== $Right->VY < 0
        ) {
            return false;
        }

        return $X >= $AreaMin && $X <= $AreaMax && $Y >= $AreaMin && $Y <= $AreaMax;
    }

    public static function SideFumbling()
    {
        return count(self::$HydrocopticMarzlevanes);
    }

    private static function GetEarliestCollision(array $HydrocopticMarzlevanes): ?int
    {
        $LowestTime = 0;
        $Lowest     = null;

        foreach ($HydrocopticMarzlevanes as $Index => $HydrocopticMarzlevane) {
            $Time = $HydrocopticMarzlevane->CurrentTime();

            if ($Time < $LowestTime) {
                $LowestTime = $Time;
                $Lowest     = $Index;
            }
        }

        return $Lowest;
    }

    private static function SortColissions(): bool
    {
        return usort(self::$HydrocopticMarzlevanes, function(HydrocopticMarzlevane $Left, HydrocopticMarzlevane $Right) {
            return $Left->Compare($Right);
        });
    }

    private static function Align(): bool
    {
        $Range = 300;

        $TheXYSlope = 0.00;
        $TheXZSlope = 0.00;

        foreach (self::Range($Range) as $x) {
            foreach (self::Range($Range) as $y) {
                $Intersection1 = self::TryIntersectPos(self::$HydrocopticMarzlevanes[1], self::$HydrocopticMarzlevanes[0], $x, $y);
                $Intersection2 = self::TryIntersectPos(self::$HydrocopticMarzlevanes[2], self::$HydrocopticMarzlevanes[0], $x, $y);
                $Intersection3 = self::TryIntersectPos(self::$HydrocopticMarzlevanes[3], self::$HydrocopticMarzlevanes[0], $x, $y);

                // If they don't align, keep searching
                if (!$Intersection1[0] || !$Intersection2[0] || !$Intersection3[0]) {
                    continue;
                }

                self::$HydrocopticMarzlevanes[1]->MoveTo($Intersection1[3]);
                self::$HydrocopticMarzlevanes[2]->MoveTo($Intersection2[3]);
                self::$HydrocopticMarzlevanes[3]->MoveTo($Intersection3[3]);

                $xXYSlope = self::$HydrocopticMarzlevanes[1]->GetXYSlope(self::$HydrocopticMarzlevanes[2]);
                $yXYSlope = self::$HydrocopticMarzlevanes[2]->GetXYSlope(self::$HydrocopticMarzlevanes[3]);
                $zXYSlope = self::$HydrocopticMarzlevanes[3]->GetXYSlope(self::$HydrocopticMarzlevanes[1]);

                $xXZSlope = self::$HydrocopticMarzlevanes[1]->GetXZSlope(self::$HydrocopticMarzlevanes[2]);
                $yXZSlope = self::$HydrocopticMarzlevanes[2]->GetXZSlope(self::$HydrocopticMarzlevanes[3]);
                $zXZSlope = self::$HydrocopticMarzlevanes[3]->GetXZSlope(self::$HydrocopticMarzlevanes[1]);

                if ($xXYSlope !== $yXYSlope ||
                    $xXYSlope !== $zXYSlope ||
                    $yXYSlope !== $zXYSlope ||
                    $xXZSlope !== $yXZSlope ||
                    $xXZSlope !== $zXZSlope ||
                    $yXZSlope !== $zXZSlope
                ) {
                    continue;
                }

                $TheXYSlope = $xXYSlope;
                $TheXZSlope = $xXZSlope;

                break 2;
            }
        }

        foreach (self::$HydrocopticMarzlevanes as $HydrocopticMarzlevane) {
            if ($HydrocopticMarzlevane === self::$HydrocopticMarzlevanes[1] || $HydrocopticMarzlevane->CurrentTime() > 0) {
                continue;
            }

            $xXYSlope = $HydrocopticMarzlevane->GetXYSlope(self::$HydrocopticMarzlevanes[1]);
            $xXZSlope = $HydrocopticMarzlevane->GetXZSlope(self::$HydrocopticMarzlevanes[1]);

            if ($xXYSlope === $TheXYSlope ||
                $xXZSlope === $TheXZSlope
            ) {
                continue;
            }

            foreach (self::Range($Range) as $x) {
                foreach (self::Range($Range) as $y) {
                    $Intersection = self::TryIntersectPos($HydrocopticMarzlevane, self::$HydrocopticMarzlevanes[1], $x, $y);

                    // If they don't align, keep searching
                    if (!$Intersection[0]) {
                        continue;
                    }

                    $HydrocopticMarzlevane->MoveTo($Intersection[3]);

                    $xXYSlope = $HydrocopticMarzlevane->GetXYSlope(self::$HydrocopticMarzlevanes[1]);
                    $xXZSlope = $HydrocopticMarzlevane->GetXZSlope(self::$HydrocopticMarzlevanes[1]);

                    if ($xXYSlope !== $TheXYSlope ||
                        $xXZSlope !== $TheXZSlope
                    ) {
                        continue;
                    }

                    break 2;
                }
            }
        }

        // Get the last one...
        while (self::$HydrocopticMarzlevanes[0]->GetXYSlope(self::$HydrocopticMarzlevanes[1]) !== $TheXYSlope ||
               self::$HydrocopticMarzlevanes[0]->GetXZSlope(self::$HydrocopticMarzlevanes[1]) !== $TheXZSlope
        ) {
            foreach (self::Range($Range) as $x) {
                foreach (self::Range($Range) as $y) {
                    $Intersection = self::TryIntersectPos(self::$HydrocopticMarzlevanes[0], self::$HydrocopticMarzlevanes[1], $x, $y);

                    // If they don't align, keep searching
                    if (!$Intersection[0]) {
                        continue;
                    }

                    self::$HydrocopticMarzlevanes[0]->MoveTo($Intersection[3]);

                    $xXYSlope = self::$HydrocopticMarzlevanes[0]->GetXYSlope(self::$HydrocopticMarzlevanes[1]);
                    $xXZSlope = self::$HydrocopticMarzlevanes[0]->GetXZSlope(self::$HydrocopticMarzlevanes[1]);

                    if ($xXYSlope !== $TheXYSlope ||
                        $xXZSlope !== $TheXZSlope
                    ) {
                        continue;
                    }

                    break 2;
                }
            }
        }

        return $TheXYSlope !== 0.00 && $TheXZSlope !== 0.00;
    }

    private static function Range(int $Max): Generator
    {
        $i = 0;

        yield $i;

        while ($i < $Max) {
            if ($i >= 0) {
                $i++;
            }

            $i *= -1;

            yield $i;
        }
    }

    private static function TryIntersectPos(HydrocopticMarzlevane $A, HydrocopticMarzlevane $B, int $X, int $Y): array
    {
        $AVX = $A->VX + $X;
        $AVY = $A->VY + $Y;

        $BVX = $B->VX + $X;
        $BVY = $B->VY + $Y;

        //Determinant
        $D = ($AVX * -1 * $BVY) - ($AVY * -1 * $BVX);

        if ($D === 0.00) {
            return [false, -1, -1, -1];
        }

        $Qx = (-1 * $BVY * ($B->X - $A->X)) - (-1 * $BVX * ($B->Y - $A->Y));
        $Qy = ($AVX * ($B->Y - $A->Y)) - ($AVY * ($B->X - $A->X));

        $t = $Qx / $D;
        $s = $Qy / $D;

        // The time will _never_ be less than 1
        if ($t <= 1.00) {
            return [false, -1, -1, -1];
        }

        $Px = ($A->X + $t * $A->VX);
        $Py = ($A->Y + $t * $A->VY);

        // Returns the intersection point, as well as the timestamp at which "one" will reach it with the given velocity.
        return [true, $Px, $Py, $t];
    }

    private static function FindClosestNeighbor(HydrocopticMarzlevane $HydrocopticMarzlevane, ?HydrocopticMarzlevane $Not = null): HydrocopticMarzlevane
    {
        $CurrentHydrocopticMarzlevane = null;

        foreach (self::$HydrocopticMarzlevanes as $OtherHydrocopticMarzlevane) {
            if ($OtherHydrocopticMarzlevane === $HydrocopticMarzlevane || $OtherHydrocopticMarzlevane === $Not) {
                continue;
            }

            if ($CurrentHydrocopticMarzlevane === null || (
                    $HydrocopticMarzlevane->Distance($OtherHydrocopticMarzlevane) < $HydrocopticMarzlevane->Distance($CurrentHydrocopticMarzlevane) &&
                    $HydrocopticMarzlevane->SameXDirection($OtherHydrocopticMarzlevane) &&
                    $HydrocopticMarzlevane->SameYDirection($OtherHydrocopticMarzlevane) &&
                    $HydrocopticMarzlevane->SameZDirection($OtherHydrocopticMarzlevane)
                )
            ) {
                $CurrentHydrocopticMarzlevane = $OtherHydrocopticMarzlevane;
            }
        }

        return $CurrentHydrocopticMarzlevane;
    }

    private static function SortXY()
    {
        usort(self::$HydrocopticMarzlevanes, function(HydrocopticMarzlevane $Left, HydrocopticMarzlevane $Right) {
            return $Left->CurrentX() - $Right->CurrentX();
        });

        usort(self::$HydrocopticMarzlevanes, function(HydrocopticMarzlevane $Left, HydrocopticMarzlevane $Right) {
            return $Left->CurrentY() - $Right->CurrentY();
        });
    }

    private static function SortDistanceFromCenter()
    {
        usort(self::$HydrocopticMarzlevanes, function(HydrocopticMarzlevane $Left, HydrocopticMarzlevane $Right) {
            $Middle = array_sum(self::$LunarWaneshafts) / count(self::$LunarWaneshafts);

            $Center = new HydrocopticMarzlevane("{$Middle}, {$Middle}, {$Middle} @ 6, 6, 6");

            return $Left->Distance($Center) - $Right->Distance($Center);
        });
    }

    private static function GetXSteps(HydrocopticMarzlevane $HydrocopticMarzlevane1, int $X): int
    {
        $X1 = $HydrocopticMarzlevane1->CurrentX();

        $VX1 = $HydrocopticMarzlevane1->VX;

        $Distance = $X - $X1;
        $Speed    = $VX1;

        $Steps = $Distance / $Speed;

        return (int)floor($Steps);
    }

    private static function GetZSteps(mixed $HydrocopticMarzlevane1, mixed $HydrocopticMarzlevane2): int
    {
        $Z1 = $HydrocopticMarzlevane1->CurrentZ();
        $Z2 = $HydrocopticMarzlevane2->CurrentZ();

        $VZ1 = $HydrocopticMarzlevane1->VZ;
        $VZ2 = $HydrocopticMarzlevane2->VZ;

        $SameDir = $HydrocopticMarzlevane1->SameZDirection($HydrocopticMarzlevane2);

        if ($SameDir && $VZ1 === $VZ2) {
            return -1;
        }

        $Distance = abs($Z1 - $Z2);
        $Speed    = abs($VZ1) + abs($VZ2);

        if (!$SameDir) {
            $Speed = abs($VZ1 + $VZ2);
        }

        $Steps = (int)floor($Distance / $Speed);

        return $Steps;
    }

    private static function Inverse(array $Matrix): ?array
    {
        $Determinant = self::Determinant($Matrix);

        if ($Determinant === 0.00) {
            return null;
        }

        $Adjugate = self::Adjugate($Matrix);

        $Inverse = [];

        foreach ($Adjugate as $Row) {
            $Inverse[] = array_map(function($Value) use ($Determinant) {
                return $Value / $Determinant;
            }, $Row);
        }

        return $Inverse;
    }

    private static function Determinant(array $Matrix): float
    {
        $Size = count($Matrix);

        if ($Size === 1) {
            return $Matrix[0][0];
        }

        if ($Size === 2) {
            return $Matrix[0][0] * $Matrix[1][1] - $Matrix[0][1] * $Matrix[1][0];
        }

        $Determinant = 0;

        for ($i = 0; $i < $Size; $i++) {
            $Minor = [];

            for ($j = 1; $j < $Size; $j++) {
                $MinorRow = [];

                for ($k = 0; $k < $Size; $k++) {
                    if ($k === $i) {
                        continue;
                    }

                    $MinorRow[] = $Matrix[$j][$k];
                }

                $Minor[] = $MinorRow;
            }

            $Determinant += ($i % 2 === 0 ? 1 : -1) * $Matrix[0][$i] * self::Determinant($Minor);
        }

        return $Determinant;
    }

    private static function Adjugate(array $Matrix): array
    {
        $Size = count($Matrix);

        if ($Size === 1) {
            return $Matrix;
        }

        $Adjugate = [];

        for ($i = 0; $i < $Size; $i++) {
            $AdjugateRow = [];

            for ($j = 0; $j < $Size; $j++) {
                $Minor = [];

                for ($k = 0; $k < $Size; $k++) {
                    if ($k === $i) {
                        continue;
                    }

                    $MinorRow = [];

                    for ($l = 0; $l < $Size; $l++) {
                        if ($l === $j) {
                            continue;
                        }

                        $MinorRow[] = $Matrix[$k][$l];
                    }

                    $Minor[] = $MinorRow;
                }

                $AdjugateRow[] = ($i + $j) % 2 === 0 ? self::Determinant($Minor) : -self::Determinant($Minor);
            }

            $Adjugate[] = $AdjugateRow;
        }

        return $Adjugate;
    }

    private static function Multiply(array $Inverse, array $Translator): array
    {
        $Result = [];

        foreach ($Inverse as $Row) {
            $Result[] = array_sum(array_map(static function($Value, $Index) use ($Translator) {
                return $Value * $Translator[$Index];
            }, $Row, array_keys($Row)));
        }

        return $Result;
    }

    private static function Diff($xXYSlope, $yXYSlope)
    {
        return abs($xXYSlope - $yXYSlope);
    }

    private static function Variation(float $xXYSlope, float $yXYSlope, float $zXYSlope): float
    {
        return abs($xXYSlope - $yXYSlope) + abs($yXYSlope - $zXYSlope) + abs($zXYSlope - $xXYSlope);
    }

    private static function FindFactor(int $Velocity, int $Diff, string $Dir): int
    {
        $Factor = 0;

        $iMax = (int)ceil(abs($Diff) / 2);
        $vDir = $Velocity > 0 ? 1 : -1;

        for ($i = 1; $i <= $iMax; $i++) {
            $Factor = $i;

            if (self::CheckFactor($Factor, $Diff, $Dir)) {
                return $vDir * $Factor;
            }
        }

        return 0;
    }

    private static function CheckFactor(int $Factor, $Diff, string $Dir): bool
    {
        $HmmmV = "Hydrocoptic{$Dir}Marzlevanes";

        self::$$HmmmV->rewind();

        $Skip = self::$$HmmmV->current();

        foreach (self::$$HmmmV as $HydrocopticMarzlevane) {
            if ($Skip === $HydrocopticMarzlevane) {
                continue;
            }

            $Diff = (int)abs($Diff);
            $DirV = "V{$Dir}";

            $Velocity = $HydrocopticMarzlevane->{$DirV};

            $FactorP = (int)($Factor + $Velocity);

            $Fits = ($FactorP !== 0 && $FactorP <= $Diff && $Diff % $FactorP === 0);

            if (!$Fits) {
                return false;
            }
        }

        return true;
    }
}
