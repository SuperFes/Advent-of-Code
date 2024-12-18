import clc  from 'cli-color';
import CCPU from "./lib/Emulate.js";

export default function CrashOverload(Registers, Program) {
    let Loopt = 0;

    let LowestFuck = BigInt(0b111111111111111111111111111111111111111111111111);

    const BytePops = [
        '000',
        '001',
        '010',
        '011',
        '100',
        '101',
        '110',
        '111',
    ];

    // RangeError: Maximum call stack size exceeded
    // Fucking JavaScript
    function* BitPopper(depth = 0, byte = 0) {
        for (let i = 0; i < 8; i++) {
            yield BytePops[i] << (depth * 3) | byte;
        }

        for (let i = 0; i < 8; i++) {
            yield* BitPopper(depth + 1, BytePops[i] << depth | byte);
        }
    }

    const Biterator = BitPopper();

    const ProgramString = Program.join(',');

    let Fuckle = BigInt(0b0000);

    let Matches = Array(16).fill(0, 0, Program.length);
    let Value   = Array(16).fill(0, 0, Program.length);

    function Random(number, number2) {
        return Math.floor(Math.random() * (number2 - number + 1)) + number;
    }

    for (;;) {
        const BitString = `0b${BytePops[Value[15]]}${BytePops[Value[14]]}${BytePops[Value[13]]}${BytePops[Value[12]]}${BytePops[Value[11]]}${BytePops[Value[10]]}${BytePops[Value[9]]}${BytePops[Value[8]]}${BytePops[Value[7]]}${BytePops[Value[6]]}${BytePops[Value[5]]}${BytePops[Value[4]]}${BytePops[Value[3]]}${BytePops[Value[2]]}${BytePops[Value[1]]}${BytePops[Value[0]]}`;

        // Apropos
        Fuckle = BigInt(BitString);

        Registers.a = Fuckle;

        const CPU = new CCPU(Registers);

        CPU.Run(Program);

        const outP = CPU.GetOutput();

        const outPStr = outP.map((value, i) => {
            const Does = value === Program[i];

            if (Does) {
                Matches[i]++;
            }
            else {
                Matches[i] = 0;
            }

            return Does ? clc.green.bold(value) : clc.red(value);
        }).join(',');

        if (Loopt++ > 100000) {
            console.log(outPStr, clc.yellow(Fuckle.toLocaleString()), BitString, 'Current:', clc.green.bold(LowestFuck));

            Loopt = 0;
        }

        if (outP.join(',') === ProgramString) {
            if (Fuckle < LowestFuck) {
                LowestFuck = Fuckle;
            }
        }

        for (let i = 15; i >= 0; i--) {
            if (Matches[i] === 0 || Matches[i] > 7 ** 3) {
                Value[i]++;

                if (Value[i] > 7) {
                    Value[i] = 0;
                }

                if (Random(0, 2) === 1) {
                    break;
                }
            }
        }
    }

    return true;
}
