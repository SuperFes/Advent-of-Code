export default function CheckingTheList(machines = {}) {
    let TotalTokens = 0;

    for (const machine of machines) {
        const name = machine['Name'];

        console.log(name);

        const instructions = machine;

        const ButtonA = instructions['Button A'];
        const ButtonB = instructions['Button B'];

        const Prize = instructions['Prize'];

        const XDest = Prize['X'].value;
        const YDest = Prize['Y'].value;

        const AXValue = ButtonA['X'].value;
        const AYValue = ButtonA['Y'].value;

        const BXValue = ButtonB['X'].value;
        const BYValue = ButtonB['Y'].value;

        let CurrentTokens = 0;

        for (let a = 1; a <= 100; a++) {
            for (let b = 100; b > 0; b--) {
                if (a * AXValue + b * BXValue === XDest && a * AYValue + b * BYValue === YDest) {
                    const tokens = a * ButtonA['Cost'] + b * ButtonB['Cost'];

                    if (CurrentTokens === 0 || tokens < CurrentTokens) {
                        CurrentTokens = tokens;
                    }
                }
            }
        }

        TotalTokens += CurrentTokens;
    }

    console.log(`Total tokens spent: ${TotalTokens}`);

    return true;
}
