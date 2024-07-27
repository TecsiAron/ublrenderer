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
 * Utility class to add NormalizePath method to IInvoiceWriter implementations
 * Preferred to NOT use this abstract class as base for all writers, since not all (potential) writers need the NormalizePath method
 * @see UBLRenderer::WriteFile
 * @see UBLRenderer::WriteFiles
 */
abstract class  InvoiceWriter implements IInvoiceWriter
{
    /**
     * If $path is null, the path will be  dirname(__FILE__)."/../output/<invoice_id>"
     * @param string|null $path
     * @param ParsedUBLInvoice $invoice
     * @param string $extension Without the dot
     * @return string
     */
    protected function NormalizePath(?string $path, ParsedUBLInvoice $invoice, string $extension): string
    {
        if ($path == null)
        {
            $path = dirname(__FILE__) . "/../output/";
        }
        if (is_dir($path))
        {
            if (!str_ends_with($path, PATH_SEPARATOR))
            {
                $path .= PATH_SEPARATOR;
            }
            $path .= $invoice->ID . "." . $extension;
        }
        return $path;
    }
}