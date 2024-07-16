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
    public ?string $id = null;
    public ?string $name = null;
    public ?string $taxTypeCode = null;
    public ?string $currencyCode = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new TaxScheme();
        $depth = $reader->depth;
        $reader->read();//move one child down
        while ($reader->nodeType != XMLReader::END_ELEMENT || $reader->depth > $depth)
        {
            if ($reader->nodeType == XMLReader::ELEMENT)
            {
                switch ($reader->localName)
                {
                    case "ID":
                        $instance->id = $reader->readString();
                        $reader->next();
                        break;
                    case "Name":
                        $instance->name = $reader->readString();
                        $reader->next();
                        break;
                    case "TaxTypeCode":
                        $instance->taxTypeCode = $reader->readString();
                        $reader->next();
                        break;
                    case "CurrencyCode":
                        $instance->currencyCode = $reader->readString();
                        $reader->next();
                        break;
                }
            }

            if (!$reader->read())
            {
                throw new Exception("Invalid XML format");
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
        if ($instance->id != "US")
        {
            $reason = "Failed to parse ID";
            return false;
        }
        if ($instance->name != "United States")
        {
            $reason = "Failed to parse Name";
            return false;
        }
        if ($instance->taxTypeCode != "VAT")
        {
            $reason = "Failed to parse TaxTypeCode";
            return false;
        }
        if ($instance->currencyCode != "USD")
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
}