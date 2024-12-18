export default class CCPU {
    registers = {
        a: 0n,
        b: 0n,
        c: 0n,
    }

    instructions       = [];
    instructionPointer = 0n;

    output = [];

    constructor(Registers) {
        this.registers = {
            a: BigInt(Registers.a),
            b: BigInt(Registers.b),
            c: BigInt(Registers.c),
        };
    }

    Run(program = []) {
        this.instructions = program;

        // console.log('Registers:', this.registers);

        let debugLoops = 0;

        const ProgramLength = BigInt(this.instructions.length);

        for (let i = this.instructionPointer, o = this.instructionPointer + 1n; o < ProgramLength; i = this.instructionPointer, o = this.instructionPointer + 1n) {
            this.Execute(BigInt(this.instructions[i]), BigInt(this.instructions[o]));

            // console.log('Registers:', this.registers, 'Instruction Pointer:', this.instructionPointer);

            debugLoops++;

            if (debugLoops % 100000 === 0) {
                process.stdout.write('.');
                // console.log('Breaking out of loop');

                // break;
            }
        }
    }

    adv(combo) {
        combo = this.CheckCombo(combo);

        this.registers.a /= (2n ** combo);

        this.instructionPointer += 2n;
    }

    bxl(literal) {
        this.registers.b ^= literal;

        this.instructionPointer += 2n;
    }

    bst(combo) {
        combo = this.CheckCombo(combo);

        this.registers.b = combo & 7n;

        this.instructionPointer += 2n;
    }

    jnz(literal) {
        if (this.registers.a !== 0n) {
            this.instructionPointer = literal;
        }
        else {
            this.instructionPointer += 2n;
        }
    }

    bxc(literal) {
        this.registers.b = this.registers.c ^ this.registers.b;

        this.instructionPointer += 2n;
    }

    out(combo) {
        combo = this.CheckCombo(combo);

        combo &= 7n;

        this.output.push(combo);

        this.instructionPointer += 2n;
    }

    bdv(combo) {
        combo = this.CheckCombo(combo);

        this.registers.b = this.registers.a / (2n ** combo);

        this.instructionPointer += 2n;
    }

    cdv(combo) {
        combo = this.CheckCombo(combo);

        this.registers.c = this.registers.a / (2n ** combo);

        this.instructionPointer += 2n;
    }

    Execute(op, arg) {
        arg = BigInt(arg);

        if (op === 0n) {
            return this.adv(arg);
        }
        else if (op === 1n) {
            return this.bxl(arg);
        }
        else if (op === 2n) {
            return this.bst(arg);
        }
        else if (op === 3n) {
            return this.jnz(arg);
        }
        else if (op === 4n) {
            return this.bxc(arg);
        }
        else if (op === 5n) {
            return this.out(arg);
        }
        else if (op === 6n) {
            return this.bdv(arg);
        }
        else if (op === 7n) {
            return this.cdv(arg);
        }
        else {
            console.log('Invalid operation', op);
        }

        return 2;
    }

    CheckCombo(combo) {
        if (combo > 3n) {
            // console.log('Combo:', combo);

            if (combo === 4n) {
                combo = this.registers.a;
            }
            else if (combo === 5n) {
                combo = this.registers.b;
            }
            else if (combo === 6n) {
                combo = this.registers.c;
            }
            else if (combo === 7n) {
                throw new Error('Invalid argument 7. Does not appear in valid programs');
            }
        }

        return BigInt(combo);
    }

    Print() {
        console.log('Output:', this.output.join(','));
    }

    GetOutput() {
        return this.output;
    }
}
