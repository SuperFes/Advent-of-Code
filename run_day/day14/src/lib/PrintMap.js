export default function PrintMap(Map, Robots) {
    for (let y = 0; y < Map.bounds.y; y++) {
        for (let x = 0; x < Map.bounds.x; x++) {
            const bot = Robots.bots.filter(bot => bot.pos.x === x && bot.pos.y === y).length;

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
