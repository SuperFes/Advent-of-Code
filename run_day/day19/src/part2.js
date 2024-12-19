import PrintTowels from './lib/PrintTowels.js';
import clc         from 'cli-color';

export default function PatternInlay(TowelsConfig) {
    let TotalCombinations = 0;
    let Towel = 1;

    for (const towel of TowelsConfig.designs) {
        let towelMap = new Map();

        function MapPatterns(pattern, available) {
            if (towelMap.has(pattern)) {
                return towelMap.get(pattern);
            }

            if (pattern.length === 0) {
                return 1;
            }

            const count = available.filter((avail) => pattern.startsWith(avail)).reduce((a, b) => a + MapPatterns(pattern.substring(b.length), available), 0);

            towelMap.set(pattern, count);

            return count;
        }

        const count = MapPatterns(towel.string, TowelsConfig.available);

        if (count > 0) {
            towel.impossible = false;
        }

        towel.completed = count;

        // console.log(towel);

        // break;

        Towel++;
    }

    process.stdout.write(clc.erase.line);
    process.stdout.write(clc.move.lineBegin);

    PrintTowels(TowelsConfig.designs);

    const PossibleDesigns = TowelsConfig.designs.filter(design => !design.impossible).map(design => design.completed).reduce((a, b) => a + b, 0);

    console.log(`Possible arrangements: ${PossibleDesigns} out of ${TowelsConfig.designs.length}.`);

    return true;
}
