export default function Fusion(lines = []) {
    let Dir   = 0;
    let Safe  = 0;
    let Level = 0;

    lines.map((line, i) => {
        if (line.length === 0) {
            return;
        }

        const Levels = line.split(' ');

        let Safety       = 0;
        let CurrentLevel = 0;
        let CurrentDir   = 0;

        Levels.map((level, i) => {
            const newLevel = parseInt(level);

            if (CurrentLevel === 0) {
                CurrentLevel = newLevel;

                Safety++;

                return;
            }

            const levelDiff = Math.abs(newLevel - CurrentLevel);
            const safeDiff  = levelDiff <= 3 && levelDiff >= 1;

            if (safeDiff) {
                if (CurrentDir === 0 && newLevel !== CurrentLevel) {
                    if (newLevel > CurrentLevel) {
                        CurrentDir = 1;
                    }
                    else {
                        CurrentDir = -1
                    }

                    Safety++;
                }
                else if (CurrentDir === -1 && newLevel < CurrentLevel) {
                    Safety++;
                }
                else if (CurrentDir === 1 && newLevel > CurrentLevel) {
                    Safety++;
                }
            }

            CurrentLevel = newLevel;
        });

        if (Safety === Levels.length) {
            Safe++;

            Dir += CurrentDir;
            Level += CurrentLevel;
        }
    });

    console.log(`The number of safe reports is: ${Safe}`);

    return true;
}
