function printMap(map, knight, char = 'K') {
    if (!map[knight.y] || !map[knight.y][knight.x]) {
        return;
    }

    let lines = [];

    const origChar = map[knight.y][knight.x];

    map[knight.y][knight.x] = char;

    for (const row of map) {
        lines.push(row.join(''));
    }

    map[knight.y][knight.x] = origChar;

    console.debug(lines.join('\n'));
}

function turn(dude) {
    if (dude.dir === 'Up') {
        dude.y++;

        dude.dir = 'Right';
    }
    else if (dude.dir === 'Right') {
        dude.x--;

        dude.dir = 'Down';
    }
    else if (dude.dir === 'Down') {
        dude.y--;

        dude.dir = 'Left';
    }
    else {
        dude.x++;

        dude.dir = 'Up';
    }
}

function loopThatBitch(map, knight, startX, startY) {
    let ghost = {x: knight.x, y: knight.y, dir: knight.dir};

    let turns     = 0;
    const origDir = ghost.dir;

    let looped = false;

    const oldTile = map[startY][startX];

    map[startY][startX] = '@';

    let path = {};

    while (map[ghost.y] && map[ghost.y][ghost.x]) {
        if (ghost.dir === 'Up') {
            ghost.y--;
        }
        else if (ghost.dir === 'Right') {
            ghost.x++;
        }
        else if (ghost.dir === 'Down') {
            ghost.y++;
        }
        else {
            ghost.x--;
        }

        const mapChar = (map[ghost.y] && map[ghost.y][ghost.x]) ? map[ghost.y][ghost.x] : false;

        if (!mapChar) {
            break;
        }

        const pathStr = `${ghost.dir}-${ghost.x}x${ghost.y}`;

        if (path[pathStr]) {
            looped = true;

            break;
        }

        path[pathStr] = turns;

        if (mapChar === '#' || mapChar === '@') {
            turns++;

            turn(ghost);
        }
    }

    map[startY][startX] = oldTile;

    return looped;
}

function isCorrectTriangle(a, b, c) {
    const side1      = manhattanDistance(a, b);
    const side2      = manhattanDistance(a, c);
    const hypotenuse = manhattanDistance(b, c);

    return side1 ^ 2 + side2 ^ 2 === hypotenuse ^ 2;
}

function manhattanDistance(src, dest) {
    return Math.abs(src.x - dest.x) + Math.abs(src.y - dest.y);
}

function walkUntilUndefined(map, Knight) {
    let Squares = 0;
    let Bumps   = [];

    while (map[Knight.y] && map[Knight.y][Knight.x]) {
        map[Knight.y][Knight.x] = 'X';

        if (Knight.dir === 'Up') {
            Knight.y--;
        }
        else if (Knight.dir === 'Right') {
            Knight.x++;
        }
        else if (Knight.dir === 'Down') {
            Knight.y++;
        }
        else {
            Knight.x--;
        }

        if (map[Knight.y] && map[Knight.y][Knight.x] && map[Knight.y][Knight.x] === '#') {
            Bumps.push({x: Knight.x, y: Knight.y});

            turn(Knight);
        }
    }

    return Squares;
}

export default function part2(map = []) {
    let squares = 0;

    let Knight = {x: -1, y: -1, found: false, dir: 'Up'};
    let foundX, foundY;

    for (let y = 0; y < map.length; y++) {
        for (let x = 0; x < map[y].length; x++) {
            if (map[y][x] === '^') {
                foundX = x;
                foundY = y;

                Knight.found = true;

                break;
            }
        }

        if (Knight.found) {
            break;
        }
    }

    Knight.x   = foundX;
    Knight.y   = foundY;
    Knight.dir = 'Up';

    walkUntilUndefined(map, Knight);

    for (let x = 0; x < map[0].length; x++) {
        for (let y = 0; y < map.length; y++) {
            if (map[y][x] === 'X') {
                Knight.x   = foundX;
                Knight.y   = foundY;
                Knight.dir = 'Up';

                if (loopThatBitch(map, Knight, x, y)) {
                    squares++;
                }
            }
        }
    }

    console.log(`Possible squares: ${squares}`);

    return true;
}
