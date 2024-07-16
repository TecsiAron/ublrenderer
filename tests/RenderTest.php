<?php

use EdituraEDU\UBLRenderer\UBLObjectDefinitions\ParsedUBLInvoice;
use EdituraEDU\UBLRenderer\UBLRenderer;
use PHPUnit\Framework\TestCase;

class RenderTest extends TestCase
{
    public function testRender()
    {
        if(file_exists("test_render.html"))
        {
            @unlink("test_render.html");
        }
        $this->assertFileDoesNotExist("test_render.html");
        $content= ParsedUBLInvoice::GetTestXML();
        $renderer = new UBLRenderer($content);
        try
        {
            $html = $renderer->CreateHTML();
        }
        catch (Exception $e)
        {
            $this->fail($e->getMessage());
        }
        $this->assertNotEmpty($html);
        try
        {
            file_put_contents("test_render.html", $html);
        }
        catch (Exception $e)
        {
            $this->fail($e->getMessage());
        }
        $this->assertFileExists("test_render.html");
    }
}