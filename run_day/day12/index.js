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
        id     : 0,
        bounds : {
            x: data[0].length,
            y: data.length - 1
        },
        grid   : {},
        groupId: 0,
    };

    for (let y = 0; y < data.length; y++) {
        const line = data[y];

        if (line.length === 0) {
            continue;
        }

        for (let x = 0; x < line.length; x++) {
            let char = line[x];

            Map.grid[`${x},${y}`] = {
                id     : Map.id++,
                plant  : char,
                x      : x,
                y      : y,
                fences : {},
                edges  : 0,
                group  : null,
                corners: 0,
            };
        }
    }

    let SearchGrid = {
        center: null,
        left  : null,
        up    : null,
        right : null,
        down  : null,
    };

    // let NoEdges = [];

    let UpdateSearchGrid = (pointX, pointY) => {
        const left  = `${pointX - 1},${pointY}`;
        const up    = `${pointX},${pointY - 1}`;
        const right = `${pointX + 1},${pointY}`;
        const down  = `${pointX},${pointY + 1}`;

        SearchGrid.center = Map.grid[`${pointX},${pointY}`]

        if (Map.grid[left]) {
            SearchGrid.left = Map.grid[left];
        }
        else {
            SearchGrid.left = null;
        }

        if (Map.grid[up]) {
            SearchGrid.up = Map.grid[up];
        }
        else {
            SearchGrid.up = null;
        }

        if (Map.grid[right]) {
            SearchGrid.right = Map.grid[right];
        }
        else {
            SearchGrid.right = null;
        }

        if (Map.grid[down]) {
            SearchGrid.down = Map.grid[down];
        }
        else {
            SearchGrid.down = null;
        }
    }

    let CheckFences = (pointX, pointY) => {
        let fences = {left: false, right: false, up: false, down: false};

        if (SearchGrid.left === null || SearchGrid.left.plant !== SearchGrid.center.plant) {
            fences['left'] = true;
        }

        if (SearchGrid.right === null || SearchGrid.right.plant !== SearchGrid.center.plant) {
            fences['right'] = true;
        }

        if (SearchGrid.up === null || SearchGrid.up.plant !== SearchGrid.center.plant) {
            fences['up'] = true;
        }

        if (SearchGrid.down === null || SearchGrid.down.plant !== SearchGrid.center.plant) {
            fences['down'] = true;
        }
        // console.debug(SearchGrid, fences);

        return fences;
    }

    for (const point in Map.grid) {
        const pip = Map.grid[point];

        UpdateSearchGrid(pip.x, pip.y);

        Map.grid[point].fences = CheckFences(pip.x, pip.y);
        Map.grid[point].edges  = Object.values(pip.fences).filter((fence) => fence).length;
    }

    let FloodFillGroup = (point) => {
        const group = Map.groupId++;

        const plant = Map.grid[point].plant;

        let stack = [point];

        while (stack.length > 0) {
            const currentPoint = stack.pop();

            const pip = Map.grid[currentPoint];

            pip.group = group;

            const left  = Map.grid[`${pip.x - 1},${pip.y}`];
            const up    = Map.grid[`${pip.x},${pip.y - 1}`];
            const right = Map.grid[`${pip.x + 1},${pip.y}`];
            const down  = Map.grid[`${pip.x},${pip.y + 1}`];

            if (!pip.fences.left && left.group === null) {
                stack.push(`${left.x},${left.y}`);
            }

            if (!pip.fences.up && up.group === null) {
                stack.push(`${up.x},${up.y}`);
            }

            if (!pip.fences.right && right.group === null) {
                stack.push(`${right.x},${right.y}`);
            }

            if (!pip.fences.down && down.group === null) {
                stack.push(`${down.x},${down.y}`);
            }
        }
    }

    for (const point in Map.grid) {
        const pip = Map.grid[point];

        if (pip.group === null) {
            FloodFillGroup(point);
        }
    }

    // console.debug(Map);
    // console.debug(NoEdges);

    for (const part in runParts) {
        console.time(`Part ${part} time`);

        runParts[part](Map);

        console.timeEnd(`Part ${part} time`);
    }

    console.timeEnd('Execution time');
});
