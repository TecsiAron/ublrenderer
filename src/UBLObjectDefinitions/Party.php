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

class Party extends UBLDeserializable
{
    public ?string $name = null;
    public ?string $partyIdentificationId = null;
    public ?string $partyIdentificationSchemeId = null;
    public ?string $partyIdentificationSchemeName = null;
    public ?PostalAddress $postalAddress = null;

    public ?Contact $contact = null;
    public ?PartyTaxScheme $partyTaxScheme = null;
    public ?LegalEntity $legalEntity = null;
    public ?string $endpointID = null;
    public ?string $endpointID_schemeID = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        //todo check for entrypointID_schemeID?
        while ($reader->nodeType != XMLReader::END_ELEMENT || $reader->depth > $depth)
        {
            if ($reader->nodeType == XMLReader::ELEMENT)
            {
                switch ($reader->localName)
                {

                    case "EndpointID":
                        $instance->endpointID = $reader->readString();
                        $reader->next(); // Move past the current text node
                        break;
                    case "PartyIdentification":
                        $parsed = $reader->parseCurrentElement();
                        $instance->partyIdentificationId = $parsed["value"][0]["value"];
                        break;
                    case "PostalAddress":
                        $parsed = $reader->parseCurrentElement();
                        $instance->postalAddress = $parsed["value"];
                        break;
                    case "Contact":
                        $parsed = $reader->parseCurrentElement();
                        $instance->contact = $parsed["value"];
                        break;
                    case "PartyTaxScheme":
                        $parsed = $reader->parseCurrentElement();
                        $instance->partyTaxScheme = $parsed["value"];
                        break;
                    case "PartyLegalEntity":
                        $parsed = $reader->parseCurrentElement();
                        $instance->legalEntity = $parsed["value"];
                        break;
                    case "PartyName":
                        $parsed = $reader->parseCurrentElement();
                        $instance->name = $parsed["value"][0]["value"];
                        break;
                }
            }
            if (!$reader->read())
            {
                throw new Exception("Unexpected end of XML file while reading Party.");
            }
        }

        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "Party";
    }

    public static function GetTestXML(): string
    {
        return '<cac:Party ' . self::NS_DEFINTIONS . '>
                    <cbc:EndpointID schemeID="schemeID">endpointID</cbc:EndpointID>
                    <cac:PartyName>
                        <cbc:Name>Seller SRL</cbc:Name>
                    </cac:PartyName>
                    <cac:PartyIdentification>
                        <cbc:ID schemeID="schemeID">partyIdentificationId</cbc:ID>
                    </cac:PartyIdentification>
                    ' . PostalAddress::GetTestXML() . Contact::GetTestXML() . PartyTaxScheme::GetTestXML() . LegalEntity::GetTestXML() . '
                </cac:Party>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof Party))
        {
            $reason = "Instance is not Party";
            return false;
        }
        if ($instance->name != "Seller SRL")
        {
            $reason = "Failed to parse name";
            return false;
        }
        if ($instance->endpointID != "endpointID")
        {
            $reason = "Failed to parse endpointID";
            return false;
        }
        if ($instance->partyIdentificationId != "partyIdentificationId")
        {
            $reason = "Failed to parse partyIdentificationId";
            return false;
        }
        if (!Address::TestDefaultValues($instance->postalAddress, $reason))
        {
            return false;
        }
        if (!Contact::TestDefaultValues($instance->contact, $reason))
        {
            return false;
        }
        if (!PartyTaxScheme::TestDefaultValues($instance->partyTaxScheme, $reason))
        {
            return false;
        }
        if (!LegalEntity::TestDefaultValues($instance->legalEntity, $reason))
        {
            return false;
        }
        return true;
    }

    public function getCIF():?string
    {
        if(isset($this->partyIdentificationId))
        {
            return $this->partyIdentificationId;
        }
        if(isset($this->partyTaxScheme->companyId))
        {
            return $this->partyTaxScheme->companyId;
        }
        return null;
    }

    public function getRegistrationNumber():?string
    {
        if(isset($this->legalEntity->companyId) && $this->IsValidRegNumber($this->legalEntity->companyId))
        {
            return $this->legalEntity->companyId;
        }
        if(isset($this->legalEntity->companyLegalForm) && $this->IsValidRegNumber($this->legalEntity->companyLegalForm))
        {
            return $this->legalEntity->companyLegalForm;
        }
        return null;
    }

    private function IsValidRegNumber(?string $regNumber): bool
    {
        if($regNumber == null)
        {
            return false;
        }
        /** @noinspection RegExpDuplicateCharacterInClass */
        /** @noinspection RegExpRedundantEscape */
        $regex="/[J|C|F][0-9][0-9][\/ \\ \-\s][0-9]*[\/ \\ \-][0-9]*/i";
        return preg_match($regex, $regNumber) === 1;
    }
}