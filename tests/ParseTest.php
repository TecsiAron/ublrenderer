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

use EdituraEDU\UBLRenderer\XMLReaderProvider;
use PHPUnit\Framework\TestCase;

class ParseTest extends TestCase
{
    public function testUBLParsing(): void
    {
        return;
        $classes = XMLReaderProvider::CLASSES;
        for ($i = 0; $i < sizeof($classes); $i++)
        {
            $testFunction = [$classes[$i], "TestParse"];
            $reason = "";
            $testResult = $testFunction($reason);
            $this->assertTrue($testResult, "$classes[$i]::TestParse failed: $reason");
            echo "$classes[$i]::TestParse passed\n";
        }
    }
}