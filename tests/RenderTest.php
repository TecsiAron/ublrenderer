<?php
/*
 *  Copyright [2024] [Tecsi Aron]
 *
 *     Licensed under the Apache License, Version 2.0 (the "License");
 *     you may not use this file except in compliance with the License.
 *     You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *     Unless required by applicable law or agreed to in writing, software
 *     distributed under the License is distributed on an "AS IS" BASIS,
 *     WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *     See the License for the specific language governing permissions and
 *     limitations under the License.
 */

use EdituraEDU\UBLRenderer\HTMLFileWriter;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\ParsedUBLInvoice;
use EdituraEDU\UBLRenderer\UBLRenderer;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
#[CoversMethod(UBLRenderer::class, "WriteFiles")]
class RenderTest extends TestCase
{
    public function testRender()
    {
        if(file_exists("test_render.html"))
        {
            @unlink("test_render.html");
        }
        $this->assertFileDoesNotExist("test_render.html");
        /*$readResult = UBLRenderer::LoadUBLFromZip(dirname(__FILE__)."/../output/RMBU-1501508.zip");
        $content = $readResult->ubl;*/
        //$content=file_get_contents(dirname(__FILE__)."/../output/4352708358.xml");
        $content= ParsedUBLInvoice::GetTestXML();
        $renderer = new UBLRenderer($content);
        $invoice=$renderer->ParseUBL();
        $validation=$invoice->CanRender();
        $validationFailReason="Validation failed:\n";
        if(is_array($validation))
        {
            $validationFailReason.=implode("\n", $validation);
            $this->fail($validationFailReason);
        }
        $this->assertTrue($validation, $validationFailReason);
        try
        {
            $renderer->WriteFiles([new HTMLFileWriter("test_render.html")]);
        }
        catch (Exception $e)
        {
            $this->fail($e->getMessage()."\n".$e->getTraceAsString());
        }
        $this->assertFileExists("test_render.html");
    }

    public function testNoRender()
    {
        $xml = file_get_contents(dirname(__FILE__)."/efactura_sample_invoice_missing_party.xml");
        $renderer = new UBLRenderer($xml);
        $invoice=$renderer->ParseUBL();
        $this->expectException(\EdituraEDU\UBLRenderer\UBLRenderException::class);
        $this->expectExceptionMessage('Invoice cannot be rendered');
        $renderer->CreateHTML($invoice);
    }
}