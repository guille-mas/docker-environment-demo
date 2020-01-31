<?php 
namespace Befeni\Basic\Controller;

require_once(__DIR__ . "/../app.php");

/**
 * Given a valid filename, return the result of 
 * every operation applied to the first operand
 */
function get_result_from_file(string $filename): float {
    $operations = \Befeni\Basic\Calculator\parse_file($filename, $first_operand);
    $result = array_reduce($operations, "\Befeni\Basic\Calculator\operate", $first_operand);
    return $result;
}


print(json_encode(get_result_from_file("input.txt")));
