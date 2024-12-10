function trampies(lines, X, Y) {
    let str = '';

    if (lines[Y - 1] !== undefined && lines[Y - 1][X - 1] !== undefined) {
        str += lines[Y - 1][X - 1];

        if (lines[Y - 1] !== undefined && lines[Y - 1][X + 1] !== undefined) {
            str += lines[Y - 1][X + 1];

            str += lines[Y][X];

            if (lines[Y + 1] !== undefined && lines[Y + 1][X - 1] !== undefined) {
                str += lines[Y + 1][X - 1];

                if (lines[Y + 1] !== undefined && lines[Y + 1][X + 1] !== undefined) {
                    str += lines[Y + 1][X + 1];
                }
            }
        }
    }

    if (str.length === 5) {
        return (
            str === 'MSAMS' ||
            str === 'MMASS' ||
            str === 'SMASM' ||
            str === 'SSAMM'
        );
    }

    return false;
}

function trampStamp(lines, X, Y) {
    let Matches = 0;

    if (trampies(lines, X, Y)) {
        Matches++;
    }

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

export default function XedMAS(lines = []) {
    const Tramps = scannerXXX(lines);

    console.log(`Number of X-MAS is: ${Tramps}`);

    return true;
}
