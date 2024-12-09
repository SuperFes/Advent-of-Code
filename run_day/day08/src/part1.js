function getDistXY(antenna, pair) {
    const distX = Math.abs(antenna.x - pair.x);
    const distY = Math.abs(antenna.y - pair.y);

    return {x: distX, y: distY};
}

function getOppositeDir(antenna, pair) {
    let dist = getDistXY(antenna, pair);

    if (pair.x > antenna.x) {
        dist.x = -dist.x;
    }

    if (pair.y > antenna.y) {
        dist.y = -dist.y;
    }

    const antinode = {
        x: antenna.x + dist.x,
        y: antenna.y + dist.y,
    };

    return antinode;
}

export default function part1(map = {}) {
    // console.debug(map);

    for (const antenna of map.antennae) {
        const pairs = map.antennae.filter((otherAntenna) => {
            return otherAntenna.id === antenna.id;
        });

        for (const pair of pairs) {
            if (antenna.x === pair.x && antenna.y === pair.y) {
                continue;
            }

            const distance = getDistXY(antenna, pair);

            const antinode = getOppositeDir(antenna, pair);

            // console.log(`Antenna ${antenna.id} at (${antenna.x}, ${antenna.y}) has a pair at`, distance, antinode);

            map.antinodes.push(antinode);
        }
    }

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
