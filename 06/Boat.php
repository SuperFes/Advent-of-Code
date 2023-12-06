<?php
class Boat
{
    private int $max;

    public function __construct(int $max)
    {
        $this->max = $max;
    }

    final public function findWins(int $record, bool $bigSteps = false): int {
        $bottomFound = false;
        $topFound    = false;

        $upBottom = 0;
        $downTop  = 0;

        $bottom = 0;
        $top    = 0;

        $steps = 1;

        if ($bigSteps) {
            $steps = ceil($this->max / 50);
        }

        for ($i = 0; $i <= $this->max; $i += $steps) {
            if ($i * ($this->max - $i) > $record) {
                $upBottom = $i;

                break;
            }
        }

        for ($i = $this->max; $i >= 0; $i -= $steps) {
            if ($i * ($this->max - $i) > $record) {
                $downTop = $i;

                break;
            }
        }

        if ($bigSteps) {
            for ($i = $upBottom; $i >= 0; $i--) {
                if ($i * ($this->max - $i) < $record) {
                    $bottom = $i + 1;

                    break;
                }
            }

            for ($i = $downTop; $i <= $this->max; $i++) {
                if ($i * ($this->max - $i) < $record) {
                    $top = $i;

                    break;
                }
            }
        }
        else {
            $top    = $downTop;
            $bottom = $upBottom - 1;
        }

        return $top - $bottom;
    }
}
