import PrintMap from './lib/PrintMap.js';
import clc      from "cli-color";

export default function MutexLockerator(Map) {
    const MaxCheatSteps = 20;

    function MakeGraph() {
        Map.graph = {};

        for (let y = 0; y < Map.bounds.height; y++) {
            for (let x = 0; x < Map.bounds.width; x++) {
                if (!Map.grid[`${x}:${y}`]) {
                    continue;
                }

                const left  = Map.grid[`${x - 1}:${y}`];
                const right = Map.grid[`${x + 1}:${y}`];
                const up    = Map.grid[`${x}:${y - 1}`];
                const down  = Map.grid[`${x}:${y + 1}`];

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

        // console.log(Map.graph);
        // console.debug(Map);
    }

    function MakeWallGraph() {
        Map.wallGraph = {};

        for (let y = 0; y < Map.bounds.height; y++) {
            for (let x = 0; x < Map.bounds.width; x++) {
                const left  = Map.walls[`${x - 1}:${y}`] ?? Map.grid[`${x - 1}:${y}`];
                const right = Map.walls[`${x + 1}:${y}`] ?? Map.grid[`${x + 1}:${y}`];
                const up    = Map.walls[`${x}:${y - 1}`] ?? Map.grid[`${x}:${y - 1}`];
                const down  = Map.walls[`${x}:${y + 1}`] ?? Map.grid[`${x}:${y + 1}`];

                if (!left && !right && !up && !down) {
                    continue;
                }

                if (!Map.wallGraph[`${x}:${y}`]) {
                    Map.wallGraph[`${x}:${y}`] = [];
                }

                if (left) {
                    Map.wallGraph[`${x}:${y}`].push(`${x - 1}:${y}`);
                }

                if (right) {
                    Map.wallGraph[`${x}:${y}`].push(`${x + 1}:${y}`);
                }

                if (up) {
                    Map.wallGraph[`${x}:${y}`].push(`${x}:${y - 1}`);
                }

                if (down) {
                    Map.wallGraph[`${x}:${y}`].push(`${x}:${y + 1}`);
                }
            }
        }

        // console.debug(Map);
    }

    function Distance(a, b) {
        return Math.abs(a.x - b.x) + Math.abs(a.y - b.y);
    }

    function FindPath(startPath = null, endPath = null) {
        let startXY = [Map.start.x, Map.start.y];
        let endXY   = [Map.end.x, Map.end.y];

        if (startPath) {
            startXY = startPath.split(':').map(Number);
        }

        if (endPath) {
            endXY = endPath.split(':').map(Number);
        }

        console.log(`Finding path from ${startXY} to ${endXY}`);

        let paths = [{
            x    : startXY[0],
            y    : startXY[1],
            steps: [`${startXY[0]}:${startXY[1]}`],
        }];

        let seen = {};

        let shortestPath = Infinity;

        while (paths.length > 0) {
            paths.sort((a, b) => {
                return b.steps.length - a.steps.length;
            });

            const current = paths.pop();

            let x = current.x;
            let y = current.y;

            const stepCount = current.steps.length - 1;

            if (stepCount >= shortestPath) {
                continue;
            }

            if (x === endXY[0] && y === endXY[1]) {
                if (current.steps.length < shortestPath) {
                    shortestPath = current.steps.length;

                    Map.path = current.steps;
                }

                continue;
            }

            const neighbors = Map.graph[`${x}:${y}`];

            if (!neighbors) {
                continue;
            }

            for (const neighbor of neighbors) {
                if (current.steps.includes(neighbor)) {
                    continue;
                }

                if ((seen[neighbor] && seen[neighbor] < stepCount)) {
                    continue;
                }

                const steps = current.steps.concat(neighbor);

                seen[neighbor] = steps.length - 1;

                const neighborXY = neighbor.split(':').map(Number);

                paths.push({
                    x         : neighborXY[0],
                    y         : neighborXY[1],
                    steps     : steps,
                    cheated   : current.cheated,
                    cheatStart: current.cheatStart,
                    cheatEnd  : current.cheatEnd,
                });
            }
        }

        return Map.path;
    }

    function ManhattenDistance(start, end) {
        const a = start.split(':').map(Number);
        const b = end.split(':').map(Number);

        return Math.abs(a[0] - b[0]) + Math.abs(a[1] - b[1]);
    }

    function WalkUntil(path, pathEnd) {
        const pathStartXY = path.split(':').map(Number);
        const pathEndXY   = pathEnd.split(':').map(Number);

        let x = pathStartXY[0];
        let y = pathStartXY[1];

        let steps = [`${x}:${y}`];

        const xDist = pathEndXY[0] - x;
        const yDist = pathEndXY[1] - y;

        const xMove = xDist === 0 ? 0 : xDist / Math.abs(xDist);
        const yMove = yDist === 0 ? 0 : yDist / Math.abs(yDist);

        while (x !== pathEndXY[0]) {
            x += xMove;

            steps.push(`[${x}:${y}]`);
        }

        while (y !== pathEndXY[1]) {
            y += yMove;

            steps.push(`[${x}:${y}]`);
        }

        return steps;
    }

    function FindExits(startPath) {
        const [startX, startY] = startPath.split(':').map(Number);

        let exits = [];

        // console.log('Finding exits for:', startPath, startX, startY);

        for (let y = startY - 21; y < startY + 21; y++) {
            for (let x = startX - 21; x < startX + 21; x++) {
                const loc = `${x}:${y}`;

                if (loc !== startPath && Map.grid[loc] && ManhattenDistance(startPath, loc) <= 20) {
                    exits.push(loc);
                }
            }
        }

        // console.debug('exits', exits.length);

        return exits;
    }

    MakeGraph();

    MakeWallGraph();

    const noCheatPathLength = FindPath().length;

    PrintMap(Map);

    let PathToEnd = [];

    for (let i = 0; i < Map.path.length; i++) {
        const path = Map.path.slice(i);

        PathToEnd.push(path);
    }

    let PathLengths = {};

    let Found = 0;
    let LastPrint = new Date().getTime();

    const PathLength = Map.path.length - 1;

    Map.path.map((pathStart, i) => {
        const [startX, startY] = pathStart.split(':').map(Number);

        if (startX === Map.end.x && startY === Map.end.y) {
            return;
        }

        const exits = FindExits(pathStart);

        for (const exit of exits) {
            const startPath = Map.path.slice(0, i);
            const endPath   = PathToEnd[Map.path.indexOf(exit) + 1] || [];

            const path = WalkUntil(pathStart, exit);

            if (path.length === 0) {
                continue;
            }

            // console.log('Exit: ', exit, Map.path.indexOf(exit));
            // console.log('Paths', startPath, path, endPath);

            const completePath = startPath.concat(path, endPath);

            const cheatLength = completePath.length;

            if (cheatLength > noCheatPathLength || noCheatPathLength - cheatLength < 100) {
                continue;
            }

            // console.log('Complete Path', completePath, cheatLength);
            // const mapPath = Map.path;
            // Map.path = completePath;
            // PrintMap(Map);
            // Map.path = mapPath;

            Map.uniqueCheats[`${pathStart}<->${exit}`] = cheatLength;

            if (Map.shortestCheat.length === 0 || cheatLength < Map.shortestCheat.length) {
                Map.shortestCheat = completePath;
            }

            Found++;

            if (LastPrint + 150 < new Date().getTime()) {
                process.stdout.write(clc.erase.line);
                process.stdout.write(clc.move.lineBegin);
                process.stdout.write(`${((i/PathLength)*100).toLocaleString()}% Complete. ${pathStart}(${i}/${PathLength}): ${noCheatPathLength - cheatLength} picoseconds (${Found} found).`);

                LastPrint = new Date().getTime();
            }
        }

        return null;
    });

    for (const cheat in Map.uniqueCheats) {
        const cheatLength = Map.uniqueCheats[cheat];
        const saved = noCheatPathLength - cheatLength;

        if (saved <= 0) {
            continue;
        }

        PathLengths[saved] = PathLengths[saved] ? PathLengths[saved] + 1 : 1;
    }

    process.stdout.write(clc.erase.line);
    process.stdout.write(clc.move.lineBegin);

    let minSave = 50;

    if (Map.path.length > 4000) {
        minSave = 100;
    }

    // Find the lowest cheat path for printing.
    Map.path = Map.shortestCheat;

    PrintMap(Map);

    process.stdout.write('\n');

    let atLeast = 0;

    Object.keys(PathLengths).forEach((savings) => {
        if (savings >= minSave) {
            console.log(`There ${PathLengths[savings] === 1 ? 'is' : 'are'} ${PathLengths[savings] === 1 ? 'one' : PathLengths[savings]} cheat${PathLengths[savings] === 1 ? '' : 's'} that save${PathLengths[savings] === 1 ? 's' : ''} ${savings} picoseconds.`);

            atLeast += PathLengths[savings];
        }
    });

    console.log(`\nThere are ${atLeast} cheats that save at least ${minSave} picoseconds.\n`);

    return true;
}
