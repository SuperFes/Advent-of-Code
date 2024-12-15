import PrintMap from './lib/PrintMap.js';

export default function TinkleTown(Map, Robots) {
    console.debug(Robots);

    for (let move = 0; move < Map.iterations.part1; move++) {
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
        }
    }

    console.debug(Robots);

    const MidX = Math.floor(Map.bounds.x / 2);
    const MidY = Math.floor(Map.bounds.y / 2);

    const Quadrants = [
        Robots.bots.filter(bot => bot.pos.x < MidX && bot.pos.y < MidY).length,
        Robots.bots.filter(bot => bot.pos.x > MidX && bot.pos.y < MidY).length,
        Robots.bots.filter(bot => bot.pos.x < MidX && bot.pos.y > MidY).length,
        Robots.bots.filter(bot => bot.pos.x > MidX && bot.pos.y > MidY).length,
    ];

    console.debug(Quadrants);

    PrintMap(Map, Robots);

    const SafetyFactor = Quadrants.reduce((acc, val) => acc * val, 1);

    console.debug(SafetyFactor);

    return true;
}
