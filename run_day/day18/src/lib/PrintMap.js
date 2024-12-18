import clc from "cli-color";

export default function PrintMap(Map, id = null) {
    for (let y = 0; y <= Map.bounds.height; y++) {
        for (let x = 0; x <= Map.bounds.width; x++) {
            const byte = Map.bytes.filter(byte => byte.x === x && byte.y === y && byte.time <= Map.time).length;
            const path = Map.path.includes(`${x}:${y}`);

            if (x === 0 && y === 0) {
                process.stdout.write(clc.yellow.bold.italic('S'));
            }
            else if (x === Map.bounds.width && y === Map.bounds.height) {
                process.stdout.write(clc.yellow.bold.italic('E'));
            }
            else if (path) {
                process.stdout.write(clc.bold('O'));
            }
            else if (byte) {
                process.stdout.write(clc.red('#'));
            }
            else {
                process.stdout.write(clc.green('.'));
            }
        }

        process.stdout.write('\n');
    }

    console.log(`Steps: ${Map.path.length - 1}`);
}
