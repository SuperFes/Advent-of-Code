#!/usr/bin/env node
import {Command} from "commander";
import fs      from "node:fs";
import part1 from './src/part1.js';
import part2 from './src/part2.js';

console.time('Execution time');

const program = new Command();

let data = [];
let file = './Data/Test.txt';

let runParts = [part1, part2];

program
    .option('-d, --data <fileName>', 'Run on which data file', (fileName) => {
        file = fileName;
    })
    .option('-1, --part1', 'Run part 1 on the data', () => {
        runParts = [part1];
    })
    .option('-2, --part2', 'Run part 2 on the data', () => {
        runParts = [part2];
    });

program.parse(process.argv);

fs.readFile(file, 'utf8', (err, fileData) => {
    if (err) {
        console.error(err);

        return;
    }

    data = fileData.split(/\n/);

    runParts.map((part, i) => {
        const partNum = i + 1;

        console.time(`Part ${i} time`);

        part(data);

        console.timeEnd(`Part ${i} time`);
    });

    console.timeEnd('Execution time');
});
