<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

class LegalMonetaryTotal extends UBLDeserializable
{
    public ?string $lineExtensionAmount = null;
    public ?string $lineExtensionCurrency = null;
    public ?string $taxExclusiveAmount = null;
    public ?string $taxExclusiveCurrency = null;
    public ?string $taxInclusiveAmount = null;
    public ?string $taxInclusiveCurrency = null;
    public ?string $allowanceTotalAmount = null;
    public ?string $allowanceTotalCurrency = null;
    public ?string $prepaidAmount = null;
    public ?string $prepaidCurrency = null;
    public ?string $payableAmount = null;
    public ?string $payableCurrency = null;

    public static function XMLDeserialize(\Sabre\Xml\Reader $reader): self
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != \XMLReader::END_ELEMENT || $reader->depth > $depth)
        {
            if ($reader->nodeType == \XMLReader::ELEMENT)
            {
                switch ($reader->localName)
                {
                    case "LineExtensionAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->lineExtensionAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->lineExtensionCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "TaxExclusiveAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->taxExclusiveAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->taxExclusiveCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "TaxInclusiveAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->taxInclusiveAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->taxInclusiveCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "AllowanceTotalAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->allowanceTotalAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->allowanceTotalCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "PrepaidAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->prepaidAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->prepaidCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "PayableAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->payableAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->payableCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                }
            }

            if (!$reader->read())
            {
                throw new \Exception("Invalid XML format");
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "LegalMonetaryTotal";
    }

    public static function GetTestXML(): string
    {
        return '<cac:LegalMonetaryTotal ' . self::NS_DEFINTIONS . '>
                    <cbc:LineExtensionAmount currencyID="RON">100</cbc:LineExtensionAmount>
                    <cbc:TaxExclusiveAmount currencyID="RON">100</cbc:TaxExclusiveAmount>
                    <cbc:TaxInclusiveAmount currencyID="RON">100</cbc:TaxInclusiveAmount>
                    <cbc:AllowanceTotalAmount currencyID="RON">100</cbc:AllowanceTotalAmount>
                    <cbc:PrepaidAmount currencyID="RON">100</cbc:PrepaidAmount>
                    <cbc:PayableAmount currencyID="RON">100</cbc:PayableAmount>
                </cac:LegalMonetaryTotal>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof LegalMonetaryTotal))
        {
            $reason = "Instance is not of type LegalMonetaryTotal";
            return false;
        }
        if ($instance->lineExtensionAmount !== "100")
        {
            $reason = "LineExtensionAmount is not 100";
            return false;
        }
        if ($instance->lineExtensionCurrency !== "RON")
        {
            $reason = "LineExtensionCurrency is not RON";
            return false;
        }
        if ($instance->taxExclusiveAmount !== "100")
        {
            $reason = "TaxExclusiveAmount is not 100";
            return false;
        }
        if ($instance->taxExclusiveCurrency !== "RON")
        {
            $reason = "TaxExclusiveCurrency is not RON";
            return false;
        }
        if ($instance->taxInclusiveAmount !== "100")
        {
            $reason = "TaxInclusiveAmount is not 100";
            return false;
        }
        if ($instance->taxInclusiveCurrency !== "RON")
        {
            $reason = "TaxInclusiveCurrency is not RON";
            return false;
        }
        if ($instance->allowanceTotalAmount !== "100")
        {
            $reason = "AllowanceTotalAmount is not 100";
            return false;
        }
        if ($instance->allowanceTotalCurrency !== "RON")
        {
            $reason = "AllowanceTotalCurrency is not RON";
            return false;
        }
        if ($instance->prepaidAmount !== "100")
        {
            $reason = "PrepaidAmount is not 100";
            return false;
        }
        if ($instance->prepaidCurrency !== "RON")
        {
            $reason = "PrepaidCurrency is not RON";
            return false;
        }
        if ($instance->payableAmount !== "100")
        {
            $reason = "PayableAmount is not 100";
            return false;
        }
        if ($instance->payableCurrency !== "RON")
        {
            $reason = "PayableCurrency is not RON";
            return false;
        }
        return true;
    }
}