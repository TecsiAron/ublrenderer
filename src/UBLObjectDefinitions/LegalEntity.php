<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class LegalEntity extends UBLDeserializable
{
    public ?string $registrationName = null;
    public ?string $companyId = null;
    public ?string $companyIdAttributes = null;

    public ?string $companyLegalForm = null;

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
                    case "RegistrationName":
                        $instance->registrationName = $reader->readString();
                        $reader->next();
                        break;
                    case "CompanyID":
                        $instance->companyId = $reader->readString();
                        $instance->companyIdAttributes = $reader->getAttribute("schemeID");
                        $reader->next();
                        break;
                    case "CompanyLegalForm":
                        $instance->companyLegalForm = $reader->readString();
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
        if ($instance->registrationName !== "John Doe")
        {
            $reason = "RegistrationName is not John Doe";
            return false;
        }
        if ($instance->companyId !== "123456789")
        {
            $reason = "CompanyID is not 123456789";
            return false;
        }
        if ($instance->companyIdAttributes !== "RO")
        {
            $reason = "CompanyID schemeID is not RO";
            return false;
        }

        if ($instance->companyLegalForm !== "J40/12345/1998")
        {
            $reason = "CompanyLegalForm is not J40/12345/1998";
            return false;
        }
        return true;
    }
}