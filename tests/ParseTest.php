<?php


use EdituraEDU\UBLRenderer\XMLReaderProvider;
use PHPUnit\Framework\TestCase;

class ParseTest extends TestCase
{
    public function testUBLParsing():void
    {
        $classes= XMLReaderProvider::CLASSES;
        for($i=0; $i<sizeof($classes); $i++)
        {
            $testFunction=[$classes[$i], "TestParse"];
            $reason="";
            $testResult= $testFunction($reason);
            $this->assertTrue($testResult, "$classes[$i]::TestParse failed: $reason");
            echo "$classes[$i]::TestParse passed\n";
        }
    }
}