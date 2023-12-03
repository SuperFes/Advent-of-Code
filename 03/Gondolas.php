#!/usr/bin/env php
<?php
class Box
{
    public int $t;
    public int $l;
    public int $b;
    public int $r;

    public function __construct(int $t, int $l, int $b, int $r)
    {
        $this->t = $t;
        $this->l = $l;
        $this->b = $b;
        $this->r = $r;
    }
}

class Symbol
{
    private int $x;
    private int $y;

    private string $char;

    public function __construct(int $X, int $Y, string $Value)
    {
        $this->x = $X;
        $this->y = $Y;

        $this->char = $Value;
    }

    final public function Inside(Box $Root): bool
    {
        return $this->y >= $Root->t
               && $this->y <= $Root->b
               && $this->x >= $Root->l
               && $this->x <= $Root->r;
    }

    final public function IsGear(): bool
    {
        return $this->char === '*';
    }

    final public function FindProduct(array $Numbois): int
    {
        $Ratios = [];

        foreach ($Numbois as $Numboi) {
            if ($Numboi->SymBox($this)) {
                $Ratios[] = $Numboi->Num();
            }
        }

        $Ratio    = 0;
        $NumGears = count($Ratios);

        if ($NumGears > 1) {
            $Ratio = $Ratios[0];

            for ($g = 1; $g < $NumGears; $g++) {
                $Ratio *= $Ratios[$g];
            }
        }

        return $Ratio;
    }
}

class Number
{
    private int $x;
    private int $y;

    private int $val;
    private int $len;

    public function __construct(int $X, int $Y, string $Value)
    {
        $this->x = $X;
        $this->y = $Y;

        $this->val = (int)$Value;
        $this->len = strlen($Value);
    }

    final public function Num(): int
    {
        return $this->val;
    }

    final public function SymBox(Symbol $From): bool
    {
        $Box = new Box($this->y - 1, $this->x - 1, $this->y + 1, $this->x + $this->len);

        return $From->Inside($Box);
    }

    final public function SymBoxen(array $Froms): bool
    {
        $Box = new Box($this->y - 1, $this->x - 1, $this->y + 1, $this->x + $this->len);

        foreach ($Froms as $From) {
            if ($From->Inside($Box)) {
                return true;
            }
        }

        return false;
    }
}

$Total1 = 0;
$Total2 = 0;

$Thing = fopen("Data/Data.txt", "rb+");

$Row = 0;
$Col = null;

$Numbers = [];
$Symbols = [];

while (!feof($Thing)) {
    $Line = trim(fgets($Thing));

    if (empty($Line)) {
        continue;
    }

    $Col    = -1;
    $Number = "";

    $RowLen = strlen($Line);

    for ($c = 0; $c < $RowLen; $c++) { // Take that, Joe
        $Char = $Line[$c];

        if (is_numeric($Char)) {
            if ($Col < 0) {
                $Col = $c;
            }

            $Number .= $Char;
        }
        else {
            if ($Col >= 0) {
                $Numbers[] = new Number($Col, $Row, $Number);

                $Number = '';
                $Col    = -1;
            }

            if ($Char === '.') {
                continue;
            }

            $Symbols[] = new Symbol($c, $Row, $Char);
        }
    }

    if ($Col >= 0) {
        $Numbers[] = new Number($Col, $Row, $Number);
    }

    $Row++;
}

fclose($Thing);

$NumNumNum = 0;
$SymboyNum = 0;

foreach ($Numbers as $NumNum) {
    if ($NumNum->SymBoxen($Symbols)) {
        $NumNumNum++;

        $Total1 += $NumNum->Num();
    }
}

foreach ($Symbols as $Symboy) {
    if ($Symboy->IsGear()) {
        $SymboyNum++;

        $Total2 += $Symboy->FindProduct($Numbers);
    }
}

print "Total 1: {$Total1}({$NumNumNum})\n";
print "Total 2: {$Total2}({$SymboyNum})\n";
