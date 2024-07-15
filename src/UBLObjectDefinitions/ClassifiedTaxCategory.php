<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use Exception;
use Sabre\Xml\Reader;

class ClassifiedTaxCategory extends  TaxCategory
{
    public ?string $schemeID = null;
    public ?string $schemeName = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != \XMLReader::END_ELEMENT || $reader->depth > $depth) {
            if ($reader->nodeType == \XMLReader::ELEMENT) {
                switch ($reader->localName) {
                    case "ID":
                        $instance->id = $reader->readString();
                        $reader->next(); // Move past the current text node
                        break;
                    case "Name":
                        $instance->name = $reader->readString();
                        $reader->next();
                        break;
                    case "Percent":
                        $instance->percent = (float)$reader->readString();
                        $reader->next();
                        break;
                    case "TaxScheme":
                        $parsed = $reader->parseCurrentElement();
                        $instance->taxScheme = $parsed["value"];
                        break;
                    case "TaxExemptionReason":
                        $instance->taxExemptionReason = $reader->readString();
                        $reader->next();
                        break;
                    case "TaxExemptionReasonCode":
                        $instance->taxExemptionReasonCode = $reader->readString();
                        $reader->next();
                        break;
                    case "SchemeID":
                        $instance->schemeID = $reader->readString();
                        $reader->next();
                        break;
                    case "SchemeName":
                        $instance->schemeName = $reader->readString();
                        $reader->next();
                        break;
                }
            }
            if (!$reader->read()) {
                throw new Exception("Unexpected end of XML file while reading TaxCategory.");
            }
        }

        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA."ClassifiedTaxCategory";
    }

    public static function GetTestXML(): string
    {
        return '<cac:ClassifiedTaxCategory '.self::NS_DEFINTIONS.'>
                    <cbc:ID>1</cbc:ID>
                    <cbc:Name>VAT</cbc:Name>
                    <cbc:Percent>19</cbc:Percent>
                    '.TaxScheme::GetTestXML().'
                    <cbc:TaxExemptionReason>Reason</cbc:TaxExemptionReason>
                    <cbc:TaxExemptionReasonCode>Code</cbc:TaxExemptionReasonCode>
                    <cbc:SchemeID>1</cbc:SchemeID>
                    <cbc:SchemeName>VAT</cbc:SchemeName>
                </cac:ClassifiedTaxCategory>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if($instance==null)
        {
            $reason="Failed to parse ClassifiedTaxCategory";
            return false;
        }
        if(!($instance instanceof ClassifiedTaxCategory))
        {
            $reason="Failed to parse ClassifiedTaxCategory, wrong instance type";
            return false;
        }
        if($instance->id != "1")
        {
            $reason="Failed to parse ID";
            return false;
        }
        if($instance->name != "VAT")
        {
            $reason="Failed to parse Name";
            return false;
        }
        if($instance->percent != 19)
        {
            $reason="Failed to parse Percent";
            return false;
        }
        if($instance->taxExemptionReason != "Reason")
        {
            $reason="Failed to parse TaxExemptionReason";
            return false;
        }
        if($instance->taxExemptionReasonCode != "Code")
        {
            $reason="Failed to parse TaxExemptionReasonCode";
            return false;
        }
        if($instance->schemeID != "1")
        {
            $reason="Failed to parse SchemeID";
            return false;
        }
        if($instance->schemeName != "VAT")
        {
            $reason="Failed to parse SchemeName";
            return false;
        }
        if(!TaxScheme::TestDefaultValues($instance->taxScheme, $reason))
        {
            return false;
        }
        return true;
    }


}