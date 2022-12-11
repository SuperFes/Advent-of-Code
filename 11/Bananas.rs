use std::collections::{BTreeMap, VecDeque};

#[derive(Debug, Clone)]
enum OpValue {
    Old,
    Lit(u64),
}

#[derive(Debug, Clone)]
enum Op {
    Add,
    Mul,
}

impl Op {
    fn new(op: &str) -> Self {
        match op {
            "+" => Self::Add,
            "*" => Self::Mul,
            _ => unreachable!(),
        }
    }
}

#[derive(Debug, Clone)]
struct Operation {
    left: OpValue,
    op: Op,
    right: OpValue,
}
impl Operation {
    fn new(left: OpValue, op: Op, right: OpValue) -> Self {
        Self { left, op, right }
    }

    fn evaluate(&self, old: u64) -> u64 {
        let left = match self.left {
            OpValue::Old => old,
            OpValue::Lit(val) => val,
        };
        let right = match self.right {
            OpValue::Old => old,
            OpValue::Lit(val) => val,
        };
        match self.op {
            Op::Add => left + right,
            Op::Mul => left * right,
        }
    }
}

enum Divisor {
    Three,
    Custom(u64)
}

#[derive(Debug, Clone)]
struct Monkee {
    number: u64,
    items: VecDeque<u64>,
    operation: Operation,
    divisor: u64,
    true_target: u64,
    false_target: u64,
    inspected: u64,
}

impl Monkee {
    fn new(
        number: u64,
        items: VecDeque<u64>,
        operation: Operation,
        divisor: u64,
        true_target: u64,
        false_target: u64,
    ) -> Self {
        Self {
            number,
            items,
            operation,
            divisor,
            true_target,
            false_target,
            inspected: 0,
        }
    }

    fn inspect(&mut self, thrown: &mut BTreeMap<u64, VecDeque<u64>>, bored: Divisor) {
        while let Some(item) = self.items.pop_front() {
            let new = self.operation.evaluate(item);
            let new = match bored {
                Divisor::Three => new / 3,
                Divisor::Custom(d) => new % d
            };
            if new % self.divisor == 0 {
                thrown.entry(self.true_target).or_default().push_back(new);
            } else {
                thrown.entry(self.false_target).or_default().push_back(new);
            }
            self.inspected += 1;
        }
    }

    fn inspect_no_worries(&mut self, thrown: &mut BTreeMap<u64, VecDeque<u64>>) {
        while let Some(item) = self.items.pop_front() {
            let new = self.operation.evaluate(item);
            if new % self.divisor == 0 {
                thrown.entry(self.true_target).or_default().push_back(new);
            } else {
                thrown.entry(self.false_target).or_default().push_back(new);
            }
            self.inspected += 1;
        }
    }
}

impl std::fmt::Display for Monkee {
    fn fmt(&self, f: &mut std::fmt::Formatter<'_>) -> std::fmt::Result {
        write!(f, "Monkee: {}: {:?}", self.number, self.items)
    }
}

pub struct Monkees;

impl Monkees {
    fn part_1(&self, input: &str) -> String {
        let mut monkees = parse_monkees(input);
        let mut thrown: BTreeMap<u64, VecDeque<u64>> = BTreeMap::new();
        for _round in 0..20 {
            for (number, monkee) in monkees.iter_mut() {
                let received = thrown.entry(*number).or_default();
                monkee.items.extend(received.drain(0..));
                monkee.inspect(&mut thrown, Divisor::Three);
            }
        }
        let mut monkees = monkees.into_iter().collect::<Vec<_>>();
        monkees.sort_by_key(|(_, monkee)| monkee.inspected);
        monkees
            .into_iter()
            .rev()
            .take(2)
            .map(|pair| pair.1.inspected as u128)
            .product::<u128>()
            .to_string()
    }

    fn part_2(&self, input: &str) -> String {
        let mut monkees = parse_monkees(input);
        let mut thrown: BTreeMap<u64, VecDeque<u64>> = BTreeMap::new();
        let m = monkees.iter().fold(1, |acc, monkee|{
            if acc % monkee.1.divisor == 0 {acc}
            else {acc * monkee.1.divisor}
        }
        );
        for _round in 0..10000 {
            for (number, monkee) in monkees.iter_mut() {
                let received = thrown.entry(*number).or_default();
                monkee.items.extend(received.drain(0..));
                monkee.inspect(&mut thrown, Divisor::Custom(m));
            }
        }
        let mut monkees = monkees.into_iter().collect::<Vec<_>>();
        monkees.sort_by_key(|(_, monkee)| monkee.inspected);
        monkees
            .into_iter()
            .rev()
            .take(2)
            .map(|pair| pair.1.inspected as u128)
            .product::<u128>()
            .to_string()
    }
}

fn parse_monkees(input: &str) -> BTreeMap<u64, Monkee> {
    input
        .split("\n\n")
        .map(|lines| {
            let mut lines = lines.lines();
            let number: u64 = lines
                .next()
                .unwrap()
                .trim_start_matches("Monkey ")
                .trim_end_matches(':')
                .parse()
                .unwrap();

            let items: VecDeque<u64> = lines
                .next()
                .unwrap()
                .trim_start_matches("  Starting items: ")
                .split(", ")
                .map(|i| i.parse().unwrap())
                .collect();

            let mut operation = lines
                .next()
                .unwrap()
                .trim_start_matches("  Operation: new = ")
                .split_ascii_whitespace();
            let left = operation
                .next()
                .unwrap()
                .parse::<u64>()
                .map_or(OpValue::Old, |val| OpValue::Lit(val));
            let op = Op::new(operation.next().unwrap());
            let right = operation
                .next()
                .unwrap()
                .parse::<u64>()
                .map_or(OpValue::Old, |val| OpValue::Lit(val));
            let operation = Operation::new(left, op, right);

            let divisor: u64 = lines
                .next()
                .unwrap()
                .trim_start_matches("  Test: divisible by ")
                .parse()
                .unwrap();

            let true_target: u64 = lines
                .next()
                .unwrap()
                .trim_start_matches("    If true: throw to monkey ")
                .parse()
                .unwrap();

            let false_target: u64 = lines
                .next()
                .unwrap()
                .trim_start_matches("    If false: throw to monkey ")
                .parse()
                .unwrap();
            (
                number,
                Monkee::new(number, items, operation, divisor, true_target, false_target),
            )
        })
        .collect()
}

fn main() {
    let args: Vec<String> = std::env::args().collect();
    let file_name = &args[1];
    let input = std::fs::read_to_string(file_name).unwrap();
    let monkees = Monkees;
    println!("Part 1: {}\nPart 2: {}", monkees.part_1(&input), monkees.part_2(&input));
}
