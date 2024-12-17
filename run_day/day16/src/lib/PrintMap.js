export default function PrintMap(Map, Robots, id = null) {
    for (let y = 0; y < Map.bounds.y; y++) {
        for (let x = 0; x < Map.bounds.x; x++) {
            const bot = Robots.bots.filter(bot => bot.pos.x === x && bot.pos.y === y && (id === null || bot.id === id)).length;

            if (bot) {
                process.stdout.write(bot.toString());
            }
            else {
                process.stdout.write('.');
            }
        }

        process.stdout.write('\n');
    }
}
