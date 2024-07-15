<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class InvoiceLine extends UBLDeserializable
{
    public ?string $id = null;
    public ?string $invoicedQuantity = null;

    public ?string $lineExtensionAmount = null;
    public ?string $lineExtensionAmountCurrencyID = null;
    public ?UnitCode $unitCode = null;
    /**
     * @var AllowanceCharge[] $allowanceCharge
     */
    private ?array $allowanceCharge = null;
    /**
     * @var AllowanceCharge[] $allAllowanceCharges
     */
    public ?array $allAllowanceCharges = null;
    public ?string $unitCodeListId = null;
    public ?TaxTotal $taxTotal = null;
    public ?InvoicePeriod $invoicePeriod = null;
    public ?string $note = null;
    public ?InvoiceItem $item = null;
    public ?ItemPrice $price = null;
    public ?string $accountingCostCode = null;
    public ?string $accountingCost = null;

    public static function XMLDeserialize(Reader $reader): UBLDeserializable
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
                    case "ID":
                        $instance->id = $reader->readString();
                        $reader->next();
                        break;
                    case "InvoicedQuantity":
                        $parsed = $reader->parseCurrentElement();
                        $instance->invoicedQuantity = $parsed["value"];
                        $instance->unitCode = UnitCode::tryFrom($parsed["attributes"]["unitCode"]) ?? UnitCode::INVALID;
                        if (isset($parsed["attributes"]["unitCodeListID"]))
                        {
                            $instance->unitCodeListId = $parsed["attributes"]["unitCodeListID"];
                        }
                        break;
                    case "LineExtensionAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->lineExtensionAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->lineExtensionAmountCurrencyID = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "TaxTotal":
                        $instance->taxTotal = $reader->parseCurrentElement()["value"];
                        break;
                    case "InvoicePeriod":
                        $instance->invoicePeriod = $reader->parseCurrentElement()["value"];
                        break;
                    case "Note":
                        $instance->note = $reader->readString();
                        $reader->next();
                        break;
                    case "Item":
                        $instance->item = $reader->parseCurrentElement()["value"];
                        break;
                    case "Price":
                        $instance->price = $reader->parseCurrentElement()["value"];
                        break;
                    case "AccountingCostCode":
                        $instance->accountingCostCode = $reader->readString();
                        $reader->next();
                        break;
                    case "AccountingCost":
                        $instance->accountingCost = $reader->readString();
                        $reader->next();
                        break;
                    case "AllowanceCharge":
                        if (!isset($instance->allowanceCharge))
                        {
                            $instance->allowanceCharge = [];
                        }
                        $instance->allowanceCharge[] = $reader->parseCurrentElement()["value"];
                        break;
                }
            }

            if (!$reader->read())
            {
                throw new Exception("Invalid XML format");
            }
        }
        $instance->DeserializeComplete();
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "InvoiceLine";
    }

    public static function GetTestXML(): string
    {
        return '<cac:InvoiceLine ' . self::NS_DEFINTIONS . '>
                    <cbc:ID>1</cbc:ID>
                    <cbc:InvoicedQuantity unitCode="C62" unitCodeListID="UN/ECE rec 20" unitCodeListAgencyID="6">1</cbc:InvoicedQuantity>
                    <cbc:LineExtensionAmount currencyID="RON">100</cbc:LineExtensionAmount>
                    ' . TaxTotal::GetTestXML() . InvoicePeriod::GetTestXML() . AllowanceCharge::GetTestXML() . AllowanceCharge::GetTestXML() . '
                    <cbc:Note>Test note</cbc:Note>
                    ' . InvoiceItem::GetTestXML() . ItemPrice::GetTestXML() . '                    
                    <cbc:AccountingCostCode>123</cbc:AccountingCostCode>
                    <cbc:AccountingCost>100</cbc:AccountingCost>
                </cac:InvoiceLine>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof InvoiceLine))
        {
            $reason = "Instance is not InvoiceLine";
            return false;
        }
        if ($instance->id !== "1")
        {
            $reason = "ID is not 1";
            return false;
        }
        if ($instance->invoicedQuantity != "1")
        {
            $reason = "InvoicedQuantity is not 1";
            return false;
        }
        if ($instance->lineExtensionAmount != "100")
        {
            $reason = "LineExtensionAmount is not 100";
            return false;
        }
        if ($instance->lineExtensionAmountCurrencyID !== "RON")
        {
            $reason = "LineExtensionAmountCurrencyID is not RON";
            return false;
        }
        if ($instance->unitCode !== UnitCode::UNIT)
        {
            $reason = "UnitCode is not C62(unit)";
            return false;
        }
        if ($instance->unitCodeListId !== "UN/ECE rec 20")
        {
            $reason = "UnitCodeListID is not UN/ECE rec 20";
            return false;
        }
        if ($instance->taxTotal === null)
        {
            $reason = "TaxTotal is null";
            return false;
        }
        if ($instance->invoicePeriod === null)
        {
            $reason = "InvoicePeriod is null";
            return false;
        }
        if ($instance->note !== "Test note")
        {
            $reason = "Note is not Test note";
            return false;
        }
        if ($instance->item === null)
        {
            $reason = "Item is null";
            return false;
        }
        if ($instance->price === null)
        {
            $reason = "Price is null";
            return false;
        }
        if ($instance->accountingCostCode !== "123")
        {
            $reason = "AccountingCostCode is not 123";
            return false;
        }
        if ($instance->accountingCost !== "100")
        {
            $reason = "AccountingCost is not 100";
            return false;
        }
        return true;
    }

    protected function DeserializeComplete(): void
    {
        $nestedAllowanceCharges = $this->price->allowanceCharge ?? [];
        $lineCharge = $this->allowanceCharge ?? [];
        $this->allAllowanceCharges = array_merge($lineCharge, $nestedAllowanceCharges);
    }
}