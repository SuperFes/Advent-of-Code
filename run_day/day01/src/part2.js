export default function part2(lines = []) {
    let Left  = [];
    let Right = [];
    let Sims  = {};

    lines.map((line, i) => {
        if (line.length === 0) {
            return;
        }

        let strMatches = line.match(/(\d+)\s+(\d+)/);

        let ml = strMatches[1], mr = strMatches[2];

        Left[Left.length]   = parseInt(ml);
        Right[Right.length] = parseInt(mr);
    });

    Left.sort();
    Right.sort();

    Right.map((val, i) => {
        if (Sims[val] !== undefined) {
            Sims[val]++;
        }
        else {
            Sims[val] = 1;
        }
    });

    const Length = Left.length;

    let Similarity = 0;

    for (let i = 0; i < Length; i++) {
        if (Sims[Left[i]] !== undefined) {
            Similarity += Left[i] * Sims[Left[i]];
        }
    }

    console.log(`The similarity score is: ${Similarity}`);

    return true;
}
