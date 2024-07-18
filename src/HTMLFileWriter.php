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

namespace EdituraEDU\UBLRenderer;



use EdituraEDU\UBLRenderer\InvoiceWriter;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\ParsedUBLInvoice;

class HTMLFileWriter extends InvoiceWriter
{

    private ?string $Path;

    /**
     * If path is null or is a directory InvoiceWriter::NormalizePath will be used to generate the path
     * @see InvoiceWriter::NormalizePath
     * @param string|null $path if null dirname(__FILE__)."/../output/<invoice_id>.html will be used"
     */
    public function __construct(?string $path=null)
    {
        $this->Path = $path;
    }
    public function WriteContent(string $hmlContent, ParsedUBLInvoice $invoice): void
    {
        $this->Path=$this->NormalizePath($this->Path, $invoice, 'html');
        file_put_contents($this->Path, $hmlContent);
    }

    /**
     * Might be different from path specified in the constructor, check the constructor and InvoiceWriter:: NormalizePath docs for more details
     * @return string
     */
    public function GetPath(): string
    {
        return $this->Path;
    }
}