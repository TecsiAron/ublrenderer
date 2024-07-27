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

use EdituraEDU\UBLRenderer\UBLObjectDefinitions\ParsedUBLInvoice;
use EdituraEDU\UBLRenderer\UBLRenderer;
use PHPUnit\Framework\TestCase;

class ZipTest extends TestCase
{
    public function testZip()
    {
        $this->CreateZip();
        $xml = UBLRenderer::LoadUBLFromZip("test.zip")->ubl;
        $this->assertEquals($xml, ParsedUBLInvoice::GetTestXML());
        @unlink("test.zip");
        $this->assertFileDoesNotExist("test.zip", "Failed to delete test.zip");
    }

    private function CreateZip(): void
    {
        $zip = new ZipArchive();
        if (file_exists("test.zip"))
        {
            @unlink("test.zip");
        }
        $this->assertFileDoesNotExist("test.zip", "Test zip already exists!");
        $zip->open("test.zip", ZipArchive::CREATE);
        $number = rand(100000, 900000);
        $zip->addFromString("$number.xml", ParsedUBLInvoice::GetTestXML());
        $zip->addFromString("semnatura_$number.xml", "test");
        $zip->close();
    }
}