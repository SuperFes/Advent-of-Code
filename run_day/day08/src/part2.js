function drawMap(map) {
    for (let y = 0; y < map.bounds.height; y++) {
        let row = '';

        for (let x = 0; x < map.bounds.width; x++) {
            let char = '.';

            for (const antinode of map.antinodes) {
                if (antinode.x === x && antinode.y === y) {
                    char = '#';

                    break;
                }
            }

            for (const antenna of map.antennae) {
                if (antenna.x === x && antenna.y === y) {
                    char = antenna.id;

                    break;
                }
            }

            row += char;
        }

        console.log(row);
    }
}

function getDistXY(antenna, pair) {
    const distX = Math.abs(antenna.x - pair.x);
    const distY = Math.abs(antenna.y - pair.y);

    return {x: distX, y: distY};
}

function getOppositeDir(antenna, pair, antinode = null) {
    let dist = getDistXY(antenna, pair);

    if (pair.x > antenna.x) {
        dist.x = -dist.x;
    }

    if (pair.y > antenna.y) {
        dist.y = -dist.y;
    }

    if (antinode === null) {
        antinode = {
            x: antenna.x + dist.x,
            y: antenna.y + dist.y,
        };
    }
    else {
        antinode = {
            x: antinode.x + dist.x,
            y: antinode.y + dist.y,
        };
    }

    return antinode;
}

export default function part1(map = {}) {
    for (const antenna of map.antennae) {
        const pairs = map.antennae.filter((otherAntenna) => {
            return otherAntenna.id === antenna.id;
        });

        for (const pair of pairs) {
            if (antenna.x === pair.x && antenna.y === pair.y) {
                continue;
            }

            map.antinodes.push({x: antenna.x, y: antenna.y});

            const distance = getDistXY(antenna, pair);

            let antinode = getOppositeDir(antenna, pair);

            // console.log(`Antenna ${antenna.id} at (${antenna.x}, ${antenna.y}) has a pair at`, distance, antinode);

            map.antinodes.push(antinode);

            while (antinode.x >= 0 && antinode.x < map.bounds.width && antinode.y >= 0 && antinode.y < map.bounds.height) {
                antinode = getOppositeDir(antenna, pair, antinode);

                map.antinodes.push(antinode);
            }
        }
    }

    drawMap(map);

    let paths = {}

    let antinodesInBounds = map.antinodes.filter((antinode) => {
        const path = `${antinode.x}:${antinode.y}`;

        if (paths[path]) {
            return false;
        }

        paths[path] = true;

        return antinode.x >= 0 && antinode.x < map.bounds.width && antinode.y >= 0 && antinode.y < map.bounds.height;
    }).length;

    console.log(`Antinodes in range: ${antinodesInBounds}.`);
}
