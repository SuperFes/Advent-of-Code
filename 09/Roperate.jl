#!/usr/bin/env julia

using Printf

function abs(val)
    if val < 0
        val = val * -1
    end

    return val
end

function bodyDistance(posX, posY)
    return (posX[1] - posY[1], posX[2] - posY[2])
end

function moveBody(position, lastKnot)
    diff = bodyDistance(lastKnot, position)

    moveX = -1
    moveY = -1

    if diff[1] > 0
        moveX = 1
    end

    if diff[2] > 0
        moveY = 1
    end

    if abs(diff[1]) > 1 && diff[2] == 0
        return (position[1] + moveX, position[2])
    elseif diff[1] == 0 && abs(diff[2]) > 1
        return (position[1], position[2] + moveY)
    elseif abs(diff[1]) > 1 || abs(diff[2]) > 1
        return (position[1] + moveX, position[2] + moveY)
    end

    return position
end

function count_positions(filename)
    f = open(filename)

    rope = [(0, 0), (0, 0), (0, 0), (0, 0), (0, 0), (0, 0), (0, 0), (0, 0), (0, 0), (0, 0)]

    positions = Set{Tuple{Int, Int}}()

    push!(positions, (0, 0))

    for line in readlines(f)
        if (length(line)) == 0
            continue
        end

        dir = split(line)
        direction = dir[1]
        distance = parse(Int, dir[2])

        for mojo = 1:distance
            rope[1] = move(rope[1], direction)
            lastRope = rope

            for body = 2:10
                rope[body] = moveBody(rope[body], rope[body-1])
            end

            push!(positions, rope[10])
        end
    end

    return length(positions)
end

function printBoard(rope, positions)
    ropeChar = ["H", "1", "2", "3", "4", "5", "6", "7", "8", "9"]

    for y = -80:80
        outLine = ""

        for x = -80:80
            char = "."

            for pos in positions
                if pos[1] == x && pos[2] == y
                    char = "#"
                end
            end

            for knot = 1:10
                if rope[knot][1] == x && rope[knot][2] == y
                    char = ropeChar[knot]
                elseif x == 0 && y == 0
                    char = "s"
                end
            end

            outLine = outLine * char
        end

        println(outLine)
    end

    sleep(0.005)
end

function move(position, direction)
    if direction == "R"
        return (position[1] + 1, position[2])
    elseif direction == "L"
        return (position[1] - 1, position[2])
    elseif direction == "D"
        return (position[1], position[2] + 1)
    elseif direction == "U"
        return (position[1], position[2] - 1)
    end
end

if length(ARGS) != 1
    println("Please provide the input file name as the command line argument")
    exit()
end

filename = ARGS[1]

println("For the file ", filename, " the count of positions touched is ", count_positions(filename))

exit()
