#!/usr/bin/python3
import sys


with open(sys.argv[1], 'r') as f:
    data = [line.rstrip() for line in f]

total = 0

for line in data:
    first = line[:int(len(line) / 2)]
    second = line[int(len(line) / 2):]
    item = ''
    for c in first:
        if c in second and c != item:
            item = c
            if item.islower():
                total += ord(item) - 96
            else:
                total += ord(item) - 38

print(total)
