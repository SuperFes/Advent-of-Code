import CCPU from './lib/Emulate.js';

export default function LawnmowerMan(Registers, Program) {
    console.debug(Registers, Program);

    const CPU = new CCPU(Registers);

    CPU.Run(Program);

    CPU.Print();

    return true;
}
