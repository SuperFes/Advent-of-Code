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

    auto RockPaperScissors::Roshambo(const std::string& Left, const std::string& Right) -> unsigned int {
        unsigned int First;
        unsigned int Second;

        First = GetValue(Left);
        Second = GetValue(Right);

        if (First == Second) {
            return First + Second;
        }

        if (First == Rosham::Rock) {
            if (Second == Rosham::Paper) {
                return 6 + Rosham::Paper;
            }
            else {
                return Rosham::Scissors;
            }
        }
        else if (First == Rosham::Paper) {
            if (Second == Rosham::Scissors) {
                return 6 + Rosham::Scissors;
            }
            else {
                return Rosham::Rock;
            }
        }
        else if (First == Rosham::Scissors) {
            if (Second == Rosham::Rock) {
                return 6 + Rosham::Rock;
            }
            else {
                return Rosham::Paper;
            }
        }
    }
} // Beach
