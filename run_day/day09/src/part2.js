export default function DeFrag(files, blocks = []) {
    let CheckSum = 0;

    let pFiles = [...files].sort((a, b) => b.id - a.id);
    let pBlocks = [...blocks];

    let findSpace = function (size) {
        let space = 0;
        let start = null;

        for (let p = 0; p < pBlocks.length; p++) {
            if (pBlocks[p] === null) {
                space++;

                if (start === null) {
                    start = p;
                }
            }
            else {
                space = 0;
                start = null;

                continue;
            }

            if (space >= size) {
                return start;
            }
        }

        return -1;
    }

    for (const file of pFiles) {
        let space = findSpace(file.size);

        if (space === -1) {
            continue;
        }

        if (space > file.position) {
            continue;
        }

        for (let p = 0; p < file.size; p++) {
            pBlocks[space++] = file.id;
        }

        for (let p = 0; p < file.size; p++) {
            pBlocks[file.position + p] = null;
        }

        file.position = space - file.size;
    }

    let pos = 0;

    for (const block of pBlocks) {
        if (block === null) {
            pos++;

            continue;
        }

        CheckSum += block * pos++;
    }

    console.log(`Disk checksum after defragmentation is: ${CheckSum}`);

    return true;
}
