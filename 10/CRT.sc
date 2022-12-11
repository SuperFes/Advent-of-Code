#!/usr/bin/env scala
import scala.io.Source

object file extends App{
    var signalStrength = 0;
    var sumSignalStrength = 0;
    var registerX = 1;
    var cycle = 0;
    var toCycle = 0;
    var i = 0;

    var pixels = "";

    for (line <- Source.fromFile(args(0)).getLines()) {
      var toAdd = 0;
      var parts = line.split(" ");
      if (parts(0) == "addx") {
        toAdd = parts(1).toInt;
        toCycle = 2;
      }
      if (parts(0) == "noop") {
        toCycle = 1;
      }
      while (toCycle > 0) {
        cycle += 1;

        var under40 = cycle % 40;

        if (toCycle > 0 && under40 >= registerX && under40 <= registerX + 2) {
           pixels += "🮋";
        }
        else {
           pixels += " ";
        }

        toCycle -= 1;

        if (cycle % 40 == 0) {
           pixels += "\n";
        }

        if (cycle == 20 || cycle == 20 + i * 40) {
          signalStrength = cycle * registerX;
          sumSignalStrength += signalStrength;
          i += 1;
        }

        if (toCycle == 0 && toAdd != 0) {
          registerX += toAdd;

          toAdd = 0;
        }
      }
    }
    println(pixels);
    println("sumSignalStrength " + sumSignalStrength);
}
