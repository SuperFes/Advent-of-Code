export default function part1(lines = []) {
    let Product = 0;

    let copy = true;

    lines.map((line, i) => {
        let LineProduct = 0;

        if (line.length === 0) {
            return;
        }

        let newLine = "";

        const parts = line.split(/(don't\(\)|do\(\))/);

        parts.map((part, i) => {
            if (part === "don't()") {
                newLine += "-=[" + part + ']=-';

                copy = false;

                return;
            }
            else if (part === "do()") {
                newLine += "-=[" + part + ']=-';

                copy = true;

                return;
            }

            if (copy) {
                const muls = part.match(/mul\((\d{1,3}),(\d{1,3})\)/g);

                if (muls) {
                    muls.map((mul, i) => {
                        const digits = mul.match(/\d+/g);

                        const left  = parseInt(digits[0]);
                        const right = parseInt(digits[1]);

                        LineProduct += left * right;
                    });

                    newLine += muls.join(' ');
                }
                else if (part.length) {
                    newLine += "-=[" + part + ']=-';
                }
            }
        });

        Product += LineProduct;
    });

    console.log(`Product is: ${Product}`);

    return true;
}
