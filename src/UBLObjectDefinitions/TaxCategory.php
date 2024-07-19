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

class TaxCategory extends UBLDeserializable
{
    public ?string $ID = null;
    public ?string $Name = null;
    public ?string $Percent = null;
    public ?TaxScheme $TaxScheme = null;
    public ?string $TaxExemptionReason = null;
    public ?string $TaxExemptionReasonCode = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $xmlTaxCategory = $reader->parseInnerTree();
        if (!is_array($xmlTaxCategory))
        {
            return $instance;
        }
        for ($i = 0; $i < sizeof($xmlTaxCategory); $i++)
        {
            $parsed = $xmlTaxCategory[$i];
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
                case "Percent":
                    $instance->Percent = $parsed["value"];
                    break;
                case "TaxScheme":
                    $instance->TaxScheme = $parsed["value"];
                    break;
                case "TaxExemptionReason":
                    $instance->TaxExemptionReason = $parsed["value"];
                    break;
                case "TaxExemptionReasonCode":
                    $instance->TaxExemptionReasonCode = $parsed["value"];
                    break;
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "TaxCategory";
    }

    public static function GetTestXML(): string
    {
        return '<cac:TaxCategory ' . self::NS_DEFINTIONS . '>
                    <cbc:ID xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">US</cbc:ID>
                    <cbc:Name xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">United States</cbc:Name>
                    <cbc:Percent xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">0.00</cbc:Percent>
                    <cbc:TaxExemptionReason>Exempt</cbc:TaxExemptionReason>
                    <cbc:TaxExemptionReasonCode>EX</cbc:TaxExemptionReasonCode>
                    ' . TaxScheme::GetTestXML() . '
                </cac:TaxCategory>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Failed to parse TaxCategory";
            return false;
        }
        if (!($instance instanceof TaxCategory))
        {
            $reason = "Failed to parse TaxCategory, wrong instance type";
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
        if ($instance->Percent != "0.00")
        {
            $reason = "Failed to parse Percent";
            return false;
        }
        if ($instance->TaxExemptionReason != "Exempt")
        {
            $reason = "Failed to parse TaxExemptionReason";
            return false;
        }
        if ($instance->TaxExemptionReasonCode != "EX")
        {
            $reason = "Failed to parse TaxExemptionReasonCode";
            return false;
        }
        if (!TaxScheme::TestDefaultValues($instance->TaxScheme, $reason))
        {
            return false;
        }
        return true;
    }

    public function CanRender(): true|array
    {
        return true;
    }
}