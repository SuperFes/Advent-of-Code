export default function StrapIn(Map, Ayes) {
    // console.debug(Map, Ayes);

    let GetTile = (Map, x, y) => {
        if (Map[y] && Map[y][x]) {
            // console.debug(Map[y][x], x, y);
            return Map[y] && Map[y][x];
        }

        return null;
    }

    let GetNeighbors = (Map, x, y) => {
        let Neighs = [];

        const NextDepth = Map[y][x].depth + 1;

        const MapUp    = GetTile(Map, x, y - 1);
        const MapDown  = GetTile(Map, x, y + 1);
        const MapLeft  = GetTile(Map, x - 1, y);
        const MapRight = GetTile(Map, x + 1, y);

        // console.debug(Map[y][x], x, y, MapUp, MapDown, MapLeft, MapRight);

        if (MapUp && MapUp.filled === 0 && MapUp.depth === NextDepth) {
            Neighs.push({x: x, y: y - 1});
        }

        if (MapDown && MapDown.filled === 0 && MapDown.depth === NextDepth) {
            Neighs.push({x: x, y: y + 1});
        }

        if (MapLeft && MapLeft.filled === 0 && MapLeft.depth === NextDepth) {
            Neighs.push({x: x - 1, y: y});
        }

        if (MapRight && MapRight.filled === 0 && MapRight.depth === NextDepth) {
            Neighs.push({x: x + 1, y: y});
        }

        return Neighs;
    };

    let ResetFilled = (Map) => {
        for (let y = 0; y < Map.length; y++) {
            for (let x = 0; x < Map[0].length; x++) {
                if (Map[y][x].filled) {
                    Map[y][x].filled = 0;
                }
            }
        }
    }

    let GetFilled = (Map, Depth = 9) => {
        let Filled = [];

        for (let y = 0; y < Map.length; y++) {
            for (let x = 0; x < Map[0].length; x++) {
                if (Map[y][x].filled > 0 && Map[y][x].depth === Depth) {
                    Filled.push({x: x, y: y});
                }
            }
        }

        // console.debug(Filled);

        return Filled;
    }

    let Fill = (Map, x, y) => {
        let Done = false;

        Map[y][x].filled = 1;

        for (let d = 0; d < 9; d++) {
            const Filled = GetFilled(Map, d);

            // console.debug(Filled);

            for (const Tile of Filled) {
                let Next = GetNeighbors(Map, Tile.x, Tile.y);

                // console.debug(Next);

                while (Next.length > 0) {
                    let Neigh = Next.shift();

                    Map[Neigh.y][Neigh.x].filled++;
                }
            }
        }

        // console.debug(Map);

        return GetFilled(Map, 9).length;
    }

    let NumberNines = 0;

    for (let t = 0; t < Ayes.length; t++) {
        let Trail = Ayes[t];

        Trail.steps = Fill(Map, Trail.x, Trail.y);

        NumberNines += Trail.steps;

        Trail.complete = true;

        console.log(`Trail ${Trail.name} at (${Trail.x}, ${Trail.y}) has ${Trail.steps} trails.`);

        ResetFilled(Map);
    }

    console.log(`Number of nines: ${NumberNines}`);
}
