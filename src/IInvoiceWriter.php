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

/**
 * Interface IInvoiceWriter to be used in conjunction with UBLRenderer::WriteFile and UBLRenderer::WriteFiles
 * @see UBLRenderer::WriteFile
 * @see UBLRenderer::WriteFiles
 */
interface IInvoiceWriter
{
    /**
     * Writes the content of the invoice to a file
     * @param string $hmlContent the html content
     * @param ParsedUBLInvoice $invoice a reference to the parsed invoice
     */
    public function WriteContent(string $hmlContent, ParsedUBLInvoice $invoice): void;

    /**
     * Returns the warnings generated during the writing process, empty array if no warnings
     * @return UBLRendererWarning[]
     */
    public function GetWarnings(): array;
}