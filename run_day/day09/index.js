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

    const data = String(fileData).trim();

    let Files = [];

    let id = 0;
    let position = 0;

    for (let d = 0; d < data.length; d += 2) {
        let fileSize = parseInt(data.charAt(d));
        let freeSize = parseInt(data.charAt(d + 1));

        if (isNaN(fileSize)) {
            break;
        }

        if (isNaN(freeSize)) {
            freeSize = 0;
        }

        Files.push({
            id: id,
            position: position,
            size: fileSize,
        });

        position += fileSize + freeSize;

        id++;
    }

    let Blocks = new Array(position).fill(null);

    for (const file of Files) {
        for (let p = file.position; p < file.position + file.size; p++) {
            Blocks[p] = file.id;
        }
    }

    let partNum = 0;

    for (const part of runParts) {
        partNum++;

        console.time(`Part ${partNum} time`);

        part(Files, Blocks);

        console.timeEnd(`Part ${partNum} time`);
    };

    console.timeEnd('Execution time');
});
