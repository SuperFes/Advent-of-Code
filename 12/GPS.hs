import System.Environment (getArgs)
import Data.Array
import Data.Maybe
import Data.List
import Data.Char

import Debug.Trace

type Coord = (Int, Int)
type Path  = [Coord]
type Map   = [String]

-- Load the file and convert into a map
main = do
    args <- getArgs
    file <- readFile $ head args
    let map = lines file
    let start = findChar 'S' map
    let end = findChar 'E' map
    print map
    print start
    print end
    let part1 = length $ findPath map start end
    let start = findChar 'b' map
    let end = findChar 'E' map
    let part2 = length $ findPath map start end
    print (part1 - 1)
    print (part2 + 1)

-- Find the coordinates in the map for a specific character
findChar :: Char -> Map -> Coord
findChar c m = head [ (x,y) | (y,r) <- zip [0..] m, (x,c') <- zip [0..] r, c == c' ]

-- Find a path from the start coords to the end coords
findPath :: Map -> Coord -> Coord -> Path
findPath m s e = findPath' m s e [s]
    where
        findPath' m s e p
            | s == e = p
            | otherwise = findPath' m (nextCoord m s p) e (p ++ [nextCoord m s p])

-- Given a coordinate find the next coordinate in the path
nextCoord :: Map -> Coord -> Path -> Coord
nextCoord m (x,y) p
    | not $ null candidates = head candidates
    | otherwise = (0,0)
    where
        candidates =
--                   sortBy (
--                   \a b -> compare
--                      (distance m (x,y) a p)
--                      (distance m (x,y) b p)
--                   )
--                   $
                   [ c | c <- [(x,y+1),(x+1,y),(x,y-1),(x-1,y)], not $ visited c p, validCoord m c, distance m (x,y) c p == 0 ]

-- Check if a coordinate has been visited
visited :: Coord -> Path -> Bool
visited c p = c `elem` p

-- Check if a coordinate is in the map
validCoord :: Map -> Coord -> Bool
validCoord m (x,y)
    | x < 0 || y < 0 = False
    | x >= length (m !! 0) || y >= length m = False
    | otherwise = True

-- Calculate the distance between two coordinates
distance :: Map -> Coord -> Coord -> Path -> Int
distance m (cx,cy) (x,y) p
    | current == 'S' && (next == 'a' || next == 'b') = 0
    | (current == 'x' || current == 'y') && next == 'E' = 0
    | otherwise = calcDiff current next
    where
        current = m !! cy !! cx
        next = m !! y !! x
        calcDiff c n
            | ord n - ord c < 2 = 0
            | otherwise = 1
