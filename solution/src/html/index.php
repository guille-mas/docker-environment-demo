<?php 
namespace Befeni\Basic\Controller;

require_once(__DIR__ . "/../calculator.php");

/**
 * Proof of concept implementation of an api endpoint that will return the result of ./data/input.txt
 */

/**
 * Given a valid filename, return the result of 
 * every operation applied to the first operand
 */
function get_result_from_file(string $filename): float {
    $operations = \Befeni\Basic\Calculator\parse_file($filename, $first_operand);
    return array_reduce($operations, "\Befeni\Basic\Calculator\operate", $first_operand);
}

header('Content-Type: application/json');

print(json_encode([
    'file'          =>  '/var/tmp/befeni/input.txt',
    'result'        =>  get_result_from_file("input.txt"),
    'operations'    =>  \Befeni\Basic\Calculator\parse_file("input.txt", $first_operand),
]));
