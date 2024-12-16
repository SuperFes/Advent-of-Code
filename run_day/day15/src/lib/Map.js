import clc from 'cli-color';

export class Map {
    map   = null;
    moves = 0;

    constructor(map) {
        this.map   = map;
        this.moves = 0;

        this.GetCheckSum();
    }

    Push(move, x, y, bro, stack = []) {
        const offset = this.map.Width - 1;

        // Push all the boxes, stop at the wall
        const box  = this.map.Boxes.find(box => box !== bro && box.x === x && box.y === y);
        const wall = this.map.Walls.find(wall => wall.x === x && wall.y === y);

        if (wall) {
            return false;
        }

        if (box) {
            if (move === '^') {
                if (!this.Push(move, x, y - 1, box, stack)) {
                    return false;
                }

                if (box.bro && !this.Push(move, box.bro.x, y - 1, box, stack)) {
                    return false;
                }

                if (!stack.includes(box)) {
                    stack.push(box);
                }
            }
            else if (move === '>') {
                if (!this.Push(move, x + 1, y, box, stack)) {
                    return false;
                }

                if (!stack.includes(box)) {
                    stack.push(box);
                }
            }
            else if (move === 'v') {
                if (!this.Push(move, x, y + 1, box, stack)) {
                    return false;
                }

                if (box.bro && !this.Push(move, box.bro.x, y + 1, box, stack)) {
                    return false;
                }

                if (!stack.includes(box)) {
                    stack.push(box);
                }
            }
            else if (move === '<') {
                if (!this.Push(move, x - 1, y, box, stack)) {
                    return false;
                }

                if (!stack.includes(box)) {
                    stack.push(box);
                }
            }

            if (box.bro && !stack.includes(box.bro)) {
                stack.push(box.bro);
            }
        }

        return stack;
    }

    Move() {
        const move  = this.map.Moves[this.moves] ?? '';
        const robot = this.map.Robot;

        if (move === '^') {
            const stack = this.Push(move, robot.x, robot.y - 1);

            if (stack !== false) {
                robot.y--;

                for (const box of stack) {
                    box.y--;
                }
            }
        }
        else if (move === '>') {
            const stack = this.Push(move, robot.x + 1, robot.y);

            if (stack !== false) {
                robot.x++;

                for (const box of stack) {
                    box.x++;
                }
            }
        }
        else if (move === 'v') {
            const stack = this.Push(move, robot.x, robot.y + 1);

            if (stack !== false) {
                robot.y++;

                for (const box of stack) {
                    box.y++;
                }
            }
        }
        else if (move === '<') {
            const stack = this.Push(move, robot.x - 1, robot.y);

            if (stack !== false) {
                robot.x--;

                for (const box of stack) {
                    box.x--;
                }
            }
        }

        this.moves++;

        this.GetCheckSum();
    }

    MoveAll() {
        while (this.moves < this.map.Moves.length) {
            // console.debug('Move: ', this.moves, this.map.Moves[this.moves]);

            this.Move();
        }

        this.Print();

        return this.map.CheckSums[this.moves - 1];
    }

    Print() {
        for (let y = 0; y < this.map.Bounds.y; y++) {
            for (let x = 0; x < this.map.Bounds.x; x++) {
                const box = this.map.Boxes.find(box => box.x === x && box.y === y);

                if (this.map.Walls.find(wall => wall.x === x && wall.y === y)) {
                    process.stdout.write(clc.red('#'));
                }
                else if (box) {
                    process.stdout.write(clc.italic.green(box.char));
                }
                else if (this.map.Robot.x === x && this.map.Robot.y === y) {
                    process.stdout.write(clc.bold.yellow('@'));
                }
                else {
                    process.stdout.write(' ');
                }
            }

            process.stdout.write(`\n`);
        }

        console.debug('Moves: ', this.moves, `Move: ${this.map.Moves[this.moves]}`);
    }

    GetCheckSum(move = -1) {
        let CheckSum = 0;

        if (move === -1) {
            for (const box of this.map.Boxes) {
                if (box.char === ']') {
                    continue;
                }

                CheckSum += box.y * 100 + box.x;
            }

            console.debug('CheckSum: ', CheckSum, this.moves);

            this.map.CheckSums[this.moves] = CheckSum;
        }
        else {
            CheckSum = this.map.CheckSums[this.moves] ?? 0;
        }

        return CheckSum;
    }
}
