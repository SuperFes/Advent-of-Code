export default function HOWDOESITGROW(Map = {}) {
    let PriceTotal = 0;

    let Regions = {};
    let RegionSides = {};

    /*
     * What are we looking for.
     *
     * The following patterns are corners:
     *
     *  2  2  2  2  4  4  4  4  2  2
     *  XX OX XO XX XY XO OX XB BB OX
     *  OX XX XX XO ZA OX XO AX XO BB
     *
     * Thus, any time there is one type of plant in a corner, it is a corner. (Outside)
     * If there are 3 blocks of the same plant, they are also a corner. (Inside)
     * The only special check is when the blocks are diagonal from each other. Wherein they would value 2,
     * so we check that they are not in line with each other.
     *
     * So, I guess you wanna ask, why are we counting corners?
     *
     * Well, the number of sides is equal to the number of corners, and they're easier to look for.
     *
     * Voilà.
     */
    let Blocks = [
        [null, null],
        [null, null],
    ]

    // Loop through from just outside the top left corner to just outside the bottom right corner of the map
    for (let y = -1; y < Map.bounds.y; y++) {
        for (let x = -1; x < Map.bounds.x; x++) {
            Blocks[0][0] = Map.grid[`${x},${y}`]?.group ?? null;
            Blocks[0][1] = Map.grid[`${x + 1},${y}`]?.group ?? null;
            Blocks[1][0] = Map.grid[`${x},${y + 1}`]?.group ?? null;
            Blocks[1][1] = Map.grid[`${x + 1},${y + 1}`]?.group ?? null;

            let Chars = {};

            // Counting the occurrences of each plant in the block.
            for (let i = 0; i < 2; i++) {
                for (let j = 0; j < 2; j++) {
                    if (Blocks[i][j] !== null) {
                        if (!Chars[Blocks[i][j]]) {
                            Chars[Blocks[i][j]] = 1;
                        }
                        else {
                            Chars[Blocks[i][j]]++;
                        }
                    }
                }
            }

            // Checking for inside and outside corners
            for (const char in Chars) {
                if (Chars[char] === 3 || Chars[char] === 1) {
                    if (!RegionSides[char]) {
                        RegionSides[char] = 1;
                    }
                    else {
                        RegionSides[char]++;
                    }
                }
            }

            // Checking for diagonal corners
            if (Blocks[0][0] === Blocks[1][1] && Chars[Blocks[0][0]] === 2) {
                if (!RegionSides[Blocks[0][0]]) {
                    RegionSides[Blocks[0][0]] = 2;
                }
                else {
                    RegionSides[Blocks[0][0]] += 2;
                }
            }

            // The other diagonal
            if (Blocks[0][1] === Blocks[1][0] && Chars[Blocks[0][1]] === 2) {
                if (!RegionSides[Blocks[1][0]]) {
                    RegionSides[Blocks[1][0]] = 2;
                }
                else {
                    RegionSides[Blocks[1][0]] += 2;
                }
            }
        }
    }

    // console.log(RegionSides);

    for (const point in Map.grid) {
        const Point = Map.grid[point];

        if (!Regions[Point.group]) {
            Regions[Point.group] = {
                plant: Point.plant,
                count: 1,
                area: 1,
                sides: RegionSides[Point.group],
            };
        }
        else {
            Regions[Point.group].count++;
            Regions[Point.group].area++;
        }
    }

    // console.log(Regions);

    for (const region in Regions) {
        const Region = Regions[region];
        const Price = Region.area * Region.sides;

        PriceTotal += Price;

        console.log(`A region of ${Region.plant} plants with price ${Region.area} * ${Region.sides} = ${Price}`);
    }

    console.log(`The total price of the garden is: ${PriceTotal}`);

}
