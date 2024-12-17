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

let UsedNames = {};

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

    let Map    = {
        id: 0,
        start: {
            x: 0,
            y: 0,
        },
        finish: {
            x: 0,
            y: 0,
        },
        bounds: {
            x: data[0].length,
            y: data.length
        },
        paths: {},
    };

    for (let y = 0; y < Map.bounds.y; y++) {
        let line = data[y];

        if (line.length === 0) {
            continue;
        }

        for (let x = 0; x < Map.bounds.x; x++) {
            let char = line[x];

            if (char === '#') {
                continue;
            }
            else if (char === 'S') {
                Map.start.x = x;
                Map.start.y = y;
            }
            else if (char === 'E') {
                Map.finish.x = x;
                Map.finish.y = y;
            }

            const path = `${x},${y}`;

            Map.paths[path] = {
                id: Map.id,
                x: x,
                y: y,
                cost: Infinity,
                used: {
                    up: false,
                    down: false,
                    left: false,
                    right: false,
                },
                ajd: {},
            };

            Map.id++;
        }
    }

    for (let y = 0; y < Map.bounds.y; y++) {
        for (let x = 0; x < Map.bounds.x; x++) {
            if (Map.paths[`${x},${y}`] !== undefined) {
                const path = Map.paths[`${x},${y}`];

                const up = Map.paths[`${x},${y - 1}`];
                const down = Map.paths[`${x},${y + 1}`];
                const left = Map.paths[`${x - 1},${y}`];
                const right = Map.paths[`${x + 1},${y}`];

                let adjCount = 0;

                if (up !== undefined) {
                    path.ajd.up = up;

                    adjCount++;
                }

                if (right !== undefined) {
                    path.ajd.right = right;

                    adjCount++;
                }

                if (down !== undefined) {
                    path.ajd.down = down;

                    adjCount++;
                }

                if (left !== undefined) {
                    path.ajd.left = left;

                    adjCount++;
                }

                if (adjCount > 2) {
                    path.intersection = true;
                }
            }
        }
    }

    // console.debug(Map);

    for (const part in runParts) {
        console.time(`Part ${part} time`);

        runParts[part](Map);

        console.timeEnd(`Part ${part} time`);
    }

    console.timeEnd('Execution time');
});
