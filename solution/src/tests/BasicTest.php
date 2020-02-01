<?php

use PHPUnit\Framework\TestCase;
use Befeni\Basic\Calculator;

require_once(__DIR__."/../calculator.php");


final class CalculatorTest extends TestCase
{

    public function testOperationParser(): void {
        $this->assertEquals(
            Befeni\Basic\Calculator\parse_operation("add 12.2"),
            ['add',12.2]
        );

        $this->assertEquals(
            Befeni\Basic\Calculator\parse_operation("add -12"),
            ['add', -12]
        );

        $this->assertEquals(
            Befeni\Basic\Calculator\parse_operation("substract 7"),
            ['substract', 7]
        );

        $this->assertEquals(
            Befeni\Basic\Calculator\parse_operation("substract -4.5"),
            ['substract', -4.5]
        );

        $this->assertEquals(
            Befeni\Basic\Calculator\parse_operation("substract -4.5"),
            ['substract', -4.5]
        );

        $this->assertEquals(
            Befeni\Basic\Calculator\parse_operation("multiply 123"),
            ['multiply', 123]
        );

        $this->assertEquals(
            Befeni\Basic\Calculator\parse_operation("divide 1.2"),
            ['divide', 1.2]
        );

        $this->assertEquals(
            Befeni\Basic\Calculator\parse_operation("apply 10"),
            ['apply', 10]
        );
    }


    public function testOperationParserShouldFailWithInvalidParams(): void {
        $this->expectException(\Exception::class);
        Befeni\Basic\Calculator\parse_operation("foo 123");
        Befeni\Basic\Calculator\parse_operation("apply 10 extra");
        Befeni\Basic\Calculator\parse_operation("divide foo");
    }
}

