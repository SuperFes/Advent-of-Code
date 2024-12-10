function trampies(lines, X, Y, dirX, dirY) {
    let str = '';

    let oX = X, oY = Y;

    for (let f = 0; f < 4; f++) {
        if (lines[oY] !== undefined && lines[oY][oX] !== undefined) {
            str += lines[oY][oX];
        }
        else {
            // No need to go deeper if they are undefined no matter how far we've gone
            break;
        }

        oX += dirX;
        oY += dirY;
    }

    if (str.length === 4) {
        return str === 'XMAS';
    }

    return false;
}

function trampStamp(lines, X, Y) {
    let Matches = 0;

    const dirs = [
        [1, 0],
        [-1, 0],
        [0, 1],
        [0, -1],
        [1, 1],
        [1, -1],
        [-1, 1],
        [-1, -1],
    ];

    dirs.map((dir, i) => {
        if (trampies(lines, X, Y, dir[0], dir[1])) {
            Matches++;
        }
    });

    return Matches;
}

function scannerXXX(lines) {
    let Hits = 0;

    const height = lines.length;
    const width  = lines[0].length;

    for (let x = 0; x < width; x++) {
        for (let y = 0; y < height; y++) {
            Hits += trampStamp(lines, x, y);
        }
    }

    return Hits;
}

export default function XMAS(lines = []) {
    const Tramps = scannerXXX(lines);

    console.log(`Number of XMAS is: ${Tramps}`);

    return true;
}
