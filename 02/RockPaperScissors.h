#ifndef RockPaperScissors_h
#define RockPaperScissors_h

#include <string>

namespace Beach {
    enum Rosham {
        Unknown  = 0,
        Rock     = 1,
        Paper    = 2,
        Scissors = 3
    };

    enum Bo {
        None = -1,
        Win  = 6,
        Lose = 0,
        Draw = 3
    };

    class RockPaperScissors {
      private:
        static auto GetValue(const std::string& Eval) -> unsigned int;

        static auto GetFormulaXValue(const unsigned int Left, const std::string Right) -> unsigned int;

        static auto Calcubromulate(unsigned int First, unsigned int Second) -> unsigned int;

      public:
        static auto Roshambo(const std::string& Left, const std::string& Right) -> unsigned int;

        static auto SlickRoshambo(const std::string& Left, const std::string& Right) -> unsigned int;
    };

} // Beach

#endif //RockPaperScissors_h