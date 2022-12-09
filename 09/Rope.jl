#!/usr/bin/env julia

using Printf

function tailDistance(posX, posY)
    return sqrt((posX[1]-posY[1])^2 + (posX[2]-posY[2])^2)
end

function count_positions(filename)
    f = open(filename)

    position = (0, 0)
    tailPosition = (0, 0)
    positions = Set{Tuple{Int, Int}}()
    push!(positions, position)

    for line in readlines(f)
        if (length(line)) == 0
            continue
        end
        dir = split(line)
        direction = dir[1]
        distance = parse(Int, dir[2])
        for mojo = 1:distance
            position = move(position, direction, 1)

            dist = tailDistance(position, tailPosition)

            if dist > 1.5
                if direction == "R"
                    tailPosition = (position[1] - 1, position[2])
                elseif direction == "L"
                    tailPosition = (position[1] + 1, position[2])
                elseif direction == "U"
                    tailPosition = (position[1], position[2] + 1)
                elseif direction == "D"
                    tailPosition = (position[1], position[2] - 1)
                end

                push!(positions, tailPosition)
            end
        end
    end

    return length(positions)
end

function move(position, direction, distance)
    if direction == "R"
        return (position[1] + distance, position[2])
    elseif direction == "L"
        return (position[1] - distance, position[2])
    elseif direction == "D"
        return (position[1], position[2] + distance)
    elseif direction == "U"
        return (position[1], position[2] - distance)
    end
end

if length(ARGS) != 1
    println("Please provide the input file name as the command line argument")
    exit()
end

filename = ARGS[1]

println("For the file ", filename, " the count of positions touched is ", count_positions(filename))

exit()
