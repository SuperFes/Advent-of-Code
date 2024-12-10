export default function CheckingTheList(lines = []) {
    let Left = [];
    let Right = [];

    lines.map((line, i) => {
        if (line.length === 0) {
            return;
        }

        let strMatches = line.match(/(\d+)\s+(\d+)/);

        let ml = strMatches[1], mr = strMatches[2];

        Left[Left.length] = parseInt(ml);
        Right[Right.length] = parseInt(mr);
    });

    Left.sort();
    Right.sort();

    const Length = Left.length;

    let Distance = 0;

    for (let i = 0; i < Length; i++) {
        Distance += Math.abs(Right[i] - Left[i]);
    }

    console.log(`The distance from left and right is: ${Distance}`);

    return true;
}
