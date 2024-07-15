<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

class PaymentTerms extends UBLDeserializable
{
    public ?string $note = null;
    public ?string $settlementDiscountPercent = null;
    public ?string $amount = null;
    public ?string $amountCurrencyID;
    public ?SettlementPeriod $settlementPeriod;

    public static function XMLDeserialize(\Sabre\Xml\Reader $reader): UBLDeserializable
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != \XMLReader::END_ELEMENT || $reader->depth > $depth) {
            if ($reader->nodeType == \XMLReader::ELEMENT) {
                switch ($reader->localName) {
                    case "Note":
                        $instance->note = $reader->readString();
                        $reader->next();
                        break;
                    case "SettlementDiscountPercent":
                        $string = trim($reader->readString(), "%");
                        $instance->settlementDiscountPercent = $string;
                        $reader->next();
                        break;
                    case "Amount":
                        $instance->amount = $reader->readString();
                        if($reader->hasAttributes)
                        {
                            $instance->amountCurrencyID = $reader->getAttribute("currencyID");
                        }
                        $reader->next();
                        break;
                    case "SettlementPeriod":
                        $instance->settlementPeriod = $reader->parseCurrentElement()["value"];
                        break;
                }
            }

            if (!$reader->read()) {
                throw new \Exception("Invalid XML format");
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA."PaymentTerms";
    }

    public static function GetTestXML(): string
    {
        return '<cac:PaymentTerms '.self::NS_DEFINTIONS.'>
                    <cbc:Note>Payment terms</cbc:Note>
                    <cbc:SettlementDiscountPercent>10</cbc:SettlementDiscountPercent>
                    <cbc:Amount currencyID="RON">100</cbc:Amount>
                    '.SettlementPeriod::GetTestXML().'
                </cac:PaymentTerms>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if(!($instance instanceof PaymentTerms))
        {
            $reason = "Instance is not of type PaymentTerms";
            return false;
        }
        if($instance->note !== "Payment terms")
        {
            $reason = "Note is not Payment terms";
            return false;
        }
        if($instance->settlementDiscountPercent !== "10")
        {
            $reason = "SettlementDiscountPercent is not 10";
            return false;
        }
        if($instance->amount !== "100")
        {
            $reason = "Amount is not 100";
            return false;
        }
        if($instance->amountCurrencyID !== "RON")
        {
            $reason = "AmountCurrencyID is not RON";
            return false;
        }
        if(!SettlementPeriod::TestDefaultValues($instance->settlementPeriod, $reason))
        {
            return false;
        }
        return true;
    }
}