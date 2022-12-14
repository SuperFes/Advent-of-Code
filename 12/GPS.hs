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
    let part1 = findPath map start end
    print part1
    let start = findChar 'E' map
    let end = findChar 'b' map
    let part2 = findPathDown map start end
    print part2
    print (length part1 - 1)
    print (length part2)

-- Find the coordinates in the map for a specific character
findChar :: Char -> Map -> Coord
findChar c m = head [ (x,y) | (y,r) <- zip [0..] m, (x,c') <- zip [0..] r, c == c' ]

-- Find a path from the start coords to the end coords
findPath :: Map -> Coord -> Coord -> Path
findPath m s e = findPath' m s e [s]
    where
        findPath' m s e p =
            let
                next     = nextCoord m s p
                next'    = sortBy (
                                  \a b -> compare
                                  (findPath' m a e p)
                                  (findPath' m b e p)
                         ) $ nextCoords m s p
                nextChar = mapChar m next == mapChar m e
                path     = p ++ [next]
            in
                if (s == e || mapChar m s == mapChar m e)
                then p
                else if (length next' > 0)
                then p ++ next'
                else if (nextCoord m s p == (-1, -1))
                then []
                else findPath' m next e path

-- Find a path from the start coords to the end coords
findPathDown :: Map -> Coord -> Coord -> Path
findPathDown m s e = findPathDown' m s e [s]
    where
        findPathDown' m s e p =
            let
                next  = nextCoordDown m s p
                next' = nextCoordDown m next p
                path  = p ++ [next] ++ [next']
            in
                if (s == e)
                then p
                else if (nextCoordDown m s p == (-1, -1))
                then []
                else if (mapChar m s == mapChar m e)
                then p
                else findPathDown' m next' e path

-- Given a coordinate find the next coordinate in the path
nextCoord :: Map -> Coord -> Path -> Coord
nextCoord m (x,y) p
    | not $ null candidates = head $ filter (\c -> distance m (x,y) c p >= 0) candidates
    | otherwise = (-1,-1)
    where
        candidates =
                   [ c | c <- [(x,y+1),(x+1,y),(x-1,y),(x,y-1)], not $ visited c p, validCoord m c ]

-- Given a coordinate find the next coordinate in the path
nextCoords :: Map -> Coord -> Path -> Path
nextCoords m (x,y) p
    | not $ null candidates = candidates
    | otherwise = [(-1,-1)]
    where
        candidates =
                   [ c | c <- [(x,y+1),(x+1,y),(x-1,y),(x,y-1)], not $ visited c p, validCoord m c ]

-- Given a coordinate find the next coordinate in the path
nextCoordDown :: Map -> Coord -> Path -> Coord
nextCoordDown m (x,y) p
    | not $ null candidates = head candidates
    | otherwise = (-1,-1)
    where
        candidates =
                   sortBy (
                       \a b -> compare
                       (distanceDown m (x,y) a p)
                       (distanceDown m (x,y) b p)
                   )
                   $
                   [ c | c <- [(x-1,y),(x,y-1),(x,y+1),(x+1,y)], not $ visited c p, validCoord m c, distanceDown m (x,y) c p >= 0 ]

-- Retrive the Char from a map point
mapChar :: Map -> Coord -> Char
mapChar m (x, y) = m !! y !! x 

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
    | ord next == ord current +1 = 0
    | next == current = 1
    | otherwise = calcDiff current next
    where
        current = m !! cy !! cx
        next = m !! y !! x
        calcDiff c n
            | c == 'E' && (n == 'z' || n == 'y') = 0
            | c == 'z' && n == 'E' = 0
            | c == 'E' && (n == 'z') = 0
            | c == 'S' && (n == 'a' || n == 'b') = 1
            | ord n - ord c == 1 = 2
            | otherwise = -1

-- Calculate the distance between two coordinates
distanceDown :: Map -> Coord -> Coord -> Path -> Int
distanceDown m (cx,cy) (x,y) p
    | ord next == ord current -1 = 0
    | next == current = 1
    | otherwise = calcDiff current next
    where
        current = m !! cy !! cx
        next = m !! y !! x
        calcDiff c n
            | c == 'E' && (n == 'z' || n == 'y') = 0
            | ord c - ord n == 1 = 2
            | otherwise = -1
