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

use EdituraEDU\UBLRenderer\CountyMap;
use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class Address extends UBLDeserializable
{
    public ?string $StreetName = null;
    public ?string $AdditionalStreetName = null;
    public ?string $BuildingNumber = null;
    public ?string $CityName = null;
    public ?string $PostalZone = null;
    public ?string $CountrySubentity = null;
    public ?Country $Country = null;

    public static function XMLDeserialize(Reader $reader, ?Address $instance = null): self
    {
        if ($instance == null)
        {
            $instance = new self();
        }
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != XMLReader::END_ELEMENT || $reader->depth > $depth)
        {
            if ($reader->nodeType == XMLReader::ELEMENT)
            {
                switch ($reader->localName)
                {
                    case "StreetName":
                        $instance->StreetName = $reader->readString();
                        $reader->next();
                        break;
                    case "AdditionalStreetName":
                        $instance->AdditionalStreetName = $reader->readString();
                        $reader->next();
                        break;
                    case "BuildingNumber":
                        $instance->BuildingNumber = $reader->readString();
                        $reader->next();
                        break;
                    case "CityName":
                        $instance->CityName = $reader->readString();
                        $reader->next();
                        break;
                    case "PostalZone":
                        $instance->PostalZone = $reader->readString();
                        $reader->next();
                        break;
                    case "CountrySubentity":
                        $instance->CountrySubentity = $reader->readString();
                        $reader->next();
                        break;
                    case "Country":
                        $parsed = $reader->parseCurrentElement();
                        $instance->Country = $parsed["value"];
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

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "Address";
    }

    public static function GetTestXML(): string
    {
        return '<cac:Address ' . self::NS_DEFINTIONS . '>
                    <cbc:StreetName>Strada</cbc:StreetName>
                    <cbc:AdditionalStreetName>Strada2</cbc:AdditionalStreetName>
                    <cbc:BuildingNumber>1</cbc:BuildingNumber>
                    <cbc:CityName>Oras</cbc:CityName>
                    <cbc:PostalZone>123456</cbc:PostalZone>
                    <cbc:CountrySubentity>Judet</cbc:CountrySubentity>
                    <cac:Country>
                        <cbc:IdentificationCode>RO</cbc:IdentificationCode>
                    </cac:Country>
                </cac:Address>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof Address))
        {
            $reason = "Instance is not Address";
            return false;
        }
        if ($instance->StreetName !== "Strada")
        {
            $reason = "StreetName is not Strada";
            return false;
        }
        if ($instance->AdditionalStreetName !== "Strada2")
        {
            $reason = "AdditionalStreetName is not Strada2";
            return false;
        }
        if ($instance->BuildingNumber !== "1")
        {
            $reason = "BuildingNumber is not 1";
            return false;
        }
        if ($instance->CityName !== "Oras")
        {
            $reason = "CityName is not Oras";
            return false;
        }
        if ($instance->PostalZone !== "123456")
        {
            $reason = "PostalZone is not 123456";
            return false;
        }
        if ($instance->CountrySubentity !== "Judet")
        {
            $reason = "CountrySubentity is not Judet";
            return false;
        }
        if (!Country::TestDefaultValues($instance->Country, $reason))
        {
            return false;
        }
        return true;
    }

    public function CanRender(): true|array
    {
        $toCheck=[
            $this->StreetName,
            $this->CityName,
            $this->GetCountyName()
        ];
        if(!$this->ContainsNull($toCheck))
        {
            return true;
        }
        $results=[];
        if($this->StreetName==null)
        {
            $results[]="[Address] StreetName is null";
        }
        if($this->CityName==null)
        {
            $results[]="[Address] CityName is null";
        }
        if($this->CountrySubentity==null)
        {
            $results[]="[Address] CountrySubentity is null";
        }
        return $results;
    }

    public function HasZipCode():bool
    {
        if(!isset($this->PostalZone) || empty($this->PostalZone))
        {
            return false;
        }
        return true;
    }

    public function GetCountyName():?string
    {
        if(trim(strtolower($this->Country->IdentificationCode))=="ro")
        {
            if (!isset($this->CountrySubentity))
            {
                return null;
            }
            return CountyMap::GetCounty($this->CountrySubentity);
        }
        return $this->CountrySubentity??"";
    }
}