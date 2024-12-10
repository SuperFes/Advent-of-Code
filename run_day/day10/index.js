#!/usr/bin/env node
import {Command} from "commander";
import fs        from "node:fs";
import part1     from './src/part1.js';
import part2     from './src/part2.js';

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

    data = fileData.split(/\n/i);

    let MiddleBits = [
        "Christmas",
        "Holiday",
        "Winter",
        "Snow",
        "Ice",
        "Santa Claus",
        "Mrs. Claus",
        "Elves",
        "Reindeer",
        "Rudolph",
        "North Pole",
        "Christmas Tree",
        "Ornaments",
        "Lights",
        "Tinsel",
        "Gifts",
        "Presents",
        "Wrapping Paper",
        "Ribbon",
        "Bow",
        "Stocking",
        "Fireplace",
        "Chimney",
        "Candy Cane",
        "Gingerbread",
        "Cookie",
        "Hot Chocolate",
        "Eggnog",
        "Mistletoe",
        "Snowflakes",
        "Sleigh",
        "Joy",
        "Peace",
        "Love",
        "Family",
        "Friends",
        "Cheer",
        "Merry",
        "Bright",
        "Happy",
        "Yuletide",
        "Noel",
        "Silent Night",
        "Jingle Bells",
        "Carol",
        "Hymn",
        "Winter Wonderland",
        "Frosty the Snowman",
        "Santa's Workshop",
        "Christmas Eve",
    ];

    let EndBits = [
        "North Pole",
        "Santa Claus",
        "Christmasville",
        "Winterhaven",
        "Snowville",
        "Frosty Hollow",
        "Candy Cane Lane",
        "Gingerbread Junction",
        "Mistletoe Manor",
        "Hollyhock Hills",
        "Evergreen Estates",
        "Pine Needle Point",
        "Reindeer Ridge",
        "Elfville",
        "Santa's Workshop",
        "Christmas Cove",
        "Yuletide Village",
        "Noelville",
        "Silent Night Springs",
        "Jingle Bell Junction",
        "Merryville",
        "Bright Christmas",
        "Happy Holidays Haven",
        "Winter Wonderland Way",
        "Frosty's Forest",
        "Rudolph's Ranch",
        "Santa's Secret Spot",
        "Elf's Enchanted Forest",
        "Grinch's Grotto",
        "Christmas Creek",
        "Winter River",
        "Snowy Mountain",
        "Icy Peak",
        "Twinkle Town",
        "Sparkle City",
        "Shimmer Shire",
        "Crystal Clear Cove",
        "Snowy Pines",
        "Frosted Fields",
        "Winter's Whisper Way",
        "Silent Night Street",
        "Joyful Junction",
        "Peaceful Place",
        "Love Lane",
        "Family Farm",
        "Friendly Forest",
        "Cheerful Circle",
        "Bright Boulevard",
        "Happy Highway",
        "Yuletide Yard",
    ];

    let Suffixes = [
        "Trail",
        "Path",
        "Run",
        "Slide",
        "Expedition",
    ];

    let GetTrailName = () => {
        const NumberOfMiddles = Math.floor(Math.random() * 3) + 1;

        let Middle = "";

        for (let i = 0; i < NumberOfMiddles; i++) {
            Middle += MiddleBits[Math.floor(Math.random() * MiddleBits.length)] + " ";
        }

        Middle = Middle.trim();

        let End    = EndBits[Math.floor(Math.random() * EndBits.length)];
        let Suffix = Suffixes[Math.floor(Math.random() * Suffixes.length)];

        return `${Middle} ${End} ${Suffix}`;
    }

    let Map  = [];
    let Ayes = [];

    for (const line of data) {
        if (line === '') {
            continue;
        }

        let Row = [];

        line.split('').forEach((el) => {
            let Depth = parseInt(el);

            if (isNaN(Depth)) {
                Depth = -1;
            }

            Row.push({
                depth : Depth,
                filled: 0
            });
        });

        Map.push(Row);
    }

    for (let y = 0; y < Map.length; y++) {
        for (let x = 0; x < Map[0].length; x++) {
            if (Map[y][x].depth === 0) {
                Ayes.push({
                    name    : GetTrailName(),
                    x       : x,
                    y       : y,
                    depth   : 0,
                    complete: false,
                    steps   : 0,
                    paths   : 0,
                });
            }
        }
    }

    runParts.map((part, i) => {
        const partNum = i + 1;

        console.time(`Part ${i} time`);

        part(Map, Ayes);

        console.timeEnd(`Part ${i} time`);
    });

    console.timeEnd('Execution time');
});
