<?php

namespace day11;

use common\Day;

class Day11 extends Day{

    /** @var array<mixed> */
    private array $monkeyItems;
    /** @var array<mixed> */
    private array $monkeyInspections;
    /** @var array<mixed> */
    private array $monkeyRules;
    private int $currentMonkeyId = 0;
    private bool $monkeyGetsBored;

    protected function loadData(): void 
    {
        parent::loadData();
        $this->loadMonkeyData();
    }

    public function part1(): int
    {
        $this->startRounds(20);
        return $this->getScore();
    }

    public function part2(): string
    {
        return "ELPLZGZL";
    }

    public function startRounds(int $numberOfRound=1, bool $getsBored = TRUE): void {
        $this->monkeyGetsBored = $getsBored;
        for($round=1;$round<=$numberOfRound;$round++) {
            $this->inspectItems();
        }
    }

    /**
     * @return array<mixed>
     */
    public function getItemList(): array
    {
        return $this->monkeyItems;
    }

    private function inspectItems(): void {
        foreach($this->monkeyItems as $monkeyId => &$items) {
            if ( empty($this->monkeyItems[$monkeyId] ) ) {
				continue;
			}
            foreach($items as $itemId => &$item) {
                $this->monkeyInspections[$monkeyId] += 1;
                $worryLevel = $this->applyOperation($monkeyId, $item);
                $worryLevel = $this->getsBored($worryLevel);
                $this->actOnItem($monkeyId, $itemId, $worryLevel);
            }
        }
    }

    private function actOnItem(int $monkeyId, int $itemId, int $worryLevel): void {
        if($worryLevel % $this->monkeyRules[$monkeyId]["test"] == 0) {
            $toMonkey = $this->monkeyRules[$monkeyId]["testTrue"];
        } else {
            $toMonkey = $this->monkeyRules[$monkeyId]["testFalse"];
        }

        unset($this->monkeyItems[$monkeyId][$itemId]);
        $this->monkeyItems[$toMonkey][] = $worryLevel;
    }

    private function getsBored(int $value): int {
        $superModulo = array_product(array_column($this->monkeyRules, "test"));
        return ($this->monkeyGetsBored ? floor($value / 3) : $value % $superModulo);
    }
    private function applyOperation(int $monkeyId, int $value): int {
        $part1 = 0;
        $part2 = 0;
        $operation = $this->monkeyRules[$monkeyId]["operation"];
        preg_match("/[a-z]+ = ([a-z0-9]+) (.*) ([a-z0-9]+)/", $operation, $matches);

        if($matches[1] == 'old') { $part1 = $value; }
        if($matches[3] == 'old') { $part2 = $value; } else { $part2 = $matches[3]; }

        switch($matches[2]){
            case "+":
                return $part1 + $part2;
            case "-":
                return $part1 - $part2;
            case "*":
                return $part1 * $part2;
            case "/":
                return $part1 / $part2;
        }
        return 0;
    }

    private function loadMonkeyData(): void {
        foreach($this->inputData as $key => $line) {
            $indent = strspn($line, " ");
            if(strlen($line) > 0) {
                switch($indent) {
                    case 0:
                        // new monkey
                        $monkeyId = substr($line,7,1);
                        $this->monkeyRules[$monkeyId] = [];
                        $this->monkeyItems[$monkeyId] = [];
                        $this->monkeyInspections[$monkeyId] = 0;
                        $this->currentMonkeyId = (int) $monkeyId;
                        break;
                    case 2:
                        // starting items
                        // operation
                        // test
                        $operator = trim(substr($line,0,strpos($line, ":",0)));
                        if($operator == 'Starting items') {
                            $items = explode(",", substr($line, strpos($line, ":",0)+1));
                            foreach($items as $item) {
                                $this->addItemToMonkeyNo($this->currentMonkeyId, $item);
                            }
                        }
                        if($operator == 'Operation') {
                            $rule = substr($line, strpos($line, ":",0)+1);
                            $this->monkeyRules[$this->currentMonkeyId]["operation"] = trim($rule);
                        }
                        if($operator == 'Test') {
                            $rule = substr($line, strpos($line, ":",0)+1);
                            $rule = str_replace("divisible by ", "", $rule);
                            $this->monkeyRules[$this->currentMonkeyId]["test"] = trim($rule);
                        }

                        break;
                    case 4:
                        // tests
                        $toMonkey = substr($line, -1);
                        if(strpos($line, "If true")) {
                            $this->monkeyRules[$this->currentMonkeyId]["testTrue"] = trim($toMonkey);
                        } else {
                            $this->monkeyRules[$this->currentMonkeyId]["testFalse"] = trim($toMonkey);
                        }
                        break;
                }
            }
        }
    }

    private function addItemToMonkeyNo(int $monkeyId, string $item): void {
        $this->monkeyItems[$monkeyId][] = trim($item);
    }

    public function getScore(): int {
        arsort($this->monkeyInspections,);
        $top2 = array_slice($this->monkeyInspections,0,2);
        return $top2[0] * $top2[1];
    }
}