#!/usr/bin/env julia

using Printf

function bodyDistance(posX, posY)
    return sqrt((posX[1]-posY[1])^2 + (posX[2]-posY[2])^2)
end

function moveBody(position, direction)
    if direction == "UR"
        return (position[1] - 1, position[2] + 1)
    elseif direction == "UL"
        return (position[1] + 1, position[2] + 1)
    elseif direction == "DR"
        return (position[1] - 1, position[2] - 1)
    elseif direction == "DL"
        return (position[1] + 1, position[2] - 1)
    elseif direction == "R"
        return (position[1] - 1, position[2])
    elseif direction == "L"
        return (position[1] + 1, position[2])
    elseif direction == "D"
        return (position[1], position[2] - 1)
    elseif direction == "U"
        return (position[1], position[2] + 1)
    end

    return position
end

function getDirection(pos1, pos2)
    if pos1[1] == pos2[1] # move vertical
        if pos1[2] > pos2[2] # moves down
            return "U"
        elseif pos1[2] < pos2[2] # moves up
            return "D"
        end
    elseif pos1[2] == pos2[2] # move horizontal
        if pos1[1] > pos2[1] # moves left
            return "L"
        elseif pos1[1] < pos2[1] # moves right
            return "R"
        end
    elseif pos1[1] > pos2[1] && pos1[2] > pos2[2] # moves bottom left
        return "UL"
    elseif pos1[1] < pos2[1] && pos1[2] > pos2[2] # moves bottom right
        return "UR"
    elseif pos1[1] > pos2[1] && pos1[2] < pos2[2] # moves top left
        return "DL"
    elseif pos1[1] < pos2[1] && pos1[2] < pos2[2] # moves top right
        return "DR"
    end
    return "NONE"
end

function count_positions(filename)
    f = open(filename)

    rope = [(0, 0), (0, 0), (0, 0), (0, 0), (0, 0), (0, 0), (0, 0), (0, 0), (0, 0), (0, 0)]
    ropeChar = ["H", "1", "2", "3", "4", "5", "6", "7", "8", "9"]

    positions = Set{Tuple{Int, Int}}()

    push!(positions, (0, 0))

    for line in readlines(f)
        if (length(line)) == 0
            continue
        end
        dir = split(line)
        direction = dir[1]
        distance = parse(Int, dir[2])
        lastRope = rope
        for mojo = 1:distance
            rope[1] = move(rope[1], direction)

            for body = 2:10
                dist2 = 1
                dir2  = "NONE"

                dist  = bodyDistance(rope[body-1], rope[body])
                dir   = getDirection(rope[body], rope[body-1])

                if dir == "NONE"
                    break
                end

                if body < 10
                    dist2 = bodyDistance(rope[body+1], rope[body])
                    dir2  = getDirection(rope[body], rope[body+1])
                end

                println(dist, "/", dist2, "->", dir, "|" , dir2)

                if dist > 1.5
                    if mojo - 1 > body
                        rope[body] = moveBody(rope[body-1], direction)
                    else
                        rope[body] = moveBody(rope[body-1], dir)
                    end
                end
            end

            for y = -15:15
                outLine = ""

                for x = -15:15
                    char = "."

                    for pos in positions
                        if pos[1] == x && pos[2] == y
                            char = "#"
                        end
                    end

                    for knot = 10:1
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
            push!(positions, rope[10])
            sleep(0.25)
        end
    end

    return length(positions)
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
