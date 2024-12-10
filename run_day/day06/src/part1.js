function walkUntilUndefined(map, Knight) {
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
            if (Knight.dir === 'Up') {
                Knight.y++;
                Knight.x++;

                Knight.dir = 'Right';
            }
            else if (Knight.dir === 'Right') {
                Knight.x--;
                Knight.y++;

                Knight.dir = 'Down';
            }
            else if (Knight.dir === 'Down') {
                Knight.y--;
                Knight.x--;

                Knight.dir = 'Left';
            }
            else {
                Knight.y--;
                Knight.x++;

                Knight.dir = 'Up';
            }
        }
    }
}

export default function TakeAShortWalk(map = []) {
    let MapPoints = 0;

    let Knight = {x: -1, y: -1, found: false, dir: 'Up'};

    for (let y = 0; y < map.length; y++) {
        for (let x = 0; x < map[y].length; x++) {
            if (map[y][x] === '^') {
                Knight.found = true;
                Knight.x = x;
                Knight.y = y;

                break;
            }
        }

        if (Knight.found) {
            break;
        }
    }

    walkUntilUndefined(map, Knight);

    for (let y = 0; y < map.length; y++) {
        for (let x = 0; x < map[y].length; x++) {
            if (map[y][x] === 'X') {
                MapPoints++;
            }
        }
    }

    console.log(`Blocks walked by the knight: ${MapPoints}`);

    return true;
}
