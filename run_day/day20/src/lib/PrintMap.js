import clc from "cli-color";

export default function PrintMap(Map, id = null) {
    for (let y = 0; y < Map.bounds.height; y++) {
        for (let x = 0; x < Map.bounds.width; x++) {
            const curLoc = `${x}:${y}`;

            const wall = Map.walls[curLoc];
            const path = Map.path.includes(curLoc);

            let cheat = Map.path.includes(`[${curLoc}]`);

            if (x === Map.start.x && y === Map.start.y) {
                process.stdout.write(clc.yellow.bgXterm(29).bold.italic('󰬚'));
            }
            else if (x === Map.end.x && y === Map.end.y) {
                process.stdout.write(clc.yellow.bgXterm(29).bold.italic('󰬌'));
            }
            else if (cheat) {
                process.stdout.write(clc.bgXterm(165).bold.xterm(87)('C'));
            }
            else if (path) {
                process.stdout.write(clc.bgXterm(29).bold.xterm(158)('X'));
            }
            else if (wall) {
                process.stdout.write(clc.xterm(207).bgXterm(29)('█'));
            }
            else {
                process.stdout.write(clc.bgXterm(29).green('󱔐'));
            }
        }

        process.stdout.write('\n');
    }

    console.log(`Steps: ${Map.path.length - 1}`);
}
