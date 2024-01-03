<?php
class Rule
{
    private array  $Labels  = [];
    private string $Default = 'R';

    public function __construct(string $Rules)
    {
        foreach (explode(',', $Rules) as $Rule) {
            $Matches = [];

            if (!str_contains($Rule, ':')) {
                $this->Default = $Rule;
            }
            else if (preg_match('/(?<Label>[a-z]+)(?<Op>[><])(?<Test>\d+):(?<To>[a-z]+)/i', $Rule, $Matches)) {
                $this->Labels[] = ['Label' => $Matches['Label'], 'Op' => $Matches['Op'], 'Test' => (int)$Matches['Test'], 'To' => $Matches['To']];
            }
            else {
                throw new RuntimeException('Cannot parse rule: ' . $Rule);
            }
        }
    }

    final public function Test(array $Part): string
    {
        foreach ($this->Labels as $Rule) {
            $Label = $Rule['Label'];

            if (isset($Part[$Label])) {
                if ($Rule['Op'] === '<' && $Part[$Label] < $Rule['Test']) {
                    return $Rule['To'];
                }

                if ($Rule['Op'] === '>' && $Part[$Label] > $Rule['Test']) {
                    return $Rule['To'];
                }
            }
        }

        return $this->Default;
    }

    public function GetOuts(): array
    {
        $Outs = [];

        foreach ($this->Labels as $Label) {
            $Outs[] = $Label;
        }

        return $Outs;
    }

    public function GetDefault(): string
    {
        return $this->Default;
    }

    public function GetLabels(): array
    {
        return $this->Labels;
    }
}

class Rules implements Iterator, ArrayAccess
{
    private array $_Rules = [];

    final public function offsetExists(mixed $offset): bool
    {
        if (!is_string($offset)) {
            throw new RuntimeException('Only strings, please.');
        }

        return isset($this->_Rules[$offset]);
    }

    final public function offsetGet(mixed $offset): mixed
    {
        if (!is_string($offset)) {
            throw new RuntimeException('Only strings, please.');
        }

        if (!isset($this->_Rules[$offset])) {
            throw new RuntimeException('That do no exist, sirs');
        }

        return $this->_Rules[$offset];
    }

    final public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!is_string($value) || !is_string($offset)) {
            throw new RuntimeException('Only strings, please.');
        }

        $this->_Rules[$offset] = new Rule($value);
    }

    final public function offsetUnset(mixed $offset): void
    {
        if (!is_string($offset)) {
            throw new RuntimeException('Only strings, please.');
        }

        unset($this->_Rules[$offset]);
    }

    final public function count(): int
    {
        return count($this->_Rules);
    }

    final public function current(): mixed
    {
        return current($this->_Rules);
    }

    final public function next(): void
    {
        next($this->_Rules);
    }

    final public function key(): mixed
    {
        return key($this->_Rules);
    }

    final public function valid(): bool
    {
        return current($this->_Rules) !== false;
    }

    final public function rewind(): void
    {
        reset($this->_Rules);
    }
}

class SortFactory
{
    private static bool  $Initialized = false;
    private static Rules $Rules;

    private static array $Parts = [];

    public static function AddRule(string $Line): void
    {
        self::Initialize();

        // px{a<2006:qkq,m>2090:A,rfg}
        $Matches = [];

        preg_match('/(?<Name>[a-z]+)\{(?<Rules>[^}]+)}/i', $Line, $Matches);

        $Name  = $Matches['Name'];
        $Rules = $Matches['Rules'];

        self::$Rules[$Name] = $Rules;
    }

    public static function AddPart(string $Line): void
    {
        self::Initialize();

        $Part = [];

        $Name = "P-" . hash('ripemd128', $Line);

        $Trimmed = str_replace(['{', '}'], '', $Line);

        $Categories = explode(',', $Trimmed);

        $Rating = 0;

        foreach ($Categories as $Category) {
            [$Label, $Value] = explode('=', $Category);

            $Part[$Label] = (int)$Value;

            $Rating += $Part[$Label];
        }

        $Part['r'] = $Rating;

        if (isset(self::$Parts[$Name])) {
            throw new RuntimeException("I done fucked up");
        }

        self::$Parts[$Name] = $Part;
    }

    private static function IsFinal(?string $Res): bool
    {
        if (empty($Res)) {
            return false;
        }

        return $Res === 'A' || $Res === 'R';
    }

    public static function TestParts(): int
    {
        self::Initialize();

        $Accepted = 0;

        foreach (self::$Parts as $Name => $Part) {
            $Result = self::TestPart($Part);

            if ($Result === 'A') {
                $Accepted += $Part['r'];
            }
        }

        return $Accepted;
    }

    private static function Initialize(): void
    {
        if (!self::$Initialized) {
            self::$Rules = new Rules();

            self::$Initialized = true;
        }
    }

    public static function FindMaxParts(string $RuleName = 'in', array $MinMax = ['x' => [1, 4000], 'm' => [1, 4000], 'a' => [1, 4000], 's' => [1, 4000]], array $Path = []): int
    {
        self::Initialize();

        $Value = 0;

        $Path[] = $RuleName;

        if ($RuleName === 'A') {
            return self::Range($MinMax);
        }

        if ($RuleName === 'R') {
            return 0;
        }

        $Negative = $MinMax;

        $Rule = self::$Rules[$RuleName];

        $Rules   = $Rule->GetOuts();
        $Default = $Rule->GetDefault();

        foreach ($Rules as $Out) {
            $Label = $Out['Label'];
            $Op    = $Out['Op'];
            $Test  = $Out['Test'];
            $To    = $Out['To'];

            $Positive = $Negative;

            if ($Op === '<') {
                $Positive[$Label][1] = min($Positive[$Label][1], $Test - 1);
                $Negative[$Label][0] = max($Negative[$Label][0], $Test);

            }
            else {
                $Positive[$Label][0] = max($Positive[$Label][0], $Test + 1);
                $Negative[$Label][1] = min($Negative[$Label][1], $Test);

            }

            $NewPath = $Path;

            $NewPath[] = '+>';

            $Value += self::FindMaxParts($To, $Positive, $NewPath);
        }

        $Path[] = '->';

        return $Value + self::FindMaxParts($Default, $Negative, $Path);
    }

    private static function Range(array &$XMAS): int
    {
        $AddItUp = 1;

        foreach ($XMAS as $For) {
            $AddItUp *= ($For[1] - $For[0] + 1);
        }

        return $AddItUp;
    }

    public static function Brute(): int
    {
        $Count = 0;

        print "Brute forcing...";

        for ($X = 1; $X <= 4000; $X++) {
            for ($M = 1; $M <= 4000; $M++) {
                for ($A = 1; $A <= 4000; $A++) {
                    for ($S = 1; $S <= 4000; $S++) {
                        $Part = ['x' => $X, 'm' => $M, 'a' => $A, 's' => $S];

                        $Res = self::TestPart($Part);

                        if ($Res === 'A') {
                            $Count++;
                        }
                    }
                }
            }

            print ".";
        }

        print " done.";

        return $Count;
    }

    private static function TestPart(array $Part): string
    {
        $Result = 'in';

        for (;;) {
            $Result = self::$Rules[$Result]->Test($Part);

            if (self::IsFinal($Result)) {
                break;
            }
        }

        return $Result;
    }
}
