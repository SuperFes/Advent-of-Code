import {Map} from './lib/Map.js';
import clc   from "cli-color";

export default async function AYBABTU(map1 = [], map = []) {
    const ActiveMap = new Map(map);

    // console.debug(ActiveMap);

    const CheckSum = ActiveMap.MoveAll();

    // for (let i = 0; i < 700; i++) {
    //     ActiveMap.Move();
    //     ActiveMap.Print();
    //
    //     await new Promise((resolve) => setTimeout(resolve, 50));
    // }
    //
    // const CheckSum = ActiveMap.GetCheckSum(700);

    // console.debug(ActiveMap);
    console.debug(clc.bold('Checksum: '), clc.bold.bgCyan(CheckSum));

    return true;
}
