import clc from 'cli-color';

export default function PrintTowels(Towels) {
    const R = clc.red.bgXterm(24);
    const G = clc.green.bgXterm(24);
    const U = clc.blue.bgXterm(24);
    const W = clc.white.bgXterm(24);
    const B = clc.black.bgXterm(24);

    for (const Towel of Towels) {
        const TowelColours    = Towel.string.split('');
        const TowelImpossible = Towel.impossible;

        if (TowelImpossible) {
            process.stdout.write(clc.bold.red('Impossible: '));
        }
        else {
            process.stdout.write(clc.bold.green('Possible:   '));
        }

        for (const Colour of TowelColours) {
            const Color = Colour === 'r' ? R :
                          Colour === 'g' ? G :
                          Colour === 'u' ? U :
                          Colour === 'w' ? W :
                          Colour === 'b' ? B : null;
            const TowelChar = '|';

            if (TowelImpossible) {
                process.stdout.write(Color(clc.strike(TowelChar)));
            }
            else {
                process.stdout.write(Color(TowelChar));
            }
        }

        process.stdout.write('\n');
    }
}
