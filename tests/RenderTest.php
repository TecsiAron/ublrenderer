<?php

use EdituraEDU\UBLRenderer\UBLObjectDefinitions\ParsedUBLInvoice;
use EdituraEDU\UBLRenderer\UBLRenderer;
use PHPUnit\Framework\TestCase;

class RenderTest extends TestCase
{
    public function testRender()
    {
        $content= ParsedUBLInvoice::GetTestXML();
        $renderer = new UBLRenderer($content);
    }
}