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



use EdituraEDU\UBLRenderer\UBLObjectDefinitions\ParsedUBLInvoice;

class HTMLFileWriter implements IInvoiceWriter
{

    private string $Path;

    /**
     * If path does not have a file name, it will be generated based on the invoice id
     * @param string|null $path if null dirname(__FILE__)."/../output/<invoice_id>.html will be used"
     */
    public function __construct(?string $path=null)
    {
        if($path==null)
        {
            $path = dirname(__FILE__)."/../output/";
        }
        $this->Path = $path;
    }
    public function WriteContent(string $hmlContent, ParsedUBLInvoice $invoice): void
    {
        $path=$this->Path;
        if(is_dir($this->Path))
        {
            if(!str_ends_with($path, PATH_SEPARATOR))
            {
                $path.=PATH_SEPARATOR;
            }
            $path.=$invoice->ID.".html";
            $this->Path = $path;
        }
        file_put_contents($this->Path, $hmlContent);
    }

    /**
     * Might be different from path specified in the constructor, check the constructor docs for more details
     * @return string
     */
    public function GetPath(): string
    {
        return $this->Path;
    }
}