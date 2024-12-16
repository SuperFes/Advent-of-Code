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

    let Map   = {
        Width: 1,
        Bounds: {
            x: 0,
            y: 0,
        },
        Boxes: [],
        Walls: [],
        Moves: [],
        Robot: {
            x: -1,
            y: -1,
        },
        CheckSums: [],
    };

    let Map2   = {
        Width: 2,
        Bounds: {
            x: 0,
            y: 0,
        },
        Boxes: [],
        Walls: [],
        Moves: [],
        Robot: {
            x: -1,
            y: -1,
        },
        CheckSums: [],
    };

    let MapData = true;
    let y = 0;

    for (const line of data) {
        if (line.length === 0) {
            MapData = false;

            continue;
        }

        if (MapData) {
            if (Map.Bounds.x === 0) {
                Map.Bounds.x = line.length;
            }

            if (Map2.Bounds.x === 0) {
                Map2.Bounds.x = line.length * 2;
            }

            for (let x = 0; x < line.length; x++) {
                if (line[x] === '#') {
                    Map.Walls.push({x: x, y: y});

                    Map2.Walls.push({x: x * 2, y: y});
                    Map2.Walls.push({x: x * 2 + 1, y: y});
                }
                else if (line[x] === 'O') {
                    Map.Boxes.push({x: x, y: y, char: 'O'});

                    let box = {x: x * 2, y: y, char: '['};
                    let boxButt = {x: x * 2 + 1, y: y, char: ']'};

                    box.bro = boxButt;
                    boxButt.bro = box;

                    Map2.Boxes.push(box);
                    Map2.Boxes.push(boxButt);
                }
                else if (line[x] === '@') {
                    Map.Robot.x = x;
                    Map.Robot.y = y;

                    Map2.Robot.x = x * 2;
                    Map2.Robot.y = y;
                }
            }

            y++;
        }
        else {
            for (let x = 0; x < line.length; x++) {
                const char = line[x];

                if (char === '^' || char === '>' || char === 'v' || char === '<') {
                    Map.Moves.push(char);
                    Map2.Moves.push(char);
                }
            }
        }
    }

    Map.Bounds.y = y;
    Map2.Bounds.y = y;

    for (const part in runParts) {
        console.time(`Part ${part} time`);

        runParts[part](Map, Map2);

        console.timeEnd(`Part ${part} time`);
    }

    console.timeEnd('Execution time');
});
