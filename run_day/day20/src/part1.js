import PrintMap from './lib/PrintMap.js';
import clc      from "cli-color";

export default function MutexLock(Map) {
    function MakeGraph() {
        Map.graph = {};

        for (let y = 0; y <= Map.bounds.height; y++) {
            for (let x = 0; x <= Map.bounds.width; x++) {
                const left  = Map.grid[`${x - 1}:${y}`] || `${x - 1}:${y}` === Map.cheat;
                const right = Map.grid[`${x + 1}:${y}`] || `${x + 1}:${y}` === Map.cheat;
                const up    = Map.grid[`${x}:${y - 1}`] || `${x}:${y - 1}` === Map.cheat;
                const down  = Map.grid[`${x}:${y + 1}`] || `${x}:${y + 1}` === Map.cheat;

                if (!left && !right && !up && !down) {
                    continue;
                }

                if (!Map.graph[`${x}:${y}`]) {
                    Map.graph[`${x}:${y}`] = [];
                }

                if (left) {
                    Map.graph[`${x}:${y}`].push(`${x - 1}:${y}`);
                }

                if (right) {
                    Map.graph[`${x}:${y}`].push(`${x + 1}:${y}`);
                }

                if (up) {
                    Map.graph[`${x}:${y}`].push(`${x}:${y - 1}`);
                }

                if (down) {
                    Map.graph[`${x}:${y}`].push(`${x}:${y + 1}`);
                }
            }
        }

        // console.debug(Map);
    }

    function Distance(a) {
        return Math.abs(a.x - Map.bounds.width) + Math.abs(a.y - Map.bounds.height);
    }

    function FindPath(highest = Infinity) {
        let paths = [{
            x      : Map.start.x,
            y      : Map.start.y,
            steps  : [`${Map.start.x}:${Map.start.y}`],
            cheated: false,
        }];

        let seen = {};

        let shortestPath = Infinity;

        while (paths.length > 0) {
            paths.sort((a, b) => {
                return b.steps.length - a.steps.length;
            });

            const current = paths.pop();

            const x = current.x, y = current.y;

            if (current.steps.length > highest || current.steps.length >= shortestPath) {
                continue;
            }

            if (`${x}:${y}` === Map.cheat) {
                current.cheated = true;
            }

            if (x === Map.end.x && y === Map.end.y) {
                if (shortestPath === Infinity || current.steps.length < Map.path.length) {
                    Map.path = current.steps;

                    if (current.cheated) {
                        Map.cheats.push(current.steps);
                    }

                    shortestPath = current.steps.length;
                }
            }

            const neighbors = Map.graph[`${x}:${y}`];

            if (!neighbors) {
                continue;
            }

            for (const neighbor of neighbors) {
                if (current.steps.includes(neighbor)) {
                    continue;
                }

                if (seen[neighbor] && seen[neighbor] <= current.steps.length + 1) {
                    continue;
                }

                if (current.cheated && Map.cheatExits[neighbor]) {
                    const steps = current.steps.concat(Map.cheatExits[neighbor]);

                    if (shortestPath === Infinity || steps.length < Map.path.length) {
                        Map.path = steps;

                        if (current.cheated) {
                            Map.cheats.push(steps);
                        }

                        shortestPath = steps.length;
                    }

                    continue;
                }

                const neighborXY = neighbor.split(':').map(Number);

                paths.push({
                    x      : neighborXY[0],
                    y      : neighborXY[1],
                    steps  : current.steps.concat(neighbor),
                    cheated: current.cheated,
                });

                seen[neighbor] = current.steps.length + 1;
            }

            if (current.cheated && shortestPath !== Infinity) {
                break;
            }
        }

        return shortestPath;
    }

    MakeGraph();

    const noCheatPathLength = FindPath();

    for (const path of Map.path) {
        Map.cheatExits[path] = Map.path.slice(Map.path.indexOf(path));
    }

    let PathLengths = {};

    const numCheats = Object.keys(Map.cheatPoints).length;
    let cheatNum    = 0;

    for (const cheatPoint in Map.cheatPoints) {
        process.stdout.write(clc.erase.line);
        process.stdout.write(clc.move.lineBegin);

        cheatNum++;

        process.stdout.write(`Checking cheat point ${cheatPoint}... (${cheatNum}/${numCheats})`);

        Map.cheat = cheatPoint;

        MakeGraph();

        const cheatPathLength = FindPath(noCheatPathLength);
        const savings         = noCheatPathLength - cheatPathLength;

        if (!PathLengths[savings]) {
            PathLengths[savings] = 1;
        }
        else {
            PathLengths[savings]++;
        }
    }

    process.stdout.write(clc.erase.line);
    process.stdout.write(clc.move.lineBegin);

    for (const savings in PathLengths) {
        console.log(`There are ${PathLengths[savings]} cheat${PathLengths[savings] === 1 ? '' : 's'} that save ${savings} picoseconds.`);
    }

    // Find the lowest cheat path for printing.
    Map.path = Map.cheats.sort((a, b) => {
        return a.length - b.length;
    })[0];

    Map.cheat =Map.path.filter((step) => Map.cheatPoints[step] !== undefined)[0];

    PrintMap(Map);

    let atLeast = 0;

    Object.keys(PathLengths).forEach((savings) => {
        if (savings >= 100) {
            atLeast += PathLengths[savings];
        }
    });

    console.log(`There are ${atLeast} cheats that save at least 100 picoseconds.`);

    return true;
}
