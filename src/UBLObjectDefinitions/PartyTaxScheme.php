<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class PartyTaxScheme extends UBLDeserializable
{
    public ?string $registrationName = null;
    public ?string $companyId = null;
    public ?TaxScheme $taxScheme = null;

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
                        $reader->next(); // Move past the current text node.
                        break;
                    case "CompanyID":
                        $instance->companyId = $reader->readString();
                        $reader->next();
                        break;
                    case "TaxScheme":
                        $parsed= $reader->parseCurrentElement();
                        $instance->taxScheme = $parsed["value"];
                        break;
                }
            }
            if (!$reader->read())
            {
                throw new Exception("Unexpected end of XML file while reading PartyTaxScheme.");
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
        return '<cac:PartyTaxScheme '.self::NS_DEFINTIONS.'>
                <cbc:RegistrationName>United States</cbc:RegistrationName>
                <cbc:CompanyID>US</cbc:CompanyID>
                '.TaxScheme::GetTestXML().'
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
        if ($instance->registrationName != "United States")
        {
            $reason = "Failed to parse RegistrationName";
            return false;
        }
        if ($instance->companyId != "US")
        {
            $reason = "Failed to parse CompanyID";
            return false;
        }
        if (!TaxScheme::TestDefaultValues($instance->taxScheme, $reason))
        {
            return false;
        }
        return true;
    }
}