export default function BluntAndAPancake(stones = []) {
    let Stones = {};
    let NextStones = {};

    for (let i = 0; i < stones.length; i++) {
        const stone = stones[i];

        if (!Stones[stone]) {
            Stones[stone] = 1;
        }
        else {
            Stones[stone]++;
        }
    }

    let setStone = (stone, count) => {
        if (!NextStones[stone]) {
            NextStones[stone] = count;
        }
        else {
            NextStones[stone] += count;
        }
    }

    for (let r = 0; r < 75; r++) {
        // console.time(`Itteration ${r + 1}`);

        for (const stone in Stones) {
            const Count = Stones[stone];

            if (stone === "0") {
                setStone('1', Count);
            }
            else if (stone.length % 2 === 0) {
                const StoneSplit = stone.length / 2;

                const LeftStone = Number(stone.slice(0, StoneSplit));
                const RightStone = Number(stone.slice(StoneSplit));

                setStone(LeftStone, Count);
                setStone(RightStone, Count);
            }
            else {
                setStone(Number(stone) * 2024, Count);
            }
        }

        Stones = NextStones;

        NextStones = {};

        // console.timeEnd(`Itteration ${r + 1}`);
    }

    let StoneCount = 0;

    for (const stone in Stones) {
        // console.debug(stone, Stones[stone]);

        StoneCount += Stones[stone];
    }

    console.log(`The total number of stones is: ${StoneCount}`);

    return true;
}
