//
// Created by Fester on 12/2/22.
//

#include "RockPaperScissors.h"

namespace Beach {
    auto RockPaperScissors::GetValue(const std::string& Eval) -> unsigned int {
        if (Eval == "A" || Eval == "X") {
            return Rosham::Rock;
        }
        else if (Eval == "B" || Eval == "Y") {
            return Rosham::Paper;
        }
        else if (Eval == "C" || Eval == "Z") {
            return Rosham::Scissors;
        }

        return Rosham::Unknown;
    }

    auto RockPaperScissors::GetFormulaXValue(const unsigned int Left, const std::string Right) -> unsigned int {
        Bo Strategy;
        unsigned int ShamWow = 0;

        if (Right == "X") {
            Strategy = Bo::Lose;
        }
        else if (Right == "Y") {
            Strategy = Bo::Draw;
        }
        else if (Right == "Z") {
            Strategy = Bo::Win;
        }

        if (Strategy == Bo::Draw) {
            return Left;
        }

        if (Strategy == Bo::Lose) {
            if (Left == Rosham::Rock) {
                ShamWow = Rosham::Scissors;
            }

            if (Left == Rosham::Paper) {
                ShamWow = Rosham::Rock;
            }

            if (Left == Rosham::Scissors) {
                ShamWow = Rosham::Paper;
            }
        }

        if (Strategy == Bo::Win) {
            if (Left == Rosham::Rock) {
                ShamWow = Rosham::Paper;
            }

            if (Left == Rosham::Paper) {
                ShamWow = Rosham::Scissors;
            }

            if (Left == Rosham::Scissors) {
                ShamWow = Rosham::Rock;
            }
        }

        return ShamWow;
    }

    auto RockPaperScissors::Roshambo(const std::string& Left, const std::string& Right) -> unsigned int {
        unsigned int First;
        unsigned int Second;

        First = GetValue(Left);
        Second = GetValue(Right);

        if (First == Second) {
            return Bo::Draw + Second;
        }

        if (First == Rosham::Rock) {
            if (Second == Rosham::Paper) {
                return Bo::Win + Rosham::Paper;
            }
            else {
                return Bo::Lose + Rosham::Scissors;
            }
        }
        else if (First == Rosham::Paper) {
            if (Second == Rosham::Scissors) {
                return Bo::Win + Rosham::Scissors;
            }
            else {
                return Bo::Lose + Rosham::Rock;
            }
        }
        else if (First == Rosham::Scissors) {
            if (Second == Rosham::Rock) {
                return Bo::Win + Rosham::Rock;
            }
            else {
                return Bo::Lose + Rosham::Paper;
            }
        }

        return -1;
    }

    auto RockPaperScissors::SlickRoshambo(const std::string& Left, const std::string& Right) -> unsigned int {
        unsigned int First;
        unsigned int Second;

        First = GetValue(Left);
        Second = GetFormulaXValue(First, Right);

        if (First == Second) {
            return Bo::Draw + Second;
        }

        if (First == Rosham::Rock) {
            if (Second == Rosham::Paper) {
                return Bo::Win + Rosham::Paper;
            }
            else {
                return Bo::Lose + Rosham::Scissors;
            }
        }
        else if (First == Rosham::Paper) {
            if (Second == Rosham::Scissors) {
                return Bo::Win + Rosham::Scissors;
            }
            else {
                return Bo::Lose + Rosham::Rock;
            }
        }
        else if (First == Rosham::Scissors) {
            if (Second == Rosham::Rock) {
                return Bo::Win + Rosham::Rock;
            }
            else {
                return Bo::Lose + Rosham::Paper;
            }
        }

        return -1;
    }
} // Beach
