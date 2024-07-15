<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use Exception;

class TaxSubTotal extends UBLDeserializable
{
    public ?float $taxableAmount = null;
    public ?float $taxAmount = null;
    public ?TaxCategory $taxCategory = null;
    private float $percent;

    public static function XMLDeserialize(\Sabre\Xml\Reader $reader): self
    {
        $instance = new TaxSubTotal();
        $depth = $reader->depth;
        $reader->read(); // Move one child down
        while ($reader->nodeType != \XMLReader::END_ELEMENT || $reader->depth > $depth) {
            if ($reader->nodeType == \XMLReader::ELEMENT) {
                switch ($reader->localName) {
                    case "TaxableAmount":
                        $instance->taxableAmount = (float)$reader->readString();
                        $reader->next(); // Move past the current text node
                        break;
                    case "TaxAmount":
                        $instance->taxAmount = (float)$reader->readString();
                        $reader->next();
                        break;
                    case "TaxCategory":
                        $parsed = $reader->parseCurrentElement();
                        $instance->taxCategory = $parsed["value"];
                        break;
                    case "Percent":
                        $instance->percent = (float)$reader->readString();
                        $reader->next();
                        break;
                }
            }
            if (!$reader->read()) {
                throw new Exception("Unexpected end of XML file while reading TaxSubTotal.");
            }
        }

        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA."TaxSubtotal";
    }

    public static function GetTestXML(): string
    {
        return '<cac:TaxSubtotal '.self::NS_DEFINTIONS.'>
                    <cbc:TaxableAmount currencyID="USD">5.00</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="USD">6.00</cbc:TaxAmount>
                    '.TaxCategory::GetTestXML().'
                    <cbc:Percent>0.00</cbc:Percent>
                </cac:TaxSubtotal>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null) {
            $reason = "Failed to parse TaxSubTotal";
            return false;
        }
        if (!($instance instanceof TaxSubTotal)) {
            $reason = "Failed to parse TaxSubTotal, wrong instance type";
            return false;
        }
        if ($instance->taxableAmount != 5.00) {
            $reason = "Failed to parse TaxSubTotal, taxableAmount is not 5.00";
            return false;
        }
        if ($instance->taxAmount != 6.00) {
            $reason = "Failed to parse TaxSubTotal, taxAmount is not 6.00";
            return false;
        }
        if (!TaxCategory::TestDefaultValues($instance->taxCategory, $reason)) {
            return false;
        }
        if ($instance->percent != 0.00) {
            $reason = "Failed to parse TaxSubTotal, percent is not 0.00";
            return false;
        }
        return true;
    }
}