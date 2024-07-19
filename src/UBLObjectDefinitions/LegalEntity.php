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

class LegalEntity extends UBLDeserializable
{
    public ?string $RegistrationName = null;
    public ?string $CompanyID = null;
    public ?string $CompanyIDAttribute = null;

    public ?string $CompanyLegalForm = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $parsedLegalEntity = $reader->parseInnerTree();
        if (!is_array($parsedLegalEntity))
        {
            return $instance;
        }
        for ($i = 0; $i < sizeof($parsedLegalEntity); $i++)
        {
            $parsed = $parsedLegalEntity[$i];
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
                    $instance->CompanyID = $parsed["value"];
                    if (isset($parsed["attributes"]["schemeID"]))
                    {
                        $instance->CompanyIDAttribute = $parsed["attributes"]["schemeID"];
                    }
                    break;
                case "CompanyLegalForm":
                    $instance->CompanyLegalForm = $parsed["value"];
                    break;
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "PartyLegalEntity";
    }

    public static function GetTestXML(): string
    {
        return '<cac:PartyLegalEntity ' . self::NS_DEFINTIONS . '>
                    <cbc:RegistrationName>John Doe</cbc:RegistrationName>
                    <cbc:CompanyID schemeID="RO" schemeName="CUI">123456789</cbc:CompanyID>
                    <cbc:CompanyLegalForm>J40/12345/1998</cbc:CompanyLegalForm>
                </cac:PartyLegalEntity>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof LegalEntity))
        {
            $reason = "Instance is not LegalEntity";
            return false;
        }
        if ($instance->RegistrationName !== "John Doe")
        {
            $reason = "RegistrationName is not John Doe";
            return false;
        }
        if ($instance->CompanyID !== "123456789")
        {
            $reason = "CompanyID is not 123456789";
            return false;
        }
        if ($instance->CompanyIDAttribute !== "RO")
        {
            $reason = "CompanyID schemeID is not RO";
            return false;
        }

        if ($instance->CompanyLegalForm !== "J40/12345/1998")
        {
            $reason = "CompanyLegalForm is not J40/12345/1998";
            return false;
        }
        return true;
    }

    public function CanRender(): true|array
    {
        return true;
    }
}