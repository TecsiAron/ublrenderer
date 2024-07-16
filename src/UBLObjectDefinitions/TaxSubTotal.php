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
    public ?string $TaxAmount = null;
    public ?TaxCategory $TaxCategory = null;
    private string $Percent;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new TaxSubTotal();
        $depth = $reader->depth;
        $reader->read(); // Move one child down
        while ($reader->nodeType != XMLReader::END_ELEMENT || $reader->depth > $depth)
        {
            if ($reader->nodeType == XMLReader::ELEMENT)
            {
                switch ($reader->localName)
                {
                    case "TaxableAmount":
                        $instance->TaxableAmount = $reader->readString();
                        $reader->next(); // Move past the current text node
                        break;
                    case "TaxAmount":
                        $instance->TaxAmount = $reader->readString();
                        $reader->next();
                        break;
                    case "TaxCategory":
                        $parsed = $reader->parseCurrentElement();
                        $instance->TaxCategory = $parsed["value"];
                        break;
                    case "Percent":
                        $instance->Percent = $reader->readString();
                        $reader->next();
                        break;
                }
            }
            if (!$reader->read())
            {
                throw new Exception("Unexpected end of XML file while reading TaxSubTotal.");
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
}