<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;


use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class ItemPrice extends UBLDeserializable
{
    public ?string $priceAmount = null;
    public ?string $priceCurrencyID = null;
    public ?string $baseQuantity = null;
    public UnitCode $unitCode = UnitCode::UNIT;
    public ?string $unitCodeListId = null;
    /**
     * @var AllowanceCharge[] $allowanceCharge
     */
    public array $allowanceCharge=[];

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down
        while ($reader->nodeType != XMLReader::END_ELEMENT || $reader->depth > $depth) {
            if ($reader->nodeType == XMLReader::ELEMENT) {
                switch ($reader->localName) {
                    case "PriceAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->priceAmount = $parsed["value"];
                        if(isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->priceCurrencyID = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "BaseQuantity":
                        $parsed = $reader->parseCurrentElement();
                        $instance->baseQuantity = $parsed["value"];
                        if($parsed["attributes"]["unitCode"] !== null)
                        {
                            $instance->unitCode = UnitCode::tryFrom($parsed["attributes"]["unitCode"])??UnitCode::INVALID;
                        }
                        break;
                    case "AllowanceCharge":
                        if(!isset($instance->allowanceCharge))
                        {
                            $instance->allowanceCharge = [];
                        }
                        $instance->allowanceCharge[] = AllowanceCharge::XMLDeserialize($reader);
                        break;
                }
            }

            if (!$reader->read()) {
                throw new Exception("Invalid XML format");
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA."Price";
    }

    public static function GetTestXML(): string
    {
        return '<cac:Price '.self::NS_DEFINTIONS.'>
                    <cbc:PriceAmount currencyID="RON">100</cbc:PriceAmount>
                    <cbc:BaseQuantity unitCode="C62" unitCodeListID="UN/ECE rec 20" unitCodeListAgencyID="6">1</cbc:BaseQuantity>
                    '.AllowanceCharge::GetTestXML().AllowanceCharge::GetTestXML().'
                </cac:Price>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if(!($instance instanceof ItemPrice))
        {
            $reason = "Instance is not ItemPrice";
            return false;
        }
        if($instance->priceAmount !== "100")
        {
            $reason = "PriceAmount is not 100";
            return false;
        }
        if($instance->priceCurrencyID !== "RON")
        {
            $reason = "PriceCurrencyID is not RON";
            return false;
        }
        if($instance->baseQuantity !== "1")
        {
            $reason = "BaseQuantity is not 1";
            return false;
        }
        if($instance->unitCode !== UnitCode::UNIT)
        {
            $reason = "UnitCode is not C62(unit)";
            return false;
        }
        if(count($instance->allowanceCharge) != 2)
        {
            $reason = "AllowanceCharge count is not 2";
            return false;
        }
        if(!AllowanceCharge::TestDefaultValues($instance->allowanceCharge[0], $reason))
        {
            return false;
        }
        return true;
    }
}