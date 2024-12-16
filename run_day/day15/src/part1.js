import {Map} from './lib/Map.js';
import clc   from "cli-color";

export default async function MoveZig(map = []) {
    const ActiveMap = new Map(map);

    // console.debug(ActiveMap);

    const CheckSum = ActiveMap.MoveAll();

    // const CheckSum = 0;
    //
    // for (let i = 0; i < 1000; i++) {
    //     console.clear();
    //
    //     ActiveMap.Move();
    //     ActiveMap.Print();
    //
    //     await new Promise((resolve) => setTimeout(resolve, 250));
    // }

    // console.debug(ActiveMap);
    console.debug(clc.bold('Checksum: '), clc.bold.bgCyan(CheckSum));

    return true;
}
