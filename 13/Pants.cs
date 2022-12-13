using System.Text.Json;
using System.Text.Json.Nodes;

namespace AdventOfCode2022; 

public class Pants {
    public          JsonElement pants;
    public readonly int         Index;

    public Pants(int index, JsonElement newPair) {
        Index = index;
        pants = newPair;
    }

    public int Comp(JsonElement leftPair, JsonElement rightPair) {
        JsonElement newPants;

        switch (leftPair.ValueKind, rightPair.ValueKind) {
            case (JsonValueKind.Null, not JsonValueKind.Null):
                return -1;
            case (not JsonValueKind.Null, JsonValueKind.Null):
                return 1;
            case (JsonValueKind.Number, JsonValueKind.Number):
                return Comparer<int>.Default.Compare(leftPair.GetInt32(), rightPair.GetInt32());
            case(JsonValueKind.Number, JsonValueKind.Array):
                newPants = JsonSerializer.Deserialize<JsonElement>("[" + leftPair.GetInt32() + "]");
                return Comp(newPants, rightPair);
            case(JsonValueKind.Array, JsonValueKind.Number):
                newPants = JsonSerializer.Deserialize<JsonElement>("[" + rightPair.GetInt32() + "]");
                return Comp(leftPair, newPants);
            case(JsonValueKind.Array, JsonValueKind.Array):
                var leftArray  = leftPair.EnumerateArray();
                var rightArray = rightPair.EnumerateArray();

                while (leftArray.MoveNext() && rightArray.MoveNext()) {
                    var leftPants = leftArray.Current;
                    var rightPants = rightArray.Current;

                    var compair = Comp(leftPants, rightPants);

                    if (compair != 0) {
                        return compair;
                    }
                }

                var legCount = leftArray.Count() - rightArray.Count();

                return legCount switch {
                    0   => 0,
                    < 0 => -1,
                    _   => 1
                };
            default:
                return 0;
        };
    }

    public int Comp(Pants pairs) {
        return Comp(pants, pairs.pants);
    }

    public static void CreateList(string filename, List<Pants> leftPants, List<Pants> rightPants) {
        var lines = File.ReadAllLines(filename);

        var left      = true;
        var numerator = 1;

        foreach (var line in lines) {
            if (line.Length == 0) {
                continue;
            }

            var jason = JsonSerializer.Deserialize<JsonElement>(line);

            var newPair = new Pants(numerator, jason);

            if (left) {
                leftPants.Add(newPair);
            }
            else {
                rightPants.Add(newPair);;

                numerator++;
            }

            left = !left;
        }
    }

    public static void Sort(List<Pants> sorted) {
        sorted.Sort(Comparer);
    }

    private static int Comparer(Pants x, Pants y) {
        return x.Comp(y);
    }
}
