<?php

namespace day21;

use common\Day;

class Day21 extends Day {
    /** @var array<mixed> */
    private array $monkeyNumbers;
    /** @var array<mixed> */
    private array $monkeys;
    private int $inputLength;

    protected function LoadData(): void
    {
        parent::loadData();

        foreach($this->inputData as $value) {
            [$monkeyName, $action] = explode(": ", $value);

            if(intval($action)) {
                $this->monkeyNumbers[$monkeyName] = $action;
            } else {
                preg_match("#([a-zA-Z]{4}) ([+-\/*]) ([a-zA-Z]{4})#", $value, $matches);
                $this->monkeys[$monkeyName] = [
                    "monkey1" => $matches[1],
                    "monkey2" => $matches[3],
                    "operator" => $matches[2]
                ];
            }
        }
        $this->inputLength = count($this->inputData);
    }

    public function part1(): int {
        // getRootNumber
        $i=0;
            while(count($this->monkeyNumbers) < $this->inputLength) {
            foreach($this->monkeys as $monkeyName => $details) {
                $monkey1 = $details["monkey1"];
                $monkey2 = $details["monkey2"];
                
                $this->monkeys[$monkeyName]["monkey1"] = $this->checkIfMonkeyHasNumber($monkey1);
                $this->monkeys[$monkeyName]["monkey2"] = $this->checkIfMonkeyHasNumber($monkey2);

                $this->canCalculate($monkeyName, $monkey1, $monkey2);
            }
            $i++;
        }
        return $this->monkeyNumbers["root"];
    }

    private function canCalculate(string $monkeyName, int $monkey1, int $monkey2): void {
        if(intval($monkey1) && intval($monkey2)) {
            $operator = $this->monkeys[$monkeyName]["operator"];
            $outcome = 0;
            switch($operator) {
                case "+":
                    $outcome = $monkey1 + $monkey2;
                    break;
                case "/":
                    $outcome = $monkey1 / $monkey2;
                    break;
                case "-":
                    $outcome = $monkey1 - $monkey2;
                    break;
                case "*":
                    $outcome = $monkey1 * $monkey2;
                    break;
            }

            $this->monkeyNumbers[$monkeyName] = $outcome;
            unset($this->monkeys[$monkeyName]);
        }
    }

    private function checkIfMonkeyHasNumber(string $monkeyToCheck): string {
        if(intval($monkeyToCheck)) {
            return $monkeyToCheck;
        } else {
            if(!isset($this->monkeyNumbers[$monkeyToCheck])) {
                return $monkeyToCheck;
            } else {
                return $this->monkeyNumbers[$monkeyToCheck];
            }
        }
    }

    public function part2(): int 
    {
        return 0;
    }
}