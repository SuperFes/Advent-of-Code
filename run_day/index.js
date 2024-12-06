#!/usr/bin/env node
import {Command} from "commander";
import {exec}    from "node:child_process";

const program = new Command();

program
    .command('run <day>')
    .description('Run a day')
    .action((day) => {
        exec(`./day${day}/index.js`);
    });

program.parse(process.argv);
