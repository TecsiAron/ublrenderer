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

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class TaxSubTotal extends UBLDeserializable
{
    public ?string $TaxableAmount = null;
    public ?string $TaxableCurrency = null;
    public ?string $TaxAmount = null;
    public ?string $TaxAmountCurrency = null;
    public ?TaxCategory $TaxCategory = null;
    private string $Percent;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new TaxSubTotal();
        $paredSubTotal = $reader->parseInnerTree();
        if (!is_array($paredSubTotal))
        {
            return $instance;
        }
        for ($i = 0; $i < sizeof($paredSubTotal); $i++)
        {
            $parsed = $paredSubTotal[$i];
            if ($parsed["value"] == null)
            {
                continue;
            }
            $localName = $instance->getLocalName($parsed["name"]);
            switch ($localName)
            {
                case "TaxableAmount":
                    $instance->TaxableAmount = $parsed["value"];
                    if (isset($parsed["attributes"]["currencyID"]))
                    {
                        $instance->TaxableCurrency = $parsed["attributes"]["currencyID"];
                    }
                    break;
                case "TaxAmount":
                    $instance->TaxAmount = $parsed["value"];
                    if (isset($parsed["attributes"]["currencyID"]))
                    {
                        $instance->TaxAmountCurrency = $parsed["attributes"]["currencyID"];
                    }
                    break;
                case "TaxCategory":
                    $instance->TaxCategory = $parsed["value"];
                    break;
                case "Percent":
                    $instance->Percent = $parsed["value"];
                    break;
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "TaxSubtotal";
    }

    public static function GetTestXML(): string
    {
        return '<cac:TaxSubtotal ' . self::NS_DEFINTIONS . '>
                    <cbc:TaxableAmount currencyID="USD">5.00</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="USD">6.00</cbc:TaxAmount>
                    ' . TaxCategory::GetTestXML() . '
                    <cbc:Percent>0.00</cbc:Percent>
                </cac:TaxSubtotal>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Failed to parse TaxSubTotal";
            return false;
        }
        if (!($instance instanceof TaxSubTotal))
        {
            $reason = "Failed to parse TaxSubTotal, wrong instance type";
            return false;
        }
        if ($instance->TaxableAmount !== "5.00")
        {
            $reason = "Failed to parse TaxSubTotal, taxableAmount is not 5.00";
            return false;
        }
        if ($instance->TaxAmount !== "6.00")
        {
            $reason = "Failed to parse TaxSubTotal, taxAmount is not 6.00";
            return false;
        }
        if (!TaxCategory::TestDefaultValues($instance->TaxCategory, $reason))
        {
            return false;
        }
        if ($instance->Percent != "0.00")
        {
            $reason = "Failed to parse TaxSubTotal, percent is not 0.00";
            return false;
        }
        return true;
    }

    public function CanRender(): true|array
    {
        return true;
    }
}