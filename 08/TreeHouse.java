import java.util.*;
import java.io.*;

class Tree {
    public static void main(String[] args) {
        if (args.length != 1) {
            System.out.println("Usage: java TreeHouse.Java <file>");

            System.exit(1);
        }

        String fileName = args[0];

        int[][] grid = readFile(fileName);

        // count visible trees
        int numVisible = 0;

        int height = grid.length;
        int width = grid[0].length;

        numVisible += height * 2 + width * 2 - 4;

        for (int x = 1; x < width - 1; x++) {
            // Peanut butter in a cup
            for (int y = 1; y < grid[x].length - 1; y++) {
                // We sing this song to pump us up!
                boolean visible = true;

                // Come on Java, to your thang!
                int treeHeight = grid[y][x];

                // From the left
                for (int b = 0; b < x; b++) {
                    if (grid[y][b] >= treeHeight) {
                        visible = false;

                        break;
                    }
                }

                if (!visible) {
                    visible = true;

                    // To the right
                    for (int b = x + 1; b < width; b++) {
                        if (grid[y][b] >= treeHeight) {
                            visible = false;

                            break;
                        }
                    }
                }

                if (!visible) {
                    visible = true;

                    // From the top
                    for (int b = 0; b < y; b++) {
                        if (grid[b][x] >= treeHeight) {
                            visible = false;

                            break;
                        }
                    }
                }

                if (!visible) {
                    visible = true;

                    // To the bottom
                    for (int b = y + 1; b < height; b++) {
                        if (grid[b][x] >= treeHeight) {
                            visible = false;

                            break;
                        }
                    }
                }

                if (visible) {
                    numVisible++;
                }
            }
        }

        System.out.println("# of visible trees: " + numVisible);

        int bestView = 0;

        for (int x = 1; x < width - 1; x++) {
            // Peanut butter in a cup
            for (int y = 1; y < grid[x].length - 1; y++) {
                // We sing this song to pump us up!
                int left   = 0;
                int top    = 0;
                int right  = 0;
                int bottom = 0;


                // Come on Java, to your thang!
                int treeHeight = grid[y][x];

                // From the left
                for (int b = x - 1; b  >= 0; b--, left++) {
                    if (grid[y][b] >= treeHeight) {
                        left++;

                        break;
                    }
                }

                // To the right
                for (int b = x + 1; b < width; b++, right++) {
                    if (grid[y][b] >= treeHeight) {
                        right++;

                        break;
                    }
                }

                // From the top
                for (int b = y - 1; b >= 0; b--, top++) {
                    if (grid[b][x] >= treeHeight) {
                        top++;

                        break;
                    }
                }

                for (int b = y + 1; b < height; b++, bottom++) {
                    if (grid[b][x] >= treeHeight) {
                        bottom++;

                        break;
                    }
                }

                int currViewValue = left * top * right * bottom;

                if (currViewValue > bestView) {
                    bestView = currViewValue;
                }
            }
        }

        System.out.println("Best tree view score: " + bestView);
    }

    static int[][] readFile(String fileName) {
        try {
            File file = new File(fileName);
            Scanner sc = new Scanner(file);

            int rows = 0, cols = 0;

            while (sc.hasNextLine()) {
                String line = sc.nextLine();
                rows++;
                cols = line.length();
            }

            sc.close();

            int[][] grid = new int[rows][cols];

            Scanner s = new Scanner(file);

            for (int i = 0; i < rows; i++) {
                String line = s.nextLine();

                for (int j = 0; j < cols; j++) {
                    grid[i][j] = Character.getNumericValue(line.charAt(j));
                }
            }
            s.close();

            return grid;
        } catch (Exception e) {
            System.out.println("Error reading file");

            System.exit(1);
        }

        return null;
    }
}
