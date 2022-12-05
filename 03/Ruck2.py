#!/usr/bin/python3
import sys


def diff(a, b, c):
    b = set(b)
    c = set(c)
    return [aa for aa in a if aa in b and aa in c]


with open(sys.argv[1], 'r') as f:
    data = [line.rstrip() for line in f]

total = 0

for i in range(0, len(data), 3):
    first = data[i]
    second = data[i+1]
    third = data[i+2]
    inside = diff(first, second, third)
    item = ''
    c = inside[0]
    if c in first and c in second and c in third and c != item:
        item = c
        if item.islower():
            total += ord(item) - 96
        else:
            total += ord(item) - 38

print(total)
