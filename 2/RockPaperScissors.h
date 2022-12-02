//
// Created by Fester on 12/2/22.
//

#ifndef ROCKPAPARSCISSORS_H
#define ROCKPAPARSCISSORS_H

#include <string>

namespace Beach {
    enum Rosham {
        Unknown  = 0,
        Rock     = 1,
        Paper    = 2,
        Scissors = 3
    }

    class RockPaperScissors {
      private:
        static auto GetValue(const std::string& Eval) -> unsigned int;

      public:
        static auto Roshambo(const std::string& Left, const std::string& Right) -> unsigned int;
    };

} // Beach

#endif //ROCKPAPARSCISSORS_H
