<?php

class Spring
{
    private array  $Springs            = [];
    private array  $Places             = [];
    private array  $BigSprings         = [];
    private array  $BigSpringsMask     = [];
    private array  $BigSpringsRemain   = [];
    private array  $BigPlaces          = [];
    private string $Mask;
    private string $BigMask;
    private array  $BigSegments        = [];
    private int    $Wiggles            = 0;
    private int    $BigWiggles         = 0;
    private int    $BigCount           = 0;
    private int    $BigSpringMinLength = 0;

    public function __construct(string $Row)
    {
        [$Mask, $Data] = explode(' ', $Row);

        $Springs = explode(',', $Data);

        foreach ($Springs as $Spring) {
            $this->Springs[] = (int)$Spring;
        }

        for ($i = 0; $i < 5; $i++) {
            foreach ($Springs as $Spring) {
                $this->BigSpringMinLength += (int)$Spring;

                $this->BigSprings[]     = (int)$Spring;
                $this->BigSpringsMask[] = str_pad('', (int)$Spring, '#');
            }
        }

        $BigSprings = $this->BigSprings;

        $S = 0;

        while (!empty($BigSprings)) {
            $this->BigSpringsRemain[$S++] = array_sum($BigSprings) + count($BigSprings) - 1;

            array_shift($BigSprings);
        }

        $this->BigSpringsRemain[$S] = 0;

        $this->BigSpringMinLength += count($this->BigSprings) - 2;

        $this->Mask    = $Mask;
        $this->BigMask = "{$Mask}?{$Mask}?{$Mask}?{$Mask}?{$Mask}";
    }

    private function Permutations(array $Segments, int $StartAt = 0, array $Paths = []): Generator
    {
        if (empty($Segments)) {
            yield $Paths;
        }
        else {
            $Segment = array_shift($Segments);

            foreach ($Segment as [$Start, $End]) {
                $Fore  = ($Start > 0 ? $this->Mask[$Start - 1] : null);
                $After = $this->Mask[$End] ?? null;

                if ($Fore === '#' || $After === '#') {
                    continue;
                }

                if (($Start === 0 && $StartAt === 0) || $Start > $StartAt) {
                    $TempPaths   = $Paths;
                    $TempPaths[] = [$Start, $End];
                    $NextStartAt = $End;

                    yield from $this->Permutations($Segments, $NextStartAt, $TempPaths);
                }
            }

            array_unshift($Segments, $Segment);
        }
    }

    private function HUNTSurprise(): array
    {
        $Permutations = [];

        $Depth = -1;

        while (count($this->Places) > 0) {
            $Place = array_shift($this->Places);

            if (empty($Permutations)) {
                foreach ($Place as [$Start, $End]) {
                    $Permutations[] = [[$Start, $End]];
                }
            }
            else {
                foreach ($Permutations as $Permutation) {
                    foreach ($Place as [$Start, $End]) {
                        $NewPermutation = $Permutation;

                        $Last = array_key_last($Permutation);

                        if ($Start > $Permutation[$Last][0]) {
                            $NewPermutation[] = [$Start, $End];

                            $Permutations[] = $NewPermutation;
                        }

                        unset($NewPermutation);
                    }
                }
            }
        }

        $Count   = count($Permutations);
        $Springs = count($this->Springs);

        for ($i = 0; $i < $Count; $i++) {
            $Unset = false;

            if (count($Permutations[$i]) !== $Springs) {
                $Unset = true;
            }
            else {
                $TempHash = $this->Mask;

                $Len = strlen($TempHash);

                $Permutation = $Permutations[$i];
                foreach ($Permutation as [$Start, $End]) {
                    for ($p = $Start; $p < $End; $p++) {
                        if ($TempHash[$p] !== '.') {
                            $TempHash[$p] = '@';
                        }
                    }

                    if ($Start > 0) {
                        if ($TempHash[$Start - 1] === '@') {
                            $Unset = true;
                        }
                    }

                    if ($End < $Len) {
                        if ($TempHash[$End] === '@') {
                            $Unset = true;
                        }
                    }
                }

                $HashCount = preg_match_all('/#/', $TempHash);

                if ($HashCount) {
                    $Unset = true;
                }
            }

            if ($Unset) {
                unset($Permutations[$i]);
            }
        }

        sort($Permutations);

        return $Permutations;
    }

    private function CalculateMask(): void
    {
        foreach ($this->Springs as $Spring) {
            $Positions = [];

            $RowLength = strlen($this->Mask);

            for ($i = 0; $i <= $RowLength - $Spring; $i++) {
                $Fits = true;

                for ($j = 0; $j < $Spring; $j++) {
                    if ($this->Mask[$i + $j] === '.') {
                        $Fits = false;
                    }
                }

                if ($Fits) {
                    $Fore  = ($i > 0 ? $this->Mask[$i - 1] : null);
                    $After = $this->Mask[$i + $Spring] ?? null;

                    if ($Fore === '#' || $After === '#') {
                        $Fits = false;
                    }
                }

                if ($Fits) {
                    $Positions[] = [$i, $i + $Spring];
                }
            }

            if (count($Positions)) {
                $this->Places[] = $Positions;
            }
        }
    }

    final public function GetWiggles(): int
    {
        if ($this->Wiggles > 0) {
            return $this->Wiggles;
        }

        $this->CalculateMask();

        $Possibilities = $this->HUNTSurprise();

        foreach ($Possibilities as $possibility) {
            $this->Wiggles++;
        }

        return $this->Wiggles;
    }

    private function BigHUNTSurprise(): int
    {
        $BigHUNT = 0;

        $Permutations = [];

        $WorkingPermutations = [];

        $NumSprings = count($this->BigSprings);

        $LastLength = 0;

        $MaskLen = strlen($this->BigMask);

        $this->CalculateBigMask();

        print "Working on big mask for: {$this->BigMask}\n";

        while (!empty($this->BigPlaces)) {
            $Mask  = array_key_first($this->BigPlaces);
            $Place = array_shift($this->BigPlaces);

            if (empty($Permutations)) {
                foreach ($Place as [$Start, $End]) {
                    $Permutations[] = [$Start];
                }
            }
            else {
                foreach ($Permutations as $Permutation) {
                    if (count($Permutation) !== $LastLength) {
                        continue;
                    }

                    foreach ($Place as [$Start, $End]) {
                        $NewPermutation = $Permutation;

                        $Last = array_key_last($Permutation);

                        $LastEnd = $Permutation[$Last] + $this->BigSprings[$LastLength];

                        if ($Start > $LastEnd) {
                            $NewPermutation[] = $Start;

                            $WorkingPermutations[$Mask] = $NewPermutation;
                        }
                    }
                }
            }

            $LastLength++;
        }

        unset($Permutations);

        $Count = count($WorkingPermutations);

        $this->BigCount += $Count;

        print "Calculating $Count ({$this->BigCount}) permutations... ";

        $SolvedPermutations = [];

        foreach ($WorkingPermutations as $Mask => $Permutation) {
            $Unset = false;

            $TempHash = $Mask;

            if (isset($SolvedPermutations[$Mask])) {
                $HashCount = $SolvedPermutations[$Mask];
            }
            else {
                $Len = strlen($TempHash);

                foreach ($Permutation as $Key => $Start) {
                    $End = $Start + $this->BigSprings[$Key];

                    for ($p = $Start; $p < $End; $p++) {
                        if ($TempHash[$p] !== '.') {
                            $TempHash[$p] = '@';
                        }
                    }

                    if ($Start > 0) {
                        if ($TempHash[$Start - 1] === '@') {
                            $Unset = true;
                        }
                    }

                    if ($End < $Len) {
                        if ($TempHash[$End] === '@') {
                            $Unset = true;
                        }
                    }
                }

                $HashCount = preg_match_all('/#/', $TempHash);
            }

            if ($HashCount) {
                $Unset = true;

                $SolvedPermutations[$Mask] = $HashCount;
            }

            if (!$Unset) {
                $BigHUNT++;
            }

            unset($WorkingPermutations[$Mask]);
        }

        print " $BigHUNT, done!\n";

        return $BigHUNT;
    }

    private function CalculateBigMask(): void
    {
        $this->BigSegments = explode('.', $this->BigMask);

        foreach ($this->BigSegments as $Mask) {
            foreach ($this->BigSprings as $N => $Spring) {
                $Positions = [];

                $RowLength = strlen($Mask);

                $Start = 0;
                $End   = $RowLength;

                for ($i = $Start; $i <= $End - $Spring; $i++) {
                    $Fits = true;

                    if ($i + $Spring > $End) {
                        $Fits = false;
                    }

                    for ($j = 0; $j < $Spring; $j++) {
                        if ($Mask[$i + $j] === '.') {
                            $Fits = false;
                        }
                    }

                    if ($Fits) {
                        $Fore  = $Mask[$i - 1] ?? null;
                        $After = $Mask[$i + $Spring] ?? null;

                        if ($Fore === '#' || $After === '#') {
                            $Fits = false;
                        }
                    }

                    if ($Fits) {
                        $Positions[] = [$i, $i + $Spring];
                    }
                }

                if (count($Positions)) {
                    $this->BigPlaces[$Mask] = $Positions;
                }
            }
        }
    }

    final public function GetBigWiggles(): int
    {
        $Wiggles = 0;

        $States     = ['0:0:0:0=1' => [[0, 0, 0, 0], 1]];

        $MaskEnd    = strlen($this->BigMask);
        $NumSprings = count($this->BigSprings);

        while (!empty($States)) {
            $NextStates = [];

            foreach ($States as $CurIndex => [$State, $Num]) {
                [$MaskIndex, $SpringIndex, $SpringLength, $ExpDot] = $State;

                if ($MaskIndex === $MaskEnd) {
                    if ($SpringIndex === $NumSprings) {
                        $Wiggles += $Num;
                    }

                    continue;
                }

                if ($ExpDot === 0 && ($this->BigMask[$MaskIndex] === '#' || $this->BigMask[$MaskIndex] === '?') && $SpringIndex < $NumSprings) {
                    if ($this->BigMask[$MaskIndex] === '?' && $SpringLength === 0) {
                        $Index = $this->CreateIndex($MaskIndex + 1, $SpringIndex, $SpringLength, $ExpDot, $Num);

                        if (isset($NextStates[$Index])) {
                            $NextStates[$Index][1] += $Num;
                        }
                        else {
                            $NextStates[$Index] = [[$MaskIndex + 1, $SpringIndex, $SpringLength, $ExpDot], $Num];
                        }
                    }

                    $SpringLength++;

                    if ($SpringLength === $this->BigSprings[$SpringIndex]) {
                        $SpringIndex++;

                        $SpringLength = 0;

                        $ExpDot = 1;
                    }

                    $Index = $this->CreateIndex($MaskIndex + 1, $SpringIndex, $SpringLength, $ExpDot, $Num);

                    if (isset($NextStates[$Index])) {
                        $NextStates[$Index][1] += $Num;
                    }
                    else {
                        $NextStates[$Index] = [[$MaskIndex + 1, $SpringIndex, $SpringLength, $ExpDot], $Num];
                    }
                }
                else if ($SpringLength === 0 && ($this->BigMask[$MaskIndex] === '.' || $this->BigMask[$MaskIndex] === '?')) {
                    $ExpDot = 0;

                    $Index = $this->CreateIndex($MaskIndex + 1, $SpringIndex, $SpringLength, $ExpDot, $Num);

                    if (isset($NextStates[$Index])) {
                        $NextStates[$Index][1] += $Num;
                    }
                    else {
                        $NextStates[$Index] = [[$MaskIndex + 1, $SpringIndex, $SpringLength, $ExpDot], $Num];
                    }
                }
            }

            $States     = $NextStates;
        }

        unset($States, $NextStates);

        return $Wiggles;
    }

    private function CreateIndex(mixed $MaskIndex, mixed $SpringIndex, int $SpringLength, int $ExpDot, mixed $Num): string
    {
        return "{$MaskIndex}:{$SpringIndex}:{$SpringLength}:{$ExpDot}={$Num}";
    }
}
