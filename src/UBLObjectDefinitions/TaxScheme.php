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

class TaxScheme extends UBLDeserializable
{
    public ?string $ID = null;
    public ?string $Name = null;
    public ?string $TaxTypeCode = null;
    public ?string $CurrencyCode = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new TaxScheme();
        $parsedScheme = $reader->parseInnerTree();
        if (!is_array($parsedScheme))
        {
            return $instance;
        }
        for ($i = 0; $i < sizeof($parsedScheme); $i++)
        {
            $parsed = $parsedScheme[$i];
            if ($parsed["value"] == null)
            {
                continue;
            }
            $localName = $instance->getLocalName($parsed["name"]);
            switch ($localName)
            {
                case "ID":
                    $instance->ID = $parsed["value"];
                    break;
                case "Name":
                    $instance->Name = $parsed["value"];
                    break;
                case "TaxTypeCode":
                    $instance->TaxTypeCode = $parsed["value"];
                    break;
                case "CurrencyCode":
                    $instance->CurrencyCode = $parsed["value"];
                    break;
            }
        }
        return $instance;
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Failed to parse TaxScheme";
            return false;
        }
        if (!($instance instanceof TaxScheme))
        {
            $reason = "Failed to parse TaxScheme, wrong instance type";
            return false;
        }
        if ($instance->ID != "US")
        {
            $reason = "Failed to parse ID";
            return false;
        }
        if ($instance->Name != "United States")
        {
            $reason = "Failed to parse Name";
            return false;
        }
        if ($instance->TaxTypeCode != "VAT")
        {
            $reason = "Failed to parse TaxTypeCode";
            return false;
        }
        if ($instance->CurrencyCode != "USD")
        {
            $reason = "Failed to parse CurrencyCode";
            return false;
        }
        return true;
    }


    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "TaxScheme";
    }

    public static function GetTestXML(): string
    {
        return '<cac:TaxScheme ' . self::NS_DEFINTIONS . '>
                    <cbc:ID>US</cbc:ID>
                    <cbc:Name>United States</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                    <cbc:CurrencyCode>USD</cbc:CurrencyCode>
                </cac:TaxScheme>';
    }

    public function CanRender(): true|array
    {
        return true;
    }
}