import {transformWithEsbuild} from "vite";

function printUpdates(Updates, Rules) {
    let MiddlePageNos = 0;
    let PrintedPages = {};

    for (const update of Updates) {
        let printing = true;

        let Seen = {};
        let BrokenRules = 0;

        for (let k = 0; k < update.length; k++) {
            const diggggits = update[k];

            Seen[diggggits] = k; // izzle
        }

        for (const rule in Rules) {
            const curRule = Seen[rule];

            if (curRule === undefined) {
                continue;
            }

            for (const gits of Rules[rule]) {
                if (Seen[gits] === undefined) {
                    continue;
                }

                if (Seen[rule] > Seen[gits]) {
                    printing = false;

                    BrokenRules++;

                    break;
                }
            }

            if (!printing) {
                break;
            }
        }

        if (printing) {
            const mid = update[Math.floor(update.length / 2)];

            if (PrintedPages[mid] === undefined) {
                PrintedPages[mid] = 1;
            }
            else {
                PrintedPages[mid]++;
            }
        }
    }

    for (const mid of Object.keys(PrintedPages)) {
        MiddlePageNos += parseInt(mid) * PrintedPages[mid];
    }

    return MiddlePageNos;
}

export default function PCLoadLetter(lines = []) {
    let Middles = 0;

    let Rules   = {};
    let Updates = [];

    let DoingRules = true;

    for (const line of lines) {
        if (line.length === 0) {
            DoingRules = false;

            continue;
        }

        if (DoingRules) {
            const Rule = line.split('|');

            if (Rules[Rule[0]] === undefined) {
                Rules[Rule[0]] = [Rule[1]];
            }
            else {
                Rules[Rule[0]].push(Rule[1]);
            }
        }
        else {
            const Update = line.split(',');

            Updates.push(Update);
        }
    }

    Middles = printUpdates(Updates, Rules);

    console.log(`Middles: ${Middles}`);

    return true;
}
