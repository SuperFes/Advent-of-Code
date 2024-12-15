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

const Names = [
    'Unit 734',
    'Model B',
    'Alpha-1',
    'Omega-X',
    'Cyborg',
    'Zeus',
    'Ares',
    'Apollo',
    'Hera',
    'Vulcan',
    'Minerva',
    'X-Mas',
    'Noel',
    'Mistletoe',
    'Jingle',
    'Holly',
    'Snowball',
    'Snowflake',
    'Sparkle',
    'Twinkle',
    'Nimble',
    'Swift',
    'Whisper',
    'Flicker',
    'Dasher',
    'Dancer',
    'Prancer',
    'Vixen',
    'Comet',
    'Cupid',
    'Donner',
    'Blitzen',
    'Santa,',
    'Claus',
    'KrisKringle',
    'GiftGiver',
    'Present',
];

const Suffixes = [
    'X',
    'X-1',
    'Bot',
    '209',
    'X',
    'Robo',
];

const Prefixes = [
    'Cyber',
    'Cybernetic',
    'Robotic',
    'Mechanical',
    'Automated',
    'Intelligent',
    'Smart',
    'Genius',
    'Super',
    'Ultra',
    'Mega',
    'Giga',
    'Tera',
    'Peta',
    'Exa',
    'Zetta',
    'Yotta',
    'Nano',
    'Pico',
    'Femto',
    'Atto',
    'Zepto',
    'Yocto',
    'Quantum',
    'Cosmic',
    'Galactic',
    'Universal',
    'Stellar',
    'Planetary',
    'Lunar',
    'Solar',
    'Astral',
    'Celestial',
    'Terrestrial',
    'Earthly',
    'Global',
    'Worldly',
    'Local',
    'Regional',
    'National',
    'International',
    'Interstellar',
    'Interplanetary',
    'Interdimensional',
    'Multidimensional',
    'Parallel',
    'Alternate',
    'Virtual',
    'Digital',
    'Analog',
    'Analogous',
    'Similar',
    'Equivalent',
    'Equal',
    'Identical',
    'Magnanimous',
    'Twin',
];

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

function GetRandomName() {
    let name = '';

    name += Prefixes[Math.floor(Math.random() * Prefixes.length)];
    name += ' ';
    name += Names[Math.floor(Math.random() * Names.length)];
    name += ' ';
    name += Suffixes[Math.floor(Math.random() * Suffixes.length)];

    if (UsedNames[name]) {
        name += ' v' + ++UsedNames[name];
    }
    else {
        UsedNames[name] = 1;
    }

    return name;
}

fs.readFile(file, 'utf8', (err, fileData) => {
    if (err) {
        console.error(err);

        return;
    }

    data = fileData.split(/\n/i);

    let Map    = {};
    let Robots = {
        id: 0,
        bots: [],
    };

    for (const line of data) {
        if (line.length === 0) {
            continue;
        }

        const configPart = line.split(/[ =]/);

        console.debug(configPart);

        const [left, rule1, right, rule2] = configPart;

        if (left === 'm') {
            const [MapX, MapY]   = rule1.split(',').map(Number);
            const [Part1, Part2] = rule2.split(',').map(Number);

            Map.bounds     = {x: MapX, y: MapY};
            Map.iterations = {part1: Part1, part2: Part2};
            Map.grid       = Array(MapY).fill(Array(MapX).fill(null));
        }
        else if (left === 'p') {
            const [RobotX, RobotY]       = rule1.split(',').map(Number);
            const [RobotDirX, RobotDirY] = rule2.split(',').map(Number);

            const Name = GetRandomName();

            Robots.bots[Robots.id] = {
                name: Name,
                id: Robots.id,
                origPos: {
                    x: RobotX,
                    y: RobotY,
                },
                pos: {
                    x: RobotX,
                    y: RobotY,
                },
                dir: {
                    x: RobotDirX,
                    y: RobotDirY
                },
                intervals: {},
                lastInterval: 0,
            };

            Robots.id++;
        }
    }

    // console.debug(Map);
    // console.debug(Robots);

    for (const part in runParts) {
        console.time(`Part ${part} time`);

        runParts[part](Map, Robots);

        console.timeEnd(`Part ${part} time`);
    }

    console.timeEnd('Execution time');
});
