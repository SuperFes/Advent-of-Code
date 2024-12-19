import PrintTowels from './lib/PrintTowels.js';

export default function PatternInlay(TowelsConfig) {
    for (const towel of TowelsConfig.designs) {
        let paths = [];

        for (const available of TowelsConfig.available) {
            if (towel.string.startsWith(available)) {
                paths.push({
                    goal   : towel.string,
                    current: towel.string.substring(available.length),
                    steps  : [available],
                });
            }
        }

        while (paths.length) {
            paths.sort((a, b) => b.steps.length - a.steps.length);

            const path = paths.shift();

            const completedTowel = path.steps.join('');

            // console.log(paths.length, completedTowel, completedTowel.length, towel.string.length);

            if (completedTowel.length > towel.string.length) {
                continue;
            }

            if (completedTowel === path.goal) {
                towel.impossible = false;

                break;
            }

            for (const available of TowelsConfig.available) {
                if (path.current.startsWith(available)) {
                    paths.push({
                        goal   : path.goal,
                        current: path.current.substring(available.length),
                        steps  : path.steps.concat(available),
                    });
                }
            }
        }

        // console.log(towel);

        // break;
    }

    PrintTowels(TowelsConfig.designs);

    const PossibleDesigns = TowelsConfig.designs.filter(design => !design.impossible).length;

    console.log(`Possible designs: ${PossibleDesigns} out of ${TowelsConfig.designs.length}.`);

    return true;
}
