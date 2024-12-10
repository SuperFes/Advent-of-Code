export default function DePart(files, blocks = []) {
    let CheckSum = 0;

    let pBlocks = [...blocks];

    for (let p = pBlocks.length - 1; p > 0; p--) {
        if (pBlocks[p] === null) {
            continue;
        }

        for (let m = 0; m < pBlocks.length && m < p; m++) {
            if (pBlocks[m] === null) {
                pBlocks[m] = pBlocks[p];
                pBlocks[p] = null;
            }
        }
    }

    let pos = 0;

    for (const block of pBlocks) {
        if (block === null) {
            break;
        }

        CheckSum += block * pos++;
    }

    console.log(`Disk checksum after repartitioning is: ${CheckSum}`);

    return true;
}
