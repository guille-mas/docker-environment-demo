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


    public function testParseOperationFailsWithMoreThan2Words(): void {
        $this->expectException(\Exception::class);
        Befeni\Basic\Calculator\parse_operation("apply 10 extra");
    }


    public function testParseOperationFailsWithInvalidAction(): void
    {
        $this->expectException(\Exception::class);
        Befeni\Basic\Calculator\parse_operation("foo 123");
    }


    public function testParseOperationFailsWithInvalidOperand(): void
    {
        $this->expectException(\Exception::class);
        Befeni\Basic\Calculator\parse_operation("divide foo");
    }


    public function testOperateFailsWithInvalidOperator(): void
    {
        $this->expectException(\Exception::class);
        Befeni\Basic\Calculator\operate(0,['foo',1]);
    }

    public function testOperateOperations(): void
    {
        $this->assertEquals(
            Befeni\Basic\Calculator\operate(0, ['add', 1]),
            1
        );

        $this->assertEquals(
            Befeni\Basic\Calculator\operate(0, ['add', -21]),
            -21
        );

        $this->assertEquals(
            Befeni\Basic\Calculator\operate(9, ['substract', 3]),
            6
        );

        $this->assertEquals(
            Befeni\Basic\Calculator\operate(9, ['substract', 3.5]),
            5.5
        );

        $this->assertEquals(
            Befeni\Basic\Calculator\operate(4, ['multiply', 3]),
            12
        );

        $this->assertEquals(
            Befeni\Basic\Calculator\operate(99, ['divide', 3]),
            33
        );
    }

    public function testParseCorrectFile(): void {
        $this->assertEquals(
            \Befeni\Basic\Calculator\parse_file("test-success.txt", $first_operand),[
                ["add", 77],
                ["substract", 3],
                ["multiply", 2],
                ["divide", 3]
           ]
        );
        $this->assertEquals($first_operand,5);
    }

    public function testParseInvalidFileWithInvalidOperator(): void {
        $this->expectException(\Exception::class);
        \Befeni\Basic\Calculator\parse_file("test-fail-wrong-operator.txt", $first_operand);
    }

    public function testParseInvalidFileWithDuplicatedApply(): void
    {
        $this->expectException(\Exception::class);
        \Befeni\Basic\Calculator\parse_file("test-fail-duplicated-apply.txt", $first_operand);
    }
}

