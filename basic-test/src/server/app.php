<?php
namespace Befeni\Basic\Calculator;
use Generator;



/**
 * Return the result of a single operation
 */
function operate(float $prev, array $operation): float {
    $operand = $operation[1];
    $operator = $operation[0];
    switch ($operator) {
        case 'add':
            $result = $prev + $operand;
            break;
        case 'substract':
            $result = $prev - $operand;
            break;
        case 'multiply':
            $result = $prev * $operand;
            break;
        case 'divide':
            $result = $prev / $operand;
            break;
        default:
            throw new \Exception('invalid operator');
    }
    return $result;
}


/**
 * Given the filename of an existing file,
 * this function should return an array of opperations
 */
function parse_file(string $filepath, &$first_operand): array {
    $tmp = [];
    $operations = [];
    foreach(read_line_from_file($filepath) as $k => $line) {
        if($tmp[0] == 'apply') {
            throw new \Error('no more lines allowed after "apply" at line ' . $k);
        }
        $operations[] = $tmp = parse_operation($line);
    }
    if($tmp[0] != 'apply') {
        throw new \Error('last line must start with "apply" at line ' . $k);
    }
    $first_operand = array_pop($operations)[1];
    return $operations;
}


/**
 * Parse and validates an operation string
 */
function parse_operation(string $line): array {
    $allowed_operators = ['add', 'substract', 'multiply', 'divide', 'apply'];
    $row = explode(" ", rtrim($line, "\n"));
    if(count($row) !== 2) throw new \Error('invalid line');
    if(!in_array($row[0], $allowed_operators)) throw new \Error('invalid operator. Expecting: '.implode(' , ', $allowed_operators). ' but got '.$row[0]);
    if(!is_numeric($row[1])) throw new \Error('invalid operand, must be a number. Got '. $row[1].' instead');
    $row[1] = floatval($row[1]);
    return $row;
}


/**
 * Generator used to read one line at a time of an existing file
 */
function read_line_from_file(string $filename): Generator {
    $file = fopen('/var/tmp/calculator/'.$filename, 'r');
    while (($line = fgets($file)) !== false) {
        yield $line;
    }
    fclose($file);
}

