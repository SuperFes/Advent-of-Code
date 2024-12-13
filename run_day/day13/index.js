#!/usr/bin/env node
import {Command} from "commander";
import fs        from "node:fs";
import part1     from './src/part1.js';
import part2     from './src/part2.js';

console.time('Execution time');

const program = new Command();

let data = [];
let file = './Data/Test.txt';

let runParts = {1: part1, 2: part2};

const Nouns = [
    "Snow",
    "Ice",
    "Gift",
    "Elf",
    "Reindeer",
    "Santa",
    "Christmas",
    "Winter",
    "Holiday",
    "Miracle",
    "Joy",
    "Peace",
    "Love",
    "Cheer",
    "Magic",
    "Wonder",
    "Dream",
    "Night",
    "Star",
    "Bell",
];

const Adjectives = [
    "Merry",
    "Bright",
    "Happy",
    "Jolly",
    "Frosted",
    "Snowy",
    "Icy",
    "Golden",
    "Silvery",
    "Crystal",
    "Sparkling",
    "Shimmering",
    "Cozy",
    "Warm",
    "Magical",
    "Enchanting",
    "Mysterious",
    "Whimsical",
    "Festive",
    "Cheerful",
];

const Verbs = [
    "Dashing",
    "Twinkling",
    "Gleaming",
    "Shimmering",
    "Dancing",
    "Flying",
    "Soaring",
    "Sliding",
    "Falling",
    "Whispering",
    "Singing",
    "Humming",
];

const Phrases = [
    "Silent Night",
    "White Christmas",
    "Winter Wonderland",
    "Jingle Bells",
    "Merry Christmas",
    "Happy Holidays",
    "North Pole",
    "Santa's Workshop",
    "Elf Village",
    "Reindeer Games",
];

program
    .option('-d, --data <fileName>', 'Run on which data file', (fileName) => {
        file = fileName;
    })
    .option('-1, --part1', 'Run part 1 on the data', () => {
        runParts = {1: part1};
    })
    .option('-2, --part2', 'Run part 2 on the data', () => {
        runParts = {2: part2};
    });

program.parse(process.argv);

fs.readFile(file, 'utf8', (err, fileData) => {
    if (err) {
        console.error(err);

        return;
    }

    data = fileData.split(/\n\n/i);

    let Inc = 0;

    let ClawMachineName = () => {
        let name = '';

        name += Adjectives[Math.floor(Math.random() * Adjectives.length)];
        name += ' ';
        name += Nouns[Math.floor(Math.random() * Nouns.length)];

        name += ': ';
        name += Phrases[Math.floor(Math.random() * Phrases.length)];

        return name;
    }

    let ClawMachines = [];

    for (const machine of data) {
        const lines = machine.split(/\n/i);

        const Name       = ClawMachineName();

        let Instructions = {
            'Name' : Name,
        };

        for (const line of lines) {
            if (line.length === 0) {
                continue;
            }

            const [label, values] = line.split(': ');

            const Vals = {};

            values.split(', ').map((val) => {
                const [variable, op, value] = val.split(/(\+|=)/);

                Vals[variable] = {
                    op      : op,
                    value   : parseInt(value),
                };
            });

            if (label === 'Button A') {
                Vals['Cost'] = 3;
            }
            else if (label === 'Button B') {
                Vals['Cost'] = 1;
            }

            Instructions[label] = Vals;
        }

        ClawMachines.push(Instructions);
    }

    // console.debug(ClawMachines);

    for (const part in runParts) {
        console.time(`Part ${part} time`);

        runParts[part](ClawMachines);

        console.timeEnd(`Part ${part} time`);
    }

    console.timeEnd('Execution time');
});
