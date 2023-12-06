<?php

class Boat
{
    private int $max;

    public function __construct(int $max)
    {
        $this->max = $max;
    }

    final public function findWins(int $record, bool $bigSteps = false): int
    {
        $bottom = 0;
        $top    = 0;

        $steps = 1;

        if ($bigSteps) {
            $steps = ceil($this->max / 50);
        }

        for ($i = 0; $i <= $this->max; $i += $steps) {
            if ($i * ($this->max - $i) > $record) {
                $bottom = $i;

                break;
            }
        }

        if ($bigSteps) {
            for ($i = $bottom; $i >= 0; $i--) {
                if ($i * ($this->max - $i) < $record) {
                    $bottom = $i + 1;

                    $top = $this->max - $i;

                    break;
                }
            }
        }
        else {
            $top = $this->max - $bottom + 1;
        }

        return $top - $bottom;
    }
}
