<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use Exception;

class TaxCategory extends UBLDeserializable
{
    public ?string $id = null;
    public ?string $name = null;
    public ?string $percent = null;
    public ?TaxScheme $taxScheme = null;
    public ?string $taxExemptionReason = null;
    public ?string $taxExemptionReasonCode = null;

    public static function XMLDeserialize(\Sabre\Xml\Reader $reader): self
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
                        $instance->percent = $reader->readString();
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
        return self::CAC_SCHEMA."TaxCategory";
    }

    public static function GetTestXML(): string
    {
        return '<cac:TaxCategory '.self::NS_DEFINTIONS.'>
                    <cbc:ID xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">US</cbc:ID>
                    <cbc:Name xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">United States</cbc:Name>
                    <cbc:Percent xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">0.00</cbc:Percent>
                    <cbc:TaxExemptionReason>Exempt</cbc:TaxExemptionReason>
                    <cbc:TaxExemptionReasonCode>EX</cbc:TaxExemptionReasonCode>
                    '.TaxScheme::GetTestXML().'
                </cac:TaxCategory>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if($instance==null)
        {
            $reason="Failed to parse TaxCategory";
            return false;
        }
        if(!($instance instanceof TaxCategory))
        {
            $reason="Failed to parse TaxCategory, wrong instance type";
            return false;
        }
        if($instance->id != "US")
        {
            $reason="Failed to parse ID";
            return false;
        }
        if($instance->name != "United States")
        {
            $reason="Failed to parse Name";
            return false;
        }
        if($instance->percent != "0.00")
        {
            $reason="Failed to parse Percent";
            return false;
        }
        if($instance->taxExemptionReason != "Exempt")
        {
            $reason="Failed to parse TaxExemptionReason";
            return false;
        }
        if($instance->taxExemptionReasonCode != "EX")
        {
            $reason="Failed to parse TaxExemptionReasonCode";
            return false;
        }
        if(!TaxScheme::TestDefaultValues($instance->taxScheme, $reason))
        {
            return false;
        }
        return true;
    }
}