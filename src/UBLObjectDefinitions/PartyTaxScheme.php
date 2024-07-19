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

class PartyTaxScheme extends UBLDeserializable
{
    public ?string $RegistrationName = null;
    public ?string $CompanyId = null;
    public ?TaxScheme $TaxScheme = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $parsedPartyTaxScheme = $reader->parseInnerTree();
        if (!is_array($parsedPartyTaxScheme))
        {
            return $instance;
        }
        for ($i = 0; $i < sizeof($parsedPartyTaxScheme); $i++)
        {
            $parsed = $parsedPartyTaxScheme[$i];
            if ($parsed["value"] == null)
            {
                continue;
            }
            $localName = $instance->getLocalName($parsed["name"]);
            switch ($localName)
            {
                case "RegistrationName":
                    $instance->RegistrationName = $parsed["value"];
                    break;
                case "CompanyID":
                    $instance->CompanyId = $parsed["value"];
                    break;
                case "TaxScheme":
                    $instance->TaxScheme = $parsed["value"];
                    break;
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "PartyTaxScheme";
    }

    public static function GetTestXML(): string
    {
        return '<cac:PartyTaxScheme ' . self::NS_DEFINTIONS . '>
                <cbc:RegistrationName>United States</cbc:RegistrationName>
                <cbc:CompanyID>US</cbc:CompanyID>
                ' . TaxScheme::GetTestXML() . '
            </cac:PartyTaxScheme>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Failed to parse PartyTaxScheme";
            return false;
        }
        if (!($instance instanceof PartyTaxScheme))
        {
            $reason = "Failed to parse PartyTaxScheme, wrong instance type";
            return false;
        }
        if ($instance->RegistrationName != "United States")
        {
            $reason = "Failed to parse RegistrationName";
            return false;
        }
        if ($instance->CompanyId != "US")
        {
            $reason = "Failed to parse CompanyID";
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