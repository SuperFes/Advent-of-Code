#!/usr/bin/env slsh

variable board         = Array_Type[5000];
variable patternBuffer = Int_Type[100];

variable shape;

variable loop_times = 0;
variable loop_rules;

variable height = 0;
variable lastTop = 0;

variable stoppedRocks = 0;

variable patternHeight = 0;

variable Shape_Type = struct {
    bits,
    width = Integer_Type,
    height = Integer_Type,
    left = Integer_Type,
    top = Integer_Type,
    right = Integer_Type,
    bottom = Integer_Type
};

define createShape(bits, coords) {
    variable newShape = @Shape_Type;

    variable row, col;

    newShape.bits = @Array_Type[coords[0]];

    for (row = 0; row < coords[0]; row++) {
        newShape.bits[row] = @String_Type[coords[1]];
    }

    newShape.width = coords[1] - 1;
    newShape.height = coords[0] - 1;

    for (row = 0; row <= newShape.height; row++) {
        for (col = 0; col <= newShape.width; col++) {
            newShape.bits[row][col] = bits[col + (row * coords[1])];
        }
    }

    newShape.top  = 0;
    newShape.left = 0;
    newShape.right = newShape.width - 1;
    newShape.bottom = newShape.height - 1;

    return newShape;
}

variable newShape = 0;

define transposeShape() {
    variable r, c;
    variable x, y;

    for (r = shape.top; r <= shape.bottom; r++) {
        for (c = shape.left; c <= shape.right; c++) {
            x = c - shape.left;
            y = r - shape.top;

            if (shape.bits[y][x] == "@") {
                board[r][c] = "#";

                if (r > lastTop) {
                    lastTop = r;
                }
            }
        }
    }

    stoppedRocks++;

    newShape = 1;
}

define canMove(dir) {
    variable r, c;
    variable x, y;

    variable checkX = 0;
    variable checkY = 0;

    if (dir == '_') {
        checkY = -1;
    }
    else if (dir == '<') {
        checkX = -1;
    }
    else if (dir == '>') {
        checkX = +1;
    }

    for (r = shape.top; r <= shape.bottom; r++) {
        for (c = shape.left; c <= shape.right; c++) {
            x = c - shape.left;
            y = r - shape.top;

            if (shape.bits[y][x] != " ") {
                if (c + checkX >= 0 && c + checkX <= 6 && r + checkY > 0) {
                    if (board[r + checkY][c + checkX] != " ") {
                        return 'X';
                    }
                }
            }
        }
    }

    return dir;
}

define moveShape(dir) {
    variable x = 0, y = 0;

    variable move = canMove(dir);

    if (dir == '_') {
        if (shape.top > 1 && move == '_') {
            y = -1;
        }
        else {
            transposeShape();
        }
    }
    else if (dir == '<') {
        if (shape.left > 0 && move == '<') {
            x = -1;
        }
    }
    else if (dir == '>') {
        if (shape.right < 6 && move == '>') {
            x = 1;
        }
    }

    shape.top  += y;
    shape.left += x;

    shape.bottom = shape.top + shape.height;
    shape.right  = shape.left + shape.width;
}

define setShapePos(x, y) {
    shape.top  = y;
    shape.left = x;

    shape.bottom = shape.top + shape.height;
    shape.right  = shape.left + shape.width;
}

define initBoard() {
    variable row, col;

    variable coords = array_shape(board);

    for (row = 0; row < coords[0]; row++) {
        board[row] = String_Type[7];

        for (col = 0; col < 7; col++) {
            board[row][col] = " ";
        }
    }

    coords = array_shape(patternBuffer);

    for (row = 0; row < coords[0]; row++) {
        patternBuffer[row] = 0;
    }
}

variable shapeA = createShape(["@", "@", "@", "@"], [1, 4]);
variable shapeB = createShape([" ", "@", " ", "@", "@", "@", " ", "@", " "], [3, 3]);
variable shapeC = createShape(["@", "@", "@", " ", " ", "@", " ", " ", "@"], [3, 3]);
variable shapeD = createShape(["@", "@", "@", "@"], [4, 1]);
variable shapeE = createShape(["@", "@", "@", "@"], [2, 2]);

define shapeToBoard(sendShape) {
    if (sendShape == "A") {
        shape    = @shapeA;
    }
    else if (sendShape == "B") {
        shape    = @shapeB;
    }
    else if (sendShape == "C") {
        shape    = @shapeC;
    }
    else if (sendShape == "D") {
        shape    = @shapeD;
    }
    else if (sendShape == "E") {
        shape    = @shapeE;
    }

    setShapePos(2, lastTop + 4);

    newShape = 0;
}

define printBoard(move) {
    variable r, c;
    variable line = "";
    variable x, y;

    for (r = lastTop + 10; r > lastTop - 20; r--) {
        line = "";

        if (r >= shape.top && r <= shape.bottom) {
            for (c = 0; c < 7; c++) {
                x = c - shape.left;
                y = r - shape.top;

                if (x >= 0 && x <= shape.width && shape.bits[y][x] != " " && shape.bits[y][x] != NULL) {
                    line += shape.bits[y][x];
                }
                else {
                    line += board[r][c];
                }
            }
        }
        else {
            for (c = 0; c < 7; c++) {
                line += board[r][c];
            }
        }

        printf("|%s|\n", line);
    }

    printf("+-=[%c]=-+\n", move);
}

define slsh_main () {
    variable fileName = __argv[1];

    printf("TetRocks! Using input file: %s\n", fileName);

    variable moveFile = fopen(fileName, "r");
    variable moves;

    if (moveFile == NULL) {
        throw OpenError, "Failed to open $fileName for reading =("$;
    }

    fgets(&moves, moveFile);
    moves = strtrim(moves);

    variable shapes = ["A", "B", "C", "D", "E"];

    variable shapeCount = 5;
    variable shapeInt = 0;

    initBoard();

    shapeToBoard(shapes[shapeInt]);

    variable totalMoves = strlen(moves);
    variable currentMove = 0;
    variable i, move;

    for (i = 0; ; i++) {
        move = moves[currentMove];

        moveShape(move);
        moveShape('_');

        if (newShape == 1) {
            shapeInt++;

            if (shapeInt >= shapeCount) {
                shapeInt = 0;
            }

            shapeToBoard(shapes[shapeInt]);
        }

        if (stoppedRocks == 2022) {
            printBoard(move);

            printf("Tower is %d high!\n", lastTop);

            break;
        }
        else if (stoppedRocks > 2020) {
            printBoard(move);

            sleep(1);
        }

        currentMove++;

        if (currentMove == totalMoves) {
            currentMove = 0;
        }
    }
}
