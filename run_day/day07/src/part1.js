function mathIt(test, els, math, currentPath = "") {
    if (currentPath === "") {
        const thisEl = els.shift();

        currentPath = `${thisEl}`;
    }

    const nextEl = els.shift();

    for (const op of math) {
        let copyCurrentPath = `(${currentPath} ${op} ${nextEl})`;

        if (op === '.') {
            copyCurrentPath = `Number(Number(${currentPath}).toString() + Number(${nextEl}).toString())`;
        }

        if (els.length === 0) {
            const calc = eval(copyCurrentPath);

            if (calc === test) {
                // console.debug(`Found: ${copyCurrentPath}`, test);

                return test;
            }
            else {
                // console.debug(`Not found: ${copyCurrentPath}`, test, calc);
            }
        }
        else {
            let copyEls = [...els];

            if (mathIt(test, copyEls, math, copyCurrentPath) === test) {
                return test;
            }
        }
    }

    return 0;
}

export default function RopeADope(ops = []) {
    let possibleOps = 0;

    for (const val in ops) {
        const test = parseInt(val.split(': ')[0]);

        possibleOps += mathIt(test, ops[val], ['+', '*']);
    }

    console.log(`The valuation is: ${possibleOps}.`);
}
