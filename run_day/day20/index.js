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
        bounds       : {
            width : 0,
            height: 0
        },
        start        : {
            x: 0,
            y: 0
        },
        end          : {
            x: 0,
            y: 0
        },
        id           : 0,
        path         : [],
        grid         : {},
        graph        : {},
        wallGraph    : {},
        walls        : {},
        cheat        : null,
        cheats       : [],
        cheatPoints  : {},
        uniqueCheats : {},
        shortestCheat: [],
    };

    for (const line of data) {
        if (line.length === 0) {
            continue;
        }

        if (Map.bounds.width === 0) {
            Map.bounds.width = line.length;
        }

        for (let x = 0; x < line.length; x++) {
            if (line[x] === 'S') {
                Map.start = {x, y: Map.bounds.height};
            }
            else if (line[x] === 'E') {
                Map.end = {x, y: Map.bounds.height};
            }

            if (line[x] !== '#') {
                Map.grid[`${x}:${Map.bounds.height}`] = {
                    char: line[x],
                    x   : x,
                    y   : Map.bounds.height,
                    id  : Map.id,
                };
            }
            else {
                Map.walls[`${x}:${Map.bounds.height}`] = {
                    char: line[x],
                    x   : x,
                    y   : Map.bounds.height,
                    id  : Map.id,
                };
            }
        }

        Map.bounds.height++;

        Map.id++;
    }

    for (const wall in Map.walls) {
        const wallParts = Map.walls[wall];

        const x = wallParts.x
        const y = wallParts.y;

        const left  = Map.grid[`${x - 1}:${y}`];
        const up    = Map.grid[`${x}:${y - 1}`];
        const right = Map.grid[`${x + 1}:${y}`];
        const down  = Map.grid[`${x}:${y + 1}`];

        if (left && right) {
            Map.cheatPoints[`${x}:${y}`] = {x, y, dir: '-'};
        }

        if (up && down) {
            Map.cheatPoints[`${x}:${y}`] = {x, y, dir: '|'};
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
