using System.Text.Json;
using AdventOfCode2022;

var leftPants  = new List<Pants>();
var rightPants = new List<Pants>();

var sortedPants = new List<Pants>();

Pants.CreateList("/home/Fester/RiderProjects/AdventOfCode2022/AdventOfCode2022/Packet.data", leftPants, rightPants);

var leftArray  = leftPants.GetEnumerator();
var rightArray = rightPants.GetEnumerator();

var product = 0;
var divisor = 1;
var nth     = 1;

while (leftArray.MoveNext() && rightArray.MoveNext()) {
    var leftLeg  = leftArray.Current;
    var rightLeg = rightArray.Current;

    if (leftLeg.Comp(rightLeg) < 0) {
        product += leftLeg.Index;
    }
}

Console.WriteLine("Fuck off, bro... " + product);

Pants.CreateList("/home/Fester/RiderProjects/AdventOfCode2022/AdventOfCode2022/Packet.data", sortedPants, sortedPants);

var two = JsonSerializer.Deserialize<JsonElement>("[[2]]");
var six = JsonSerializer.Deserialize<JsonElement>("[[6]]");

sortedPants.Add(new Pants(2, two));
sortedPants.Add(new Pants(4, six));

Pants.Sort(sortedPants);

var sortedArray = sortedPants.GetEnumerator();

product = 0;

while (sortedArray.MoveNext()) {
    var sorted = sortedArray.Current;

    product += sorted.Index;

    if (sorted.Comp(two, sorted.pants) == 0 || sorted.Comp(six, sorted.pants) == 0) {
        divisor *= nth;
    }

    nth++;
}

product /= divisor;

Console.WriteLine("Bro... " + divisor);
