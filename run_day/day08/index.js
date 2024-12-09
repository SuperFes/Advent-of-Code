#!/usr/bin/env node
import {Command} from "commander";
import fs      from "node:fs";
import part1 from './src/part1.js';
import part2 from './src/part2.js';

console.time('Execution time');

const program = new Command();

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

    let lines = fileData.split(/\n/i);

    let map = {
        bounds: {
            width: lines[0].length,
            height: lines.length - 1,
        },
        antennae: [],
        antinodes: [],
    };


    for (let y = 0; y < map.bounds.height; y++) {
        if (lines[y].length === 0) {
            continue;
        }

        for (let x = 0; x < map.bounds.width; x++) {
            const char = lines[y][x];

            if (char === '.') {
                continue;
            }

            map.antennae.push({id: char, x: x, y: y});
        }
    }

    runParts.map((part, i) => {
        const partNum = i + 1;

        console.time(`Part ${i} time`);

        part(map);

        console.timeEnd(`Part ${i} time`);
    });

    console.timeEnd('Execution time');
});
