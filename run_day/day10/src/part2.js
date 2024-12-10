export default function StrapIn(Map, Ayes) {
    // console.debug(Map, Ayes);

    let Graph = {};

    let AddEdge = (a, b) => {
        if (!Graph[a]) {
            Graph[a] = [];
        }

        Graph[a].push(b);
    }

    let CountPaths = (Graph, start, end) => {
        // console.debug(Graph, start, end);

        let Paths = 0;

        let Queue = [start];

        while (Queue.length > 0) {
            let Node = Queue.shift();

            if (end.includes(Node)) {
                Paths++;
            }

            if (Graph[Node]) {
                for (let Next of Graph[Node]) {
                    Queue.push(Next);
                }
            }
            else {
                // console.debug(Node);
            }
        }

        return Paths;
    }

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

        if (MapUp && MapUp.depth === NextDepth) {
            Neighs.push({x: x, y: y - 1});
        }

        if (MapDown && MapDown.depth === NextDepth) {
            Neighs.push({x: x, y: y + 1});
        }

        if (MapLeft && MapLeft.depth === NextDepth) {
            Neighs.push({x: x - 1, y: y});
        }

        if (MapRight && MapRight.depth === NextDepth) {
            Neighs.push({x: x + 1, y: y});
        }

        return Neighs;
    };

    let ResetFilled = (Map) => {
        Graph = {};

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
                if (Map[y][x].filled && Map[y][x].depth === Depth) {
                    Filled.push({x: x, y: y});
                }
            }
        }

        // console.debug(Filled);

        return Filled;
    }

    let Fill = (Map, x, y) => {
        let Done = false;

        Map[y][x].filled = true;

        for (let d = 0; d < 9; d++) {
            const Filled = GetFilled(Map, d);

            for (const Tile of Filled) {
                let Next = GetNeighbors(Map, Tile.x, Tile.y);

                // console.debug(Next);

                while (Next.length > 0) {
                    AddEdge(`${Tile.x}x${Tile.y}`, `${Next[0].x}x${Next[0].y}`);

                    let Neigh = Next.shift();

                    Map[Neigh.y][Neigh.x].filled++;
                }
            }
        }

        // console.debug(Map);

        const FilledMap = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9].map((d) => {
            const Filled = GetFilled(Map, d);

            let FilledCount = 0;

            for (const Tile of Filled) {
                FilledCount += Map[Tile.y][Tile.x].filled;
            }

            return {
                depth: d,
                els  : FilledCount,
                count:  Filled.length
            };
        });

        // console.debug(FilledMap);

        return GetFilled(Map, 9).length;
    }

    let PathSum = 0;

    for (let t = 0; t < Ayes.length; t++) {
        let Trail = Ayes[t];

        Trail.steps = Fill(Map, Trail.x, Trail.y);

        Trail.complete = true;

        Trail.paths = CountPaths(Graph, `${Trail.x}x${Trail.y}`, GetFilled(Map, 9).map((el) => `${el.x}x${el.y}`));

        // console.debug(Graph);
        // console.debug(Trail);

        PathSum += Trail.paths;

        console.log(`Trail ${Trail.name} at (${Trail.x}, ${Trail.y}) has ${Trail.paths} paths.`);

        ResetFilled(Map);
    }

    console.log(`Number of paths: ${PathSum}`);
}
