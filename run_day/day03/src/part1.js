export default function Visit(lines = []) {
    let Product = 0;

    lines.map((line, i) => {
        if (line.length === 0) {
            return;
        }

        let strMatches = line.match(/mul\((\d{1,3}),(\d{1,3})\)/g);

        strMatches.map((match) => {
            const digits = match.match(/\d+/g);

            const left = parseInt(digits[0]);
            const right = parseInt(digits[1]);

            Product += left * right;
        });
    });

    console.log(`Product is: ${Product}`);

    return true;
}
