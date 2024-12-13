import nerdamer from 'nerdamer/all.js';

export default function CheckingTheList(machines = {}) {
    let TotalTokens = 0;

    nerdamer.set('SOLUTIONS_AS_OBJECT', true);

    for (const machine of machines) {
        const name = machine['Name'];

        console.log(name);

        const instructions = machine;

        const ButtonA = instructions['Button A'];
        const ButtonB = instructions['Button B'];

        const Prize = instructions['Prize'];

        const XDest = Prize['X'].value + 10_000_000_000_000;
        const YDest = Prize['Y'].value + 10_000_000_000_000;

        const AXValue = ButtonA['X'].value;
        const AYValue = ButtonA['Y'].value;

        const BXValue = ButtonB['X'].value;
        const BYValue = ButtonB['Y'].value;

        const Solve = nerdamer.solveEquations([
                `${AXValue} * a + ${BXValue} * b = ${XDest}`,
                `${AYValue} * a + ${BYValue} * b = ${YDest}`,
            ],
            ['a', 'b']
        );

        if (Solve.a.toString().includes('.') || Solve.b.toString().includes('.')) {
            console.log(`No solution found =(`);
        }
        else {
            const ACost = ButtonA['Cost'] * Solve.a;
            const BCost = ButtonB['Cost'] * Solve.b;

            console.log(`A: ${Solve.a}, B: ${Solve.b}`);

            TotalTokens += ACost + BCost;
        }
    }

    console.log(`Total tokens spent: ${TotalTokens}`);

    return true;
}
