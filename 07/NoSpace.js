#!/usr/bin/env node
const fs = require('fs');

const fileName = process.argv[2];

// check for valid file
if (!fileName) {
    console.log('Please provide a valid filename');
    process.exit(1);
}

fs.readFile(fileName, 'utf8', (err, contents) => {
    if (err) {
        console.error(err);
    }
    const commands = contents.split('\n');

    commands.splice(0, 1);

    const tree = {
        path: '/',
        parent: null,
        files: {},
        directories: {},
        totalSize: 0
    };

    let cwd = tree; // First, I am (g)root
    let listing = false;

    commands.forEach(command => {
        if (command === '') {
            return;
        }

        const parsedCommand = parseCommand(command);

        if (parsedCommand.type === '$') {
            listing = false;

            if (parsedCommand.value === "cd") {
                if (parsedCommand.extra[0] === '..') {
                    if (cwd.parent !== null) {
                        cwd = cwd.parent;
                    }
                }
                else {
                    cwd = cwd.directories[parsedCommand.extra[0]];
                }
            }
            else if (parsedCommand.value === "ls") {
                listing = true;
                // Kinda stupid, but I'll roll with it
            }
        }
        else if (listing) {
            const filesAndDirectories = command.split(' ');

            if (filesAndDirectories[0] === 'dir') {
                const dirName = filesAndDirectories[1];

                cwd.directories[dirName] = {
                    path: cwd.path + (cwd.path === "/" ? "" : "/") + dirName,
                    parent: cwd,
                    files: {},
                    directories: {},
                    totalSize: 0
                };
            }
            else {
                const fileSize = parseInt(filesAndDirectories[0], 10);
                const fileName = filesAndDirectories[1];

                cwd.files[fileName] = fileSize;

                addToTotalSize(cwd, fileSize);
            }
        }
    });

    const sizeOfDirectory = calculateSize(tree);

    console.log(`Total size: ${sizeOfDirectory}`);

    let sizeToFind = 30000000 - (70000000 - tree.totalSize);

    const pathToRemove = findMinSize(tree, sizeToFind).path;
    const sizeToRemove = findMinSize(tree, sizeToFind).totalSize;

    console.log(`Path to remove: ${pathToRemove} (${sizeToRemove} bytes)`);

    const sizeOfMaxDirectory = calculateSize(tree, 100000);

    console.log(`Total size if dirs smaller than 100000: ${sizeOfMaxDirectory}`);
});

const parseCommand = command => {
    const type = command.split(' ')[0];
    const value = command.split(' ')[1];
    const extra = command.split(' ').splice(2);
    return {
        type,
        value,
        extra
    };
};

const calculateSize = (tree, maxSize = 0) => {
    if (maxSize === 0) {
        return tree.totalSize;
    }

    let totalSize = 0;

    if (tree.totalSize <= maxSize) {
        totalSize += tree.totalSize;
    }

    Object.keys(tree.directories).forEach(directory => {
        totalSize += calculateSize(tree.directories[directory], maxSize);
    });

    return totalSize;
};

const addToTotalSize = (tree, fileSize) => {
    tree.totalSize += fileSize;

    if (tree.parent !== null) {
        addToTotalSize(tree.parent, fileSize);
    }
};

const findMaxSize = (tree, maxSize) => {
    if (tree.totalSize <= maxSize) {
        console.log(tree.path, maxSize, tree.totalSize);

        return tree.path;
    }

    let keys = Object.keys(tree.directories);
    let keyLen = keys.length;

    for (let k = 0; k < keyLen; k++) {
        let removePath = findMaxSize(tree.directories[keys[k]], maxSize);

        if (removePath) {
            return removePath;
        }
    }

    return undefined;
};

const findMinSize = (tree, minSize) => {
    let smallest = null;

    if (tree.totalSize >= minSize) {
        smallest = tree;
    }

    let keys = Object.keys(tree.directories);
    let keyLen = keys.length;

    for (let k = 0; k < keyLen; k++) {
        let removePath = findMinSize(tree.directories[keys[k]], minSize);

        if (removePath !== undefined) {
            if (!smallest || removePath.totalSize < smallest.totalSize) {
                smallest = removePath;
            }
        }
    }

    if (smallest) {
        return smallest;
    }

    return undefined;
};

return 0;
