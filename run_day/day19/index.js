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

    data = fileData.split(/\n/i);

    let TowelsConfig    = {
        available: [],
        designs: [],
    };

    let LoadTowels = false;

    for (const line of data) {
        if (line.length === 0) {
            LoadTowels = true;

            continue;
        }

        if (LoadTowels) {
            const string = line.trim();

            TowelsConfig.designs.push({
                string: string,
                impossible: true,
                completed: 0
            });
        }
        else {
            TowelsConfig.available = line.split(', ');
        }
    }

    // console.debug(TowelsConfig);

    for (const part in runParts) {
        console.time(`Part ${part} time`);

        runParts[part](TowelsConfig);

        console.timeEnd(`Part ${part} time`);
    }

    console.timeEnd('Execution time');
});
