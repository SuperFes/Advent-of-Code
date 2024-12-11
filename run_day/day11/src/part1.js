export default function BongAndABlintz(stones = []) {
    let Stones = [...stones];

    let MarkZed = (array, stone) => {
        array.push(1);
    }

    let MarkEven = (array, stone) => {
        const StoneValue = String(stone);
        const StoneSplit = StoneValue.length / 2;

        const LeftStone = Number(StoneValue.slice(0, StoneSplit));
        const RightStone = Number(StoneValue.slice(StoneSplit));

        array.push(LeftStone);
        array.push(RightStone);
    }

    let MarkElse = (array, stone) => {
        array.push(stone * 2024);
    }

    for (let r = 0; r < 25; r++) {
        console.debug(Stones);

        let NewStones = [];

        for (const Stone of Stones) {
            if (Stone === 0) {
                MarkZed(NewStones, Stone);
            }
            else if (String(Stone).length % 2 === 0) {
                MarkEven(NewStones, Stone);
            }
            else {
                MarkElse(NewStones, Stone);
            }
        }

        Stones = NewStones;
    }

    const StoneCount = Stones.length;

    console.log(`The total number of stones is: ${StoneCount}`);

    return true;
}
