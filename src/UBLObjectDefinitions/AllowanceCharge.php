<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class AllowanceCharge extends UBLDeserializable
{
    public bool $chargeIndicator = false;
    public ?string $allowanceChargeReasonCode = null;
    public ?string $allowanceChargeReason = null;
    public ?string $multiplierFactorNumeric = null;
    public ?string $baseAmount = null;
    public ?string $amount = null;
    public ?string $amountCurrency;
    public ?string $baseAmountCurrency;
    public ?TaxTotal $taxTotal = null;
    public ?TaxCategory $taxCategory = null;

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
                    case "ChargeIndicator":
                        $instance->chargeIndicator = $reader->readString() === 'true';
                        $reader->next(); // Move past the current text node
                        break;
                    case "AllowanceChargeReasonCode":
                        $instance->allowanceChargeReasonCode = $reader->readString();
                        $reader->next();
                        break;
                    case "AllowanceChargeReason":
                        $instance->allowanceChargeReason = $reader->readString();
                        $reader->next();
                        break;
                    case "MultiplierFactorNumeric":
                        $instance->multiplierFactorNumeric = $reader->readString();
                        $reader->next();
                        break;
                    case "BaseAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->baseAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->baseAmountCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "Amount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->amount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->amountCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "TaxTotal":
                        $parsed = $reader->parseCurrentElement();
                        $instance->taxTotal = $parsed["value"];
                        break;
                    case "TaxCategory":
                        $parsed = $reader->parseCurrentElement();
                        $instance->taxCategory = $parsed["value"];
                        break;
                }
            }
            if (!$reader->read())
            {
                throw new Exception("Unexpected end of XML file while reading AllowanceCharge.");
            }
        }

        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "AllowanceCharge";
    }

    public static function GetTestXML(): string
    {
        return '<cac:AllowanceCharge ' . self::NS_DEFINTIONS . '>
                    <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
                    <cbc:AllowanceChargeReasonCode>string</cbc:AllowanceChargeReasonCode>
                    <cbc:AllowanceChargeReason>string</cbc:AllowanceChargeReason>
                    <cbc:MultiplierFactorNumeric>0.00</cbc:MultiplierFactorNumeric>
                    <cbc:BaseAmount currencyID="USD">0.00</cbc:BaseAmount>
                    <cbc:Amount currencyID="CAD">0.00</cbc:Amount>
                    ' . TaxTotal::GetTestXML() . '
                    ' . TaxCategory::GetTestXML() . '
                </cac:AllowanceCharge>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof AllowanceCharge))
        {
            $reason = "Instance is not of type AllowanceCharge";
            return false;
        }
        if ($instance->chargeIndicator !== false)
        {
            $reason = "ChargeIndicator is not false";
            return false;
        }
        if ($instance->allowanceChargeReasonCode !== "string")
        {
            $reason = "AllowanceChargeReasonCode is not 'string'";
            return false;
        }
        if ($instance->allowanceChargeReason !== "string")
        {
            $reason = "AllowanceChargeReason is not 'string'";
            return false;
        }
        if ($instance->multiplierFactorNumeric !== "0.00")
        {
            $reason = "MultiplierFactorNumeric is not 0.00";
            return false;
        }
        if ($instance->baseAmount !== "0.00")
        {
            $reason = "BaseAmount is not 0.00";
            return false;
        }
        if ($instance->amount !== "0.00")
        {
            $reason = "Amount is not 0.00";
            return false;
        }
        if ($instance->amountCurrency !== "CAD")
        {
            $reason = "AmountCurrency is not CAD";
            return false;
        }
        if ($instance->baseAmountCurrency !== "USD")
        {
            $reason = "BaseAmountCurrency is not USD";
            return false;
        }
        if (!TaxTotal::TestDefaultValues($instance->taxTotal, $reason))
        {
            $reason = "TaxTotal failed with reason: " . $reason;
            return false;
        }
        if (!TaxCategory::TestDefaultValues($instance->taxCategory, $reason))
        {
            $reason = "TaxCategory failed with reason: " . $reason;
            return false;
        }
        return true;
    }
}