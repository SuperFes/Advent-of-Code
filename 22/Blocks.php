<?php
namespace Tetris;

use ArrayAccess;
use SplObjectStorage;

class Vector
{
    public int $X = 0;
    public int $Y = 0;
    public int $Z = 0;

    public function __construct(int $X, int $Y, int $Z)
    {
        $this->X = $X;
        $this->Y = $Y;
        $this->Z = $Z;
    }
}

class Brick
{
    public Vector $V1;
    public Vector $V2;

    private int   $ZSave1;
    private int   $ZSave2;

    public function __construct(string $Victor)
    {
        [$Victor1, $Victor2] = explode('~', $Victor, 2);

        $Vector1 = explode(',', $Victor1, 3);
        $Vector2 = explode(',', $Victor2, 3);

        $this->V1 = new Vector((int)$Vector1[0], (int)$Vector1[1], (int)$Vector1[2]);
        $this->V2 = new Vector((int)$Vector2[0], (int)$Vector2[1], (int)$Vector2[2]);

        for ($z = $this->V1->Z; $z <= $this->V2->Z; $z++) {
            for ($y = $this->V1->Y; $y <= $this->V2->Y; $y++) {
                for ($x = $this->V1->X; $x <= $this->V2->X; $x++) {
                    Blocks::SetBlock($x, $y, $z, Blocks::Brick);
                }
            }
        }

        $this->SavePosition();
    }

    final public function Fall(): bool
    {
        if (!$this->CanFall()) {
            return false;
        }

        for ($z = $this->V1->Z; $z <= $this->V2->Z; $z++) {
            for ($y = $this->V1->Y; $y <= $this->V2->Y; $y++) {
                for ($x = $this->V1->X; $x <= $this->V2->X; $x++) {
                    Blocks::SetBlock($x, $y, $z, Blocks::Air);
                }
            }
        }

        $this->V1->Z--;
        $this->V2->Z--;

        for ($z = $this->V1->Z; $z <= $this->V2->Z; $z++) {
            for ($y = $this->V1->Y; $y <= $this->V2->Y; $y++) {
                for ($x = $this->V1->X; $x <= $this->V2->X; $x++) {
                    Blocks::SetBlock($x, $y, $z, Blocks::Brick);
                }
            }
        }

        return true;
    }

    final public function CanFall(): bool
    {
        $z = min($this->V1->Z, $this->V2->Z) - 1;

        for ($y = $this->V1->Y; $y <= $this->V2->Y; $y++) {
            for ($x = $this->V1->X; $x <= $this->V2->X; $x++) {
                if (Blocks::GetBlock($x, $y, $z) !== Blocks::Air) {
                    return false;
                }
            }
        }

        return true;
    }

    final public function CountNumberOfSupports(): int
    {
        $Supporters = [];

        $z = min($this->V1->Z, $this->V2->Z) - 1;

        for ($y = $this->V1->Y; $y <= $this->V2->Y; $y++) {
            for ($x = $this->V1->X; $x <= $this->V2->X; $x++) {
                if (Blocks::GetBlock($x, $y, $z) === Blocks::Brick) {
                    $Supporter = Bricks::FindBrickAt($x, $y, $z);

                    if ($Supporter !== null && !in_array($Supporter, $Supporters, true)) {
                        $Supporters[] = $Supporter;
                    }
                }
            }
        }

        return count($Supporters);
    }

    final public function TurnInto(int $Block): void
    {
        for ($z = $this->V1->Z; $z <= $this->V2->Z; $z++) {
            for ($y = $this->V1->Y; $y <= $this->V2->Y; $y++) {
                for ($x = $this->V1->X; $x <= $this->V2->X; $x++) {
                    Blocks::SetBlock($x, $y, $z, $Block);
                }
            }
        }
    }

    final public function Reset(): void
    {
        $this->RestorePosition();

        $this->TurnInto(Blocks::Brick);
    }

    final public function SavePosition(): void
    {
        $this->ZSave1 = $this->V1->Z;
        $this->ZSave2 = $this->V2->Z;
    }

    final public function RestorePosition(): void
    {
        $this->V1->Z = $this->ZSave1;
        $this->V2->Z = $this->ZSave2;
    }
}

class Bricks
{
    private static array $_Bricks = [];

    public static function AddBrick(string $Victor): void
    {
        $Brick = new Brick($Victor);

        self::$_Bricks[] = $Brick;
    }

    public static function FindBrickAt(int $x, int $y, int $z): ?Brick
    {
        foreach (self::$_Bricks as $Brick) {
            if ($Brick->V1->X <= $x && $Brick->V2->X >= $x && $Brick->V1->Y <= $y && $Brick->V2->Y >= $y && $Brick->V1->Z <= $z && $Brick->V2->Z >= $z) {
                return $Brick;
            }
        }

        return null;
    }

    private static function FallAllBricks(): int
    {
        $DoneFalling = false;

        $BricksFell = new SplObjectStorage();

        while (!$DoneFalling) {
            $Fell = false;

            foreach (self::$_Bricks as $Brick) {
                if ($Brick->CanFall()) {
                    $Fell = true;

                    if (!$BricksFell->contains($Brick)) {
                        $BricksFell[$Brick] = 1;
                    }

                    $Brick->Fall();
                }
            }

            if (!$Fell) {
                $DoneFalling = true;
            }
        }

        return $BricksFell->count();
    }

    public static function FindRemovableBricks(): int
    {
        $RemovableBricks = 0;

        self::FallAllBricks();

        foreach (self::$_Bricks as $Brick) {
            $Brick->TurnInto(Blocks::Air);

            $CanFall = false;

            foreach (self::$_Bricks as $SubBrick) {
                if ($Brick === $SubBrick) {
                    continue;
                }

                if ($SubBrick->CanFall()) {
                    $CanFall = true;
                }
            }

            $Brick->TurnInto(Blocks::Brick);

            if (!$CanFall) {
                $RemovableBricks++;
            }
        }

        return $RemovableBricks;
    }

    public static function FindLargestCascade(): int
    {
        $LargestCascade = 0;

        self::FallAllBricks();

        foreach (self::$_Bricks as $Brick) {
            $Brick->SavePosition();
        }

        foreach (self::$_Bricks as $Brick) {
            $Brick->TurnInto(Blocks::Air);

            $LargestCascade += self::FallAllBricks();

            self::ResetPositions();
        }

        foreach (self::$_Bricks as $Brick) {
            $Brick->TurnInto(Blocks::Brick);
        }

        return $LargestCascade;
    }

    private static function ResetPositions(): void
    {
        Blocks::ResetGrid();

        foreach (self::$_Bricks as $Brick) {
            $Brick->Reset();
        }
    }
}

class Blocks
{
    public const Ground = 666;
    public const Air    = 0;
    public const Brick  = 1;

    private static array $_Grid = [];

    public static function SetBlock(int $x, int $y, int $z, int $Brick): void
    {
        self::$_Grid[$z][$y][$x] = $Brick;
    }

    public static function GetBlock(int $x, int $y, int $z): int
    {
        if ($z === 0) {
            return self::Ground;
        }

        return self::$_Grid[$z][$y][$x] ?? self::Air;
    }

    public static function ResetGrid()
    {
        self::$_Grid = [];
    }
}
