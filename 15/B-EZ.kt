import java.io.File
import java.lang.Integer.max
import java.lang.Integer.min
import kotlin.math.abs

fun main(args: Array<String>) {
    if (args.isEmpty()) {
        println("Please provide the input file to parse")

        return
    }

    val inputFile = File(args[0])

    val data = inputFile.readLines()

    var minX = 0
    var maxX = 0
    var minY = 0
    var maxY = 0

    val sensors = data.map { line ->
        val split = line.split(" ", "=", ",", ":")
        val x = split[3].toInt()
        val y = split[6].toInt()

        val bx = split[13].toInt()
        val by = split[16].toInt()

        if (x < minX) {
            minX = x
        }

        if (x > maxX) {
            maxX = x
        }

        if (y < minY) {
            minY = y
        }

        if (y > maxY) {
            maxY = y
        }

        if (bx < minX) {
            minX = bx
        }

        if (bx > maxX) {
            maxX = bx
        }

        if (by < minY) {
            minY = by
        }

        if (by > maxY) {
            maxY = by
        }

        Sensor(x, y, bx, by)
    }

    val consoleGrid = ArrayList<Pair<Pair<Int, Int>, Char>>()

    sensors.forEach { sensor ->
        if (!consoleGrid.any { it.first.first == sensor.x && it.first.second == sensor.y }) {
            consoleGrid.add(Pair(Pair(sensor.x, sensor.y), 'S'))
        }

        if (!consoleGrid.any { it.first.first == sensor.bx && it.first.second == sensor.by }) {
            consoleGrid.add(Pair(Pair(sensor.bx, sensor.by), 'B'))
        }
    }

    val scanRow = 2000000

    val Ranges = sensors.map { it.CheckRange(scanRow) }

    var LeftMost = 0
    var RightMost = 0

    Ranges.forEach {range ->
        if (range == null) {
            return@forEach
        }

        if (LeftMost > range.first) {
            LeftMost = range.first
        }

        if (RightMost < range.second) {
            RightMost = range.second
        }

        if (LeftMost < minX) {
            minX = LeftMost
        }

        if (RightMost > maxX) {
            maxX = RightMost
        }
    }

    val result = RightMost - LeftMost

    println("Result: $result")

    // Part Deux
    var coordX = max(sensors.minOf { it.x }, 0)
    var coordY = max(sensors.minOf { it.y }, 0)

    val coordMaxX = min(sensors.maxOf { it.x }, 4000000)
    val coordMaxY = min(sensors.maxOf { it.y }, 4000000)

    var solved = false
    var bumped = false

    val candidates = ArrayList<Pair<Int, Int>>()

    for (row in coordY .. coordMaxY) {
        sensors.map { it.CheckRange(row) }.forEach() {
            if (it == null || solved) {
                return@forEach
            }

            bumped = false

            var check = Pair<Int, Int>(0, 0)
            var checkFailed = false

            for (sensor in sensors) {
                if (MrManhattan(row, it.first - 1, sensor.x, sensor.y) <= sensor.ManDist) {
                    bumped = true
                }
                else {
                    check = Pair(it.first - 1, row)
                }

                if (MrManhattan(row, it.second + 1, sensor.x, sensor.y) <= sensor.ManDist) {
                    bumped = true
                }
                else {
                    check = Pair(it.second + 1, row)
                }

                for (sensorCheck in sensors) {
                    if (check.first < coordX || check.first > coordMaxX || check.second < coordY || check.second > coordMaxY || MrManhattan(check.first, check.second, sensorCheck.x, sensorCheck.y) <= sensorCheck.ManDist) {
                        checkFailed = true

                        break
                    }
                }

                if (!checkFailed) {
                    candidates.add(check)

                    solved = true

                    break
                }
            }

            if (!bumped || solved) {
                println("We found!")

                return@forEach
            }
        }

        if (solved) {
            break;
        }
    }

    for (candidate in candidates) {
        bumped = false

        for (sensor in sensors) {
            if (MrManhattan(candidate.first, candidate.second, sensor.x, sensor.y) <= sensor.ManDist) {
                bumped = true

                println("Bummer")
            }
        }

        if (!bumped) {
            println("Winnar!")

            solved = true

            coordX = candidate.first
            coordY = candidate.second
        }
    }

    for (sensor in sensors) {
        if (MrManhattan(coordX, coordY, sensor.x, sensor.y) <= sensor.ManDist) {
            solved = false

            break
        }
    }

    if (solved) {
        val frequency:Long = (coordX).toLong() * 4000000 + (coordY).toLong()

        println("Frequency: $frequency")
    }
    else {
        println("You have failed, please die.")
    }
}

fun MrManhattan(x:Int, y:Int, tx:Int, ty:Int): Int {
    return abs(x - tx) + abs(y - ty)
}

class Sensor(val x: Int, val y: Int, val bx: Int, val by: Int) {
    var Top: Int = 0
    var Bottom: Int = 0
    var Left: Int = 0
    var Right: Int = 0
    var ManDist: Int = 0

    init {
        ManDist = MrManhattan(x, y, bx, by)

        this.Top = y - ManDist
        this.Bottom = y + ManDist
        this.Left = x - ManDist
        this.Right = x + ManDist
    }

    fun CheckRange(Row: Int): Pair<Int, Int>? {
        if (Row >= this.Top && Row <= this.Bottom) {
            var remDist = 1

            if (Row < this.y) {
                remDist = abs(this.Top - Row)
            }
            else if (Row > this.y) {
                remDist = abs(Row - this.Bottom)
            }

            if (remDist > 0) {
                return Pair(this.x - remDist, this.x + remDist)
            }
        }

        return null
    }
}