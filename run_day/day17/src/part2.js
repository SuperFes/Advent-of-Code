import clc  from 'cli-color';
import CCPU from "./lib/Emulate.js";

export default function CrashOverload(Registers, Program) {
    // This is actually quite a high fuck to start out with
    let LowestFuck = BigInt(0b111111111111111111111111111111111111111111111111);

    // Possible 3 bit combos
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

    // Can't use it, blows up
    const Biterator = BitPopper();

    // This is to not run .join 10 million times more than we need to
    const ProgramString = Program.join(',');

    // Our current fuck chance
    let Fuckle = 0n;

    // How many times we've tried
    let Iterations = 0n;

    // Set up some arrays that represent which bit possibility we're trying as well as tracking whether they match as we intend or not
    let Matches = Array(16).fill(0, 0, 16);
    let Value   = Array(16).fill(0, 0, 16);

    // Random numberator
    function Random(number, number2) {
        return Math.floor(Math.random() * (number2 - number + 1)) + number;
    }

    // The fucking loop
    while (Iterations++ < 10_000_000n) {
        // Cobble together our variations of bits into a bit string that we're going to convert into a BigInt in a millisecond
        const BitString = `0b${BytePops[Value[15]]}${BytePops[Value[14]]}${BytePops[Value[13]]}${BytePops[Value[12]]}${BytePops[Value[11]]}${BytePops[Value[10]]}${BytePops[Value[9]]}${BytePops[Value[8]]}${BytePops[Value[7]]}${BytePops[Value[6]]}${BytePops[Value[5]]}${BytePops[Value[4]]}${BytePops[Value[3]]}${BytePops[Value[2]]}${BytePops[Value[1]]}${BytePops[Value[0]]}`;

        // This isn't strictly necessary, used to be a const, but through varying changes of the loop, it's still here this way
        let Output = [];

        // Apropos
        Fuckle = BigInt(BitString);

        // We set our initial register to the value we're trying
        Registers.a = Fuckle;

        // Start a new emulator
        const CPU = new CCPU(Registers);

        // Run our test value
        CPU.Run(Program);

        // Capture the output
        Output = CPU.GetOutput();

        // If it's not the same length, we're not interested
        if (Output.length !== Program.length) {
            continue;
        }

        // Make the output pretty BUT ALSO, if it matches the program
        const PrettyOutput = Output.map((value, i) => {
            const Does = value === Program[i];

            if (Does) {
                Matches[i]++;
            }
            else {
                Matches[i] = 0;
            }

            return Does ? clc.green.bold(value) : clc.red(value);
        }).join(',');

        // Occasionally give the user some idea that _something_ is happening
        if (Iterations % 10000n === 0n) {
            console.log(PrettyOutput, clc.yellow(Fuckle), BitString, 'Current:', clc.green.bold(LowestFuck), Iterations.toLocaleString());
        }

        // See if we match the program
        if (Output.join(',') === ProgramString) {
            // Need to find the lowest fuck
            if (Fuckle < LowestFuck) {
                console.log(PrettyOutput, clc.yellow(Fuckle), BitString, 'Current:', clc.green.bold(LowestFuck), Iterations.toLocaleString());

                LowestFuck = Fuckle;
            }
        }

        // After program evaluation, we need to randomly, but sticky a wee, and in a little bit of luck, we'll adjust our bit string
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

    // Who wants their output to say "10,000,001", nobody
    Iterations--;

    // If the lowest is actually the quite high fuck, we didn't win
    if (LowestFuck === 281474976710655n) {
        console.log('No solution found');
        console.log(clc.yellow('You have to try again'));
    }
    // We'll output our findings and let the user know, that there may in fact be other possible values
    else {
        console.log('Ending after ', Iterations.toLocaleString(), 'iterations');
        console.log('The lowest found is:', LowestFuck);
        console.log(clc.yellow('You may have to try again'));
    }

    // Sure, it's true
    return true;
}
