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

class ClassifiedTaxCategory extends TaxCategory
{
    public ?string $SchemeID = null;
    public ?string $SchemeName = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != XMLReader::END_ELEMENT || $reader->depth > $depth)
        {
            if ($reader->nodeType == XMLReader::ELEMENT)
            {
                switch ($reader->localName)
                {
                    case "ID":
                        $instance->ID = $reader->readString();
                        $reader->next(); // Move past the current text node
                        break;
                    case "Name":
                        $instance->Name = $reader->readString();
                        $reader->next();
                        break;
                    case "Percent":
                        $instance->Percent = $reader->readString();
                        $reader->next();
                        break;
                    case "TaxScheme":
                        $parsed = $reader->parseCurrentElement();
                        $instance->TaxScheme = $parsed["value"];
                        break;
                    case "TaxExemptionReason":
                        $instance->TaxExemptionReason = $reader->readString();
                        $reader->next();
                        break;
                    case "TaxExemptionReasonCode":
                        $instance->TaxExemptionReasonCode = $reader->readString();
                        $reader->next();
                        break;
                    case "SchemeID":
                        $instance->SchemeID = $reader->readString();
                        $reader->next();
                        break;
                    case "SchemeName":
                        $instance->SchemeName = $reader->readString();
                        $reader->next();
                        break;
                }
            }
            if (!$reader->read())
            {
                throw new Exception("Unexpected end of XML file while reading TaxCategory.");
            }
        }

        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "ClassifiedTaxCategory";
    }

    public static function GetTestXML(): string
    {
        return '<cac:ClassifiedTaxCategory ' . self::NS_DEFINTIONS . '>
                    <cbc:ID>1</cbc:ID>
                    <cbc:Name>VAT</cbc:Name>
                    <cbc:Percent>19</cbc:Percent>
                    ' . TaxScheme::GetTestXML() . '
                    <cbc:TaxExemptionReason>Reason</cbc:TaxExemptionReason>
                    <cbc:TaxExemptionReasonCode>Code</cbc:TaxExemptionReasonCode>
                    <cbc:SchemeID>1</cbc:SchemeID>
                    <cbc:SchemeName>VAT</cbc:SchemeName>
                </cac:ClassifiedTaxCategory>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Failed to parse ClassifiedTaxCategory";
            return false;
        }
        if (!($instance instanceof ClassifiedTaxCategory))
        {
            $reason = "Failed to parse ClassifiedTaxCategory, wrong instance type";
            return false;
        }
        if ($instance->ID !== "1")
        {
            $reason = "Failed to parse ID";
            return false;
        }
        if ($instance->Name != "VAT")
        {
            $reason = "Failed to parse Name";
            return false;
        }
        if ($instance->Percent !== "19")
        {
            $reason = "Failed to parse Percent";
            return false;
        }
        if ($instance->TaxExemptionReason != "Reason")
        {
            $reason = "Failed to parse TaxExemptionReason";
            return false;
        }
        if ($instance->TaxExemptionReasonCode != "Code")
        {
            $reason = "Failed to parse TaxExemptionReasonCode";
            return false;
        }
        if ($instance->SchemeID !== "1")
        {
            $reason = "Failed to parse SchemeID";
            return false;
        }
        if ($instance->SchemeName !== "VAT")
        {
            $reason = "Failed to parse SchemeName";
            return false;
        }
        if (!TaxScheme::TestDefaultValues($instance->TaxScheme, $reason))
        {
            return false;
        }
        return true;
    }


}