package main

import (
	"fmt"
	"io/ioutil"
	"os"
	"strconv"
	"strings"
)

func main() {
	args := os.Args
	if len(args) != 2 {
		fmt.Println("Please specify the input file.")
		return
	}
	file, err := ioutil.ReadFile(args[1])
	if err != nil {
		fmt.Printf("Error reading file: %s", err)
		return
	}
	lines := strings.Split(strings.Trim(string(file), "\n"), "\n")

	var overlapCount = 0

	for _, line := range lines {
		pair := strings.Split(line, ",")
		range_ := strings.Split(pair[0], "-")
		start, err := strconv.Atoi(range_[0])
		if err != nil {
			fmt.Printf("Error converting string to int: %s", err)
			return
		}
		end, err := strconv.Atoi(range_[1])
		if err != nil {
			fmt.Printf("Error converting string to int: %s", err)
			return
		}
		range_ = strings.Split(pair[1], "-")
		start2, err := strconv.Atoi(range_[0])
		if err != nil {
			fmt.Printf("Error converting string to int: %s", err)
			return
		}
		end2, err := strconv.Atoi(range_[1])
		if err != nil {
			fmt.Printf("Error converting string to int: %s", err)
			return
		}

		if start <= end2 && end >= start2 {
			overlapCount++
		} else if start2 <= end && end2 >= start {
			overlapCount++
		}
	}

	fmt.Println(overlapCount)
}
