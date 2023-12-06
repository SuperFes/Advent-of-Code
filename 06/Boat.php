<?php
class Boat
{
    private int $max;

    public function __construct(int $max)
    {
        $this->max = $max;
    }

    final public function findWins(int $record): int {
        $canWin = 0;

        $ms = $this->max;

        $bottom = 0;
        $top    = 0;

        for ($i = 0; $i <= $this->max; $i++) {
            if ($i * ($ms - $i) > $record) {
                $canWin++;

                $bottom = $i;

                break;
            }
        }

        for ($i = $this->max; $i >= 0; $i--) {
            if ($i * ($ms - $i) > $record) {
                $top = $i;

                break;
            }
        }

        $canWin += $top - $bottom;

        return $canWin;
    }
}
