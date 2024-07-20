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

use EdituraEDU\UBLRenderer\UBLRenderer;
use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class Party extends UBLDeserializable
{
    public ?string $Name = null;
    public ?string $PartyIdentificationId = null;
    /**
     * @deprecated
     */
    public ?string $PartyIdentificationSchemeId = null;
    /**
     * @deprecated
     */
    public ?string $PartyIdentificationSchemeName = null;
    public ?PostalAddress $PostalAddress = null;

    public ?Contact $Contact = null;
    public ?PartyTaxScheme $PartyTaxScheme = null;
    public ?LegalEntity $LegalEntity = null;
    public ?string $EndpointID = null;

    public bool $IsBuyer = false;
    public ?string $ForcedRegistrationNumber = null;
    /**
     * @deprecated
     */
    public ?string $EndpointID_schemeID = null;


    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $parsedParty = $reader->parseInnerTree();
        if(!is_array($parsedParty))
        {
            return $instance;
        }
        for($i=0;$i<count($parsedParty);$i++)
        {
            $node = $parsedParty[$i];
            if($node["value"] == null)
            {
                continue;
            }
            $localName=$instance->getLocalName($node["name"]);
            switch ($localName)
            {
                case "EndpointID":
                    $instance->EndpointID = $node["value"];
                    break;
                case "PartyIdentification":
                    $instance->PartyIdentificationId = $node["value"][0]["value"];
                    break;
                case "PostalAddress":
                    $instance->PostalAddress = $node["value"];
                    break;
                case "Contact":
                    $instance->Contact = $node["value"];
                    break;
                case "PartyTaxScheme":
                    $instance->PartyTaxScheme = $node["value"];
                    break;
                case "PartyLegalEntity":
                    $instance->LegalEntity = $node["value"];
                    break;
                case "PartyName":
                    $instance->Name = $node["value"][0]["value"];
                    break;
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
        if ($instance->Name != "Seller SRL")
        {
            $reason = "Failed to parse name";
            return false;
        }
        if ($instance->EndpointID != "endpointID")
        {
            $reason = "Failed to parse endpointID";
            return false;
        }
        if ($instance->PartyIdentificationId != "partyIdentificationId")
        {
            $reason = "Failed to parse partyIdentificationId";
            return false;
        }
        if (!Address::TestDefaultValues($instance->PostalAddress, $reason))
        {
            return false;
        }
        if (!Contact::TestDefaultValues($instance->Contact, $reason))
        {
            return false;
        }
        if (!PartyTaxScheme::TestDefaultValues($instance->PartyTaxScheme, $reason))
        {
            return false;
        }
        if (!LegalEntity::TestDefaultValues($instance->LegalEntity, $reason))
        {
            return false;
        }
        return true;
    }

    public function GetCIF():?string
    {
        if(isset($this->PartyIdentificationId) && $this->IsValidCIF($this->PartyIdentificationId))
        {
            return $this->PartyIdentificationId;
        }
        if(isset($this->PartyTaxScheme->CompanyId) && $this->IsValidCIF($this->PartyTaxScheme->CompanyId))
        {
            return $this->PartyTaxScheme->CompanyId;
        }
        if(isset($this->LegalEntity->CompanyID) && $this->IsValidCIF($this->LegalEntity->CompanyID))
        {
            return $this->LegalEntity->CompanyID;
        }
        return null;
    }

    public function GetRegistrationNumber():?string
    {
        if(isset($this->LegalEntity->CompanyID) && $this->IsValidRegNumber($this->LegalEntity->CompanyID))
        {
            return $this->LegalEntity->CompanyID;
        }
        if(isset($this->LegalEntity->CompanyLegalForm) && $this->IsValidRegNumber($this->LegalEntity->CompanyLegalForm))
        {
            return $this->LegalEntity->CompanyLegalForm;
        }
        if(!empty($this->ForcedRegistrationNumber) && $this->IsValidRegNumber($this->ForcedRegistrationNumber))
        {
            return $this->ForcedRegistrationNumber;
        }
        return null;
    }

    public function HasRegistrationNumber():bool
    {
        return $this->GetRegistrationNumber()!=null;
    }

    public function CanRender(): true|array
    {
        $toCheck=[$this->GetCIF(), $this->Name];
        $addressValidation=$this->PostalAddress->CanRender();
        if($addressValidation===true)
        {
            if (!$this->ContainsNull($toCheck))
            {
                return true;
            }
        }
        $results=[];
        if(is_array($addressValidation))
        {
            $results=array_merge($results, $addressValidation);
        }
        if($this->GetCIF()==null)
        {
            $results[]="[Party] CIF is missing";
        }
        return $results;
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

    public function IsValidCIF(?string $input):bool
    {
        if($input===null)
        {
            return false;
        }
        $cif=trim(strtolower($input));
        if (!is_int($cif))
        {
            $cif = strtoupper($cif);
            if (str_starts_with($cif, 'RO'))
            {
                $cif = substr($cif, 2);
            }
            $cif = (int)trim($cif);
        }

        if (strlen($cif) > 10 || strlen($cif) < 2)
        {
            return false;
        }
        $v = 753217532;
        $c1 = $cif % 10;
        $cif = (int)($cif / 10);
        $t = 0;
        while ($cif > 0)
        {
            $t += ($cif % 10) * ($v % 10);
            $cif = (int)($cif / 10);
            $v = (int)($v / 10);
        }
        $c2 = $t * 10 % 11;
        if ($c2 == 10)
        {
            $c2 = 0;
        }
        return $c1 === $c2;
    }
}