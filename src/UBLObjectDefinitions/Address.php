<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use EdituraEDU\UBLRenderer\CountyMap;
use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class Address extends UBLDeserializable
{
    public ?string $streetName = null;
    public ?string $additionalStreetName = null;
    public ?string $buildingNumber = null;
    public ?string $cityName = null;
    public ?string $postalZone = null;
    public ?string $countrySubentity = null;
    public ?Country $country = null;

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
                        $instance->streetName = $reader->readString();
                        $reader->next();
                        break;
                    case "AdditionalStreetName":
                        $instance->additionalStreetName = $reader->readString();
                        $reader->next();
                        break;
                    case "BuildingNumber":
                        $instance->buildingNumber = $reader->readString();
                        $reader->next();
                        break;
                    case "CityName":
                        $instance->cityName = $reader->readString();
                        $reader->next();
                        break;
                    case "PostalZone":
                        $instance->postalZone = $reader->readString();
                        $reader->next();
                        break;
                    case "CountrySubentity":
                        $instance->countrySubentity = $reader->readString();
                        $reader->next();
                        break;
                    case "Country":
                        $parsed = $reader->parseCurrentElement();
                        $instance->country = $parsed["value"];
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
        if ($instance->streetName !== "Strada")
        {
            $reason = "StreetName is not Strada";
            return false;
        }
        if ($instance->additionalStreetName !== "Strada2")
        {
            $reason = "AdditionalStreetName is not Strada2";
            return false;
        }
        if ($instance->buildingNumber !== "1")
        {
            $reason = "BuildingNumber is not 1";
            return false;
        }
        if ($instance->cityName !== "Oras")
        {
            $reason = "CityName is not Oras";
            return false;
        }
        if ($instance->postalZone !== "123456")
        {
            $reason = "PostalZone is not 123456";
            return false;
        }
        if ($instance->countrySubentity !== "Judet")
        {
            $reason = "CountrySubentity is not Judet";
            return false;
        }
        if (!Country::TestDefaultValues($instance->country, $reason))
        {
            return false;
        }
        return true;
    }

    public function getCountyName():?string
    {
        if(!isset($this->countrySubentity))
        {
            return null;
        }
        return CountyMap::GetCounty($this->countrySubentity);
    }
}