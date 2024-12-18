import PrintMap from './lib/PrintMap.js';

export default function RunDown(Map) {
    for (let i = 0; i < Map.runTime; i++) {
        Map.bytes[i].fallen = true;
        Map.bytes[i].time = i;
        Map.time = i;
    }

    function MakeGraph() {
        for (let y = 0; y <= Map.bounds.height; y++) {
            for (let x = 0; x <= Map.bounds.width; x++) {
                const byte = Map.bytes.filter(byte => byte.x === x && byte.y === y && byte.time <= Map.time).length;

                if (byte) {
                    continue;
                }

                const left  = x > 0 && Map.bytes.filter(byte => byte.x === x - 1 && byte.y === y && byte.time <= Map.time).length === 0;
                const right = x <= Map.bounds.width && Map.bytes.filter(byte => byte.x === x + 1 && byte.y === y && byte.time <= Map.time).length === 0;
                const up    = y > 0 && Map.bytes.filter(byte => byte.x === x && byte.y === y - 1 && byte.time <= Map.time).length === 0;
                const down  = x <= Map.bounds.height && Map.bytes.filter(byte => byte.x === x && byte.y === y + 1 && byte.time <= Map.time).length === 0;

                if (!left && !right && !up && !down) {
                    continue;
                }

                Map.graph[`${x}:${y}`] = Map.graph[`${x}:${y}`] || [];

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

    MakeGraph();

    let paths = [{
        x: 0,
        y: 0,
        steps: [`0,0`],
    }];

    let seen = {};

    let shortestPath = Infinity;

    function Distance(a) {
        return Math.abs(a.x - Map.bounds.width) + Math.abs(a.y - Map.bounds.height);
    }

    while (paths.length > 0) {
        paths.sort((a, b) => {
            if (Distance(a) === Distance(b)) {
                return b.steps.length - a.steps.length;
            }

            return Distance(b) - Distance(a);
        });

        const current = paths.pop();

        const x = current.x, y = current.y;

        if (x === Map.bounds.width && y === Map.bounds.height) {
            if (shortestPath === Infinity || current.steps.length < Map.path.length) {
                Map.path = current.steps;

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

            if (current.steps.length >= shortestPath) {
                continue;
            }

            if (seen[neighbor] && seen[neighbor] <= current.steps.length + 1) {
                continue;
            }

            const neighborXY = neighbor.split(':').map(Number);

            paths.push({
                x: neighborXY[0],
                y: neighborXY[1],
                steps: current.steps.concat(neighbor),
            });

            seen[neighbor] = current.steps.length + 1;
        }
    }

    PrintMap(Map);

    return true;
}
