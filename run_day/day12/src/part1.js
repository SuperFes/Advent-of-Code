export default function QuiteContrary(Map = {}) {
    let PriceTotal = 0;
    let Regions = {};

    for (const point in Map.grid) {
        const Point = Map.grid[point];

        if (!Regions[Point.group]) {
            Regions[Point.group] = {
                plant: Point.plant,
                count: 1,
                area: 1,
                perimeter: Point.edges,
            };
        }
        else {
            Regions[Point.group].count++;
            Regions[Point.group].area++;
            Regions[Point.group].perimeter += Point.edges;
        }
    }

    for (const region in Regions) {
        const Region = Regions[region];
        const Price = Region.area * Region.perimeter;

        PriceTotal += Price;

        console.log(`A region of ${Region.plant} plants with price ${Region.area} * ${Region.perimeter} = ${Price}`);
    }

    console.log(`The total price of the garden is: ${PriceTotal}`);
}
