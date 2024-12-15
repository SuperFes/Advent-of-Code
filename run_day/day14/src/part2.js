import PrintMap from './lib/PrintMap.js';
import clc from 'cli-color';

export default function EasterEgg(Map, Robots) {
    const MidX = Math.floor(Map.bounds.x / 2);

    let RowBots = [];

    for (const bot of Robots.bots) {
        bot.pos = bot.origPos;
    }

    let move = 0;

    for (; ;) {
        move++;

        RowBots = [];

        for (const bot of Robots.bots) {
            bot.pos.x += bot.dir.x;
            bot.pos.y += bot.dir.y;

            if (bot.pos.x < 0) {
                bot.pos.x = Map.bounds.x + bot.pos.x;
            }

            if (bot.pos.y < 0) {
                bot.pos.y = Map.bounds.y + bot.pos.y;
            }

            if (bot.pos.x >= Map.bounds.x) {
                bot.pos.x = bot.pos.x - Map.bounds.x;
            }

            if (bot.pos.y >= Map.bounds.y) {
                bot.pos.y = bot.pos.y - Map.bounds.y;
            }

            if (!RowBots[bot.pos.y]) {
                RowBots[bot.pos.y] = 1;
            }
            else {
                RowBots[bot.pos.y]++;
            }
        }

        let TreeFound = false;

        let RowBotRowCols = 0;

        for (let y = 0; y < Map.bounds.y; y++) {
            if (RowBots[y] >= 17) {
                let RowString = '';

                for (let x = 0; x < Map.bounds.x; x++) {
                    const bot = Robots.bots.filter(bot => bot.pos.x === x && bot.pos.y === y).length;

                    if (bot) {
                        RowString += 'X';
                    }
                    else {
                        RowString += '.';
                    }
                }

                if (RowString.includes('.XXXXXXXXXXXXXXXXX.')) {
                    PrintMap(Map, Robots);

                    TreeFound = true;

                    break;
                }
            }
        }

        if (TreeFound) {
            break;
        }

        if (RowBotRowCols === 2) {
            console.debug("RowBots", clc.bold.red(move.toLocaleString()));

            PrintMap(Map, Robots);
        }

//         if (
//             Robots.bots.filter(bot => bot.pos.y < 2).length === 1
//         ) {
//             console.debug(Robots.bots.filter(bot => bot.pos.y === 0));
//             console.debug(Robots.bots.filter(bot => bot.pos.y === 1));
//             console.debug(clc.bold.red(move.toLocaleString()));
//
// //            PrintMap(Map, Robots);
//         }
    }

    console.debug(clc.bold.red(move.toLocaleString()));

    console.debug(clc.bold.yellow("Done"));

    return true;
}
