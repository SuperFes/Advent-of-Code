function CheckPath(Levels) {
    let Safety        = 0;
    let CurrentLevel  = 0;
    let CurrentDir    = 0;
    let CurrentLevels = Levels.length;

    // console.log(Levels);

    Levels.map((level, i) => {
        let newLevel = parseInt(level);

        if (CurrentLevel === 0) {
            Safety++;
        }
        else {
            const levelDiff = Math.abs(newLevel - CurrentLevel);
            const safeDiff  = levelDiff <= 3 && levelDiff >= 1 && levelDiff !== 0;

            const dirDiff = (newLevel > CurrentLevel ? 1 : (newLevel < CurrentLevel ? -1 : 0));

            if (safeDiff) {
                if ((CurrentDir === 0 && dirDiff !== 0) || CurrentDir === dirDiff) {
                    CurrentDir = dirDiff;

                    Safety++;
                }
            }
        }

        CurrentLevel = newLevel;
    });

    if (Safety === CurrentLevels) {
        return true;
    }
    else {
        return false;
    }
}

export default function part2(lines = []) {
    let Safe = 0;

    lines.map((line, i) => {
        let Paths = [];

        if (line.length === 0) {
            return;
        }

        const Levels = line.split(' ');

        for (let i = 0; i < Levels.length; i++) {
            if (i === Levels.length - 1) {
                Paths.push(Levels);
            }

            let Temp = [];

            for (let j = 0; j < Levels.length; j++) {
                if (j === i) {
                    continue;
                }

                Temp.push(Levels[j]);
            }

            Paths.unshift(Temp);
        }

        let path        = Paths.pop();
        let currentSafe = false;

        while (!currentSafe && path && path.length) {
            if (CheckPath(path)) {
                // console.log(`Level "${line}" is: safe.`);

                currentSafe = true;

                break;
            }

            path = Paths.pop();
        }

        if (!currentSafe) {
            // console.log(`Level "${line}" is: not safe.`);
        }
        else {
            Safe++;
        }
    });

    console.log(`The number of safe reports is: ${Safe}`);

    return true;
}
