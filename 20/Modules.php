<?php

class Module
{
    private bool $Debug = false;

    protected static int $Presses = 0;

    private static string $Prefix = '!';

    private static array $_Modules   = [];
    private static array $_Intervals = [];

    private static int $HighCount = 0;
    private static int $LowCount  = 0;

    protected string $Name;

    protected SplObjectStorage $From;
    protected SplObjectStorage $To;

    private static array $Work = [];

    private static bool $Working = false;

    public function __construct(string $Name)
    {
        $this->From = new SplObjectStorage();
        $this->To   = new SplObjectStorage();

        $this->Name = $Name;

        self::$_Modules[$Name]   = &$this;
        self::$_Intervals[$Name] = [[0]];
    }

    public static function GetHigh(): int
    {
        return self::$HighCount;
    }

    public static function GetLow(): int
    {
        return self::$LowCount;
    }

    private static function SendQueue(): void
    {
        if (self::$Working === false) {
            self::$Working = true;

            while (!empty(self::$Work)) {
                $Package = array_shift(self::$Work);

                foreach ($Package as [$Module, $Pulse, $From]) {
                    $Module->Recv($Pulse, $From);
                }
            }

            self::$Working = false;
        }
    }

    final public function AttachTo(Module $To, mixed $Info = null): void
    {
        $this->To->attach($To, $Info);
    }

    final public function AttachFrom(Module $From, mixed $Info = null): void
    {
        if ($this->Debug) {
            print $this->Name . " attached to from " . $From->GetName() . "\n";
        }

        $this->From->attach($From, $Info);
    }

    final protected function Send(bool $Pulse): void
    {
        $Package = [];

        foreach ($this->To as $Module) {
            if ($Pulse) {
                self::$HighCount++;
            }
            else {
                self::$LowCount++;
            }

            $Package[] = [$Module, $Pulse, $this];
        }

        self::$Work[] = $Package;

        self::SendQueue();
    }

    protected function Recv(bool $High, Module $From): void
    {
        if ($this->Debug) {
            print "{$this->Name} received a " . ($High ? 'high' : 'low') . " pulse from " . $From->GetName() . ".\n";
        }
    }

    public function GetName(): string
    {
        return $this->Name;
    }
}

class FlipFlop extends Module
{
    private static string $Prefix = '%';

    private bool $On = false;

    final public function Recv(bool $High, Module $From): void
    {
        parent::Recv($High, $From);

        if (!$High) {
            $this->On = !$this->On;

            $this->Send($this->On);
        }
    }

    final public function Reset(): void
    {
        $this->On = false;
    }
}

class Conjunction extends Module
{
    private static string $Prefix = '&';

    private SplObjectStorage $Intervals;

    private int $Cascade = 0;

    public function __construct(string $Name)
    {
        parent::__construct($Name);

        $this->Intervals = new SplObjectStorage();
    }

    final public function Recv(bool $High, Module $From): void
    {
        parent::Recv($High, $From);

        $this->From[$From] = $High;

        if ($High && $this->Name === 'zh') {
            $Intervals = [];

            if ($this->Intervals->offsetExists($From)) {
                $Intervals = $this->Intervals[$From];
            }

            if (count($Intervals) < 2) {
                $Intervals[] = self::$Presses;

                $this->Intervals[$From] = $Intervals;
            }
            else {
                $Ready = true;

                $Cascade = 1;

                foreach ($this->From as $Interval) {
                    if (!isset($this->Intervals[$Interval])) {
                        $Ready = false;

                        break;
                    }

                    $Intervals = $this->Intervals[$Interval];

                    $End  = end($Intervals);
                    $Prev = prev($Intervals);

                    $Cascade *= $End - $Prev;
                }

                if ($Ready === true) {
                    $this->Cascade = $Cascade;
                }
            }
        }

        $High = true;

        foreach ($this->From as $Module) {
            if (!$this->From[$Module]) {
                $High = false;
            }
        }

        $this->Send(!$High);
    }

    final public function Reset(): void
    {
        foreach ($this->From as $Module) {
            $this->From[$Module] = false;
        }
    }

    final public function GetStatus(): int
    {
        return $this->Cascade;
    }
}

class Broadcast extends Module
{
    final public function Recv(bool $High, Module $From): void
    {
        parent::Recv($High, $From);

        $this->Send($High);
    }

    final public function Reset(): void
    {
    }
}

class Output extends Module
{
    private bool $LowLowLow = false;

    final public function Recv(bool $High, Module $From): void
    {
        parent::Recv($High, $From);

        if (!$High) {
            $this->LowLowLow = true;
        }
    }

    public function IsLowLowLow(): bool
    {
        return $this->LowLowLow;
    }

    final public function Reset(): void
    {
        $this->LowLowLow = false;
    }
}

class Button extends Module
{
    final public function Press()
    {
        self::$Presses++;

        $this->Send(false);
    }

    final public function Reset(): void
    {
        self::$Presses = 0;
    }
}
