import PrintMap              from './lib/PrintMap.js';
import {createFilterOptions} from "@mui/material";

export default function ReindeerInAHalfShell(Map) {
    let Cheapest = Infinity;
    let CheapestPath = [];
    let CheapestPaths = [];

    const startX = Map.start.x;
    const startY = Map.start.y;

    const start = `${startX},${startY}`;

    const finishX = Map.finish.x;
    const finishY = Map.finish.y;

    const finish = `${finishX},${finishY}`;

    const turnCost = 1000;
    const stepCost = 1;

    let paths = [];

    for (const dir in Map.paths[start].ajd) {
        let cost = 0;

        if (dir !== 'right') {
            cost += turnCost;

            if (dir === 'left') {
                cost += turnCost;
            }
        }

        paths.push({
                node : start,
                dir  : dir,
                cost : cost,
                steps: [`${Map.paths[start].x},${Map.paths[start].y}`],
            }
        );

        Map.paths[start].used[dir] = true;
    }

    while (paths.length > 0) {
        let path = paths.shift();

        let dir = path.dir;

        let pathData = Map.paths[path.node];

        let x = pathData.x;
        let y = pathData.y;

        let cost = path.cost;

        if (cost - 1000 > pathData.cost) {
            continue;
        }

        if (x === finishX && y === finishY) {
            if (cost < Cheapest) {
                // console.debug(path.steps);
                Cheapest = cost;
                CheapestPath = path.steps;
                CheapestPaths.push({cost: cost, steps: path.steps});
            }

            continue;
        }

        let up    = pathData.ajd['up'];
        let down  = pathData.ajd['down'];
        let left  = pathData.ajd['left'];
        let right = pathData.ajd['right'];

        if (up !== undefined && (!up.used.up || up.cost >= cost)) {
            let newCost = cost + stepCost;

            if (dir === 'down') {
                newCost += turnCost * 2;
            }
            else if (dir !== 'up') {
                newCost += turnCost;
            }

            up.cost = newCost;

            paths.push({
                    node : `${up.x},${up.y}`,
                    dir  : 'up',
                    cost : up.cost,
                    steps: path.steps.concat(`${up.x},${up.y}`),
                }
            );

            up.used.up = true;
        }

        if (down !== undefined && (!down.used.down || down.cost >= cost)) {
            let newCost = cost + stepCost;

            if (dir === 'up') {
                newCost += turnCost * 2;
            }
            else if (dir !== 'down') {
                newCost += turnCost;
            }

            down.cost = newCost;

            paths.push({
                    node : `${down.x},${down.y}`,
                    dir  : 'down',
                    cost : down.cost,
                    steps: path.steps.concat(`${down.x},${down.y}`),
                }
            );

            down.used.down = true;
        }

        if (left !== undefined && (!left.used.left || left.cost >= cost)) {
            let newCost = cost + stepCost;

            if (dir === 'right') {
                newCost += turnCost * 2;
            }
            else if (dir !== 'left') {
                newCost += turnCost;
            }

            left.cost = newCost;

            paths.push({
                    node : `${left.x},${left.y}`,
                    dir  : 'left',
                    cost : left.cost,
                    steps: path.steps.concat(`${left.x},${left.y}`),
                }
            );

            left.used.left = true;
        }

        if (right !== undefined && (!right.used.right || right.cost >= cost)) {
            let newCost = cost + stepCost;

            if (dir === 'left') {
                newCost += turnCost * 2;
            }
            else if (dir !== 'right') {
                newCost += turnCost;
            }

            right.cost = newCost;

            paths.push({
                    node : `${right.x},${right.y}`,
                    dir  : 'right',
                    cost : right.cost,
                    steps: path.steps.concat(`${right.x},${right.y}`),
                }
            );

            right.used.right = true;
        }
    }

    // console.debug(Map);
    // console.debug(Map.paths[finish]);

    let Turns = 0;

    let lastX = null;
    let lastY = null;

    let dir = 'right';

    for (const path of CheapestPath) {
        let [x, y] = path.split(',');

        if (lastX === null) {
            // console.debug(`(${x}, ${y}), dir: ${dir}`);

            lastX = x;
            lastY = y;
        }
        else {
            const xDir = x - lastX;
            const yDir = y - lastY;

            if (xDir === 1 && dir !== 'right') {
                Turns++;
                dir = 'right';
            }
            else if (xDir === -1 && dir !== 'left') {
                Turns++;
                dir = 'left';
            }
            else if (yDir === 1 && dir !== 'down') {
                Turns++;
                dir = 'down';
            }
            else if (yDir === -1 && dir !== 'up') {
                Turns++;
                dir = 'up';
            }

            lastX = x;
            lastY = y;
        }
    }

    // console.debug(`Turns: ${Turns}`);
    // console.debug(`Cheapest path is ${CheapestPath.length} steps long.`);

    // const TotalCost = CheapestPath.length - 1 + Turns * turnCost;

    console.log(`Cheapest path costs ${Cheapest} points.`);

    // console.log(`Total cost: ${TotalCost}.`);

    return true;
}
