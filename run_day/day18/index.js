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

    let Map = {
        runTime: 0,
        time   : 0,
        id     : 0,
        bounds : {
            width : 0,
            height: 0
        },
        grid   : {},
        bytes  : [],
        graph  : {},
        path   : [],
    };

    for (const line of data) {
        if (line.length === 0) {
            continue;
        }

        const configPart = line.split(',').map(Number);

        if (Map.bounds.width === 0) {
            Map.bounds.width  = configPart[0];
            Map.bounds.height = configPart[1];
            Map.runTime       = configPart[2];

            continue;
        }

        if (configPart.length === 2) {
            Map.bytes.push({
                id    : Map.id,
                x     : Number(configPart[0]),
                y     : Number(configPart[1]),
                fallen: false,
                time  : Infinity,
            });
        }

        Map.id++;
    }

    for (const part in runParts) {
        console.time(`Part ${part} time`);

        runParts[part](Map);

        console.timeEnd(`Part ${part} time`);
    }

    console.timeEnd('Execution time');
});
