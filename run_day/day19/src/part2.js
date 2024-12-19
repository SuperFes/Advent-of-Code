import PrintTowels from './lib/PrintTowels.js';
import clc         from 'cli-color';

export default function PatternInlay(TowelsConfig) {
    let towelMap = new Map();
    let Towel    = 1;

    function MapPatterns(pattern, available) {
        // If we've seen this pattern before, return the count
        if (towelMap.has(pattern)) {
            return towelMap.get(pattern);
        }

        // If the pattern is empty, we are now at the end, we win
        if (pattern.length === 0) {
            return 1;
        }

        // Check for more available patterns, could be 0, if 0 then the count doesn't go up, and we won't be a valid
        // pattern
        const count = available.filter(
            (avail) => pattern.startsWith(avail)
                               )
                               .reduce(
                                   (a, b) =>
                                       a + MapPatterns(pattern.substring(b.length), available),
                                   0);

        // Make sure we save all our work here...
        towelMap.set(pattern, count);

        return count;
    }

    for (const towel of TowelsConfig.designs) {
        const count = MapPatterns(towel.string, TowelsConfig.available);

        if (count > 0) {
            towel.impossible = false;
        }

        towel.completed = count;

        Towel++;
    }

    process.stdout.write(clc.erase.line);
    process.stdout.write(clc.move.lineBegin);

    PrintTowels(TowelsConfig.designs);

    const PossibleDesigns = TowelsConfig.designs.filter(design => !design.impossible).map(design => design.completed).reduce((a, b) => a + b, 0);

    console.log(`Possible arrangements: ${PossibleDesigns} out of ${TowelsConfig.designs.length}.`);

    return true;
}
