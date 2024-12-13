export default function HOWDOESITGROW(Map = {}) {
    let PriceTotal = 0;

    let Regions = {};
    let RegionSides = {};

    let Blocks = [
        [null, null],
        [null, null],
    ]

    for (let y = -1; y < Map.bounds.y; y++) {
        for (let x = -1; x < Map.bounds.x; x++) {
            Blocks[0][0] = Map.grid[`${x},${y}`]?.group ?? null;
            Blocks[0][1] = Map.grid[`${x + 1},${y}`]?.group ?? null;
            Blocks[1][0] = Map.grid[`${x},${y + 1}`]?.group ?? null;
            Blocks[1][1] = Map.grid[`${x + 1},${y + 1}`]?.group ?? null;

            let Chars = {};

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

            if (Blocks[0][0] === Blocks[1][1] && Chars[Blocks[0][0]] === 2) {
                if (!RegionSides[Blocks[0][0]]) {
                    RegionSides[Blocks[0][0]] = 2;
                }
                else {
                    RegionSides[Blocks[0][0]] += 2;
                }
            }

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
