<?php
namespace Almanac;

use ArrayAccess;
use Countable;

class Map
{
    private array $Dest = [];
    private array $Source = [];
    private array $Length = [];

    final public function exists(mixed $offset): bool
    {
        $OS = (int)$offset;

        $Num = count($this->Dest);

        for ($l = 0; $l < $Num; $l++) {
            if ($OS >= $this->Source[$l] && $OS < $this->Source[$l] + $this->Length[$l]) {
                return true;
            }
        }

        return false;
    }

    final public function get(mixed $offset): int
    {
        $OS = (int)$offset;

        if ($this->exists($offset)) {
            $Num = count($this->Dest);

            for ($l = 0; $l < $Num; $l++) {
                if ($OS >= $this->Source[$l] && $OS < $this->Source[$l] + $this->Length[$l]) {
                    return $this->Dest[$l] + ($OS - $this->Source[$l]);
                }
            }
        }

        return $OS;
    }

    final public function add(string $Dest, string $Source, string $Length): void
    {
        $this->Dest[]   = (int)$Dest;
        $this->Source[] = (int)$Source;
        $this->Length[] = (int)$Length;
    }

    final public function getLowest(): array
    {
        $Lowest = false;

        foreach ($this->Source as $l => $lValue) {
            if ($Lowest === false || $lValue < $this->Source[$Lowest]) {
                $Lowest = $l;
            }
        }

        return [[$this->Source[$Lowest], $this->Source[$Lowest] + $this->Length[$Lowest]]];
    }

    private function inRange($x1, $x2, $y1, $y2): bool {
        // Check if any point of one pair overlaps with the other's range, including endpoints
        return ($x1 >= $x2 && $x1 <= $y2) || ($y1 >= $x2 && $y1 <= $y2)
               || ($x2 >= $x1 && $x2 <= $y1) || ($y2 >= $x1 && $y2 <= $y1);
    }

    final public function getRanges(array $Sources): array
    {
        $Ranges = [];
        $Added  = [];

        $Num  = count($this->Source);

        for ($l = 0; $l < $Num; $l++) {
            foreach ($Sources as $mValue) {
                [$Low, $High] = $mValue;

                $Source = $this->Source[$l];
                $Dest   = $this->Dest[$l];
                $Length = $this->Length[$l];
                $Top    = $this->Dest[$l] + $Length;

                if (!isset($Added[$Source]) && ($this->inRange($Low, $High, $Dest, $Top))) {
                    $Added[$Source] = true;
                    $Ranges[]       = [$Source, $Source + $Length];
                }
            }
        }

        return $Ranges;
    }
}

class Maps implements ArrayAccess, Countable
{
    static private array $_maps = [];

    final public function offsetExists(mixed $offset): bool
    {
        return isset(self::$_maps[$offset]);
    }

    final public function offsetGet(mixed $offset): ?Map
    {
        return self::$_maps[$offset] ?? null;
    }

    final public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_a($value, Map::class)) {
            self::$_maps[$offset] = $value;

            return;
        }

        [$Dest, $Source, $Length] = explode(' ', $value);

        self::$_maps[$offset]->add($Dest, $Source, $Length);
    }

    final public function offsetUnset(mixed $offset): void
    {
        unset(self::$_maps[$offset]);
    }

    final public function count(): int
    {
        return count(self::$_maps);
    }
}
