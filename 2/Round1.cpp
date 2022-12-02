#include <iostream>
#include <fstream>
#include <string>

#include "RockPaperScissors.h"

int main(void) {
    std::ifstream Guide("Roshambo.data", std::ios_base::in);

    unsigned int Total = 0;

    do {
        std::string Left;
        std::string Right;

        Guide >> Left >> Right;

        if (Left.empty() || Right.empty()) {
            continue;
        }

        std::cout << Left << " " << Right  << " = " << Beach::RockPaperScissors::Roshambo(Left, Right) << std::endl;

        Total += Beach::RockPaperScissors::Roshambo(Left, Right);
    }
    while (!Guide.eof());

    std::cout << "Total: " << Total << "." << std::endl;

    return 0;
}
