<?php
/*
 *  Copyright [2024] [Tecsi Aron]
 *
 *     Licensed under the Apache License, Version 2.0 (the "License");
 *     you may not use this file except in compliance with the License.
 *     You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *     Unless required by applicable law or agreed to in writing, software
 *     distributed under the License is distributed on an "AS IS" BASIS,
 *     WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *     See the License for the specific language governing permissions and
 *     limitations under the License.
 */

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use EdituraEDU\UBLRenderer\MappingsManager;
use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class InvoiceLine extends UBLDeserializable
{
    public ?string $id = null;
    public ?string $invoicedQuantity = null;

    public ?string $lineExtensionAmount = null;
    public ?string $lineExtensionAmountCurrencyID = null;
    public ?string $unitCode = null;
    /**
     * @var AllowanceCharge[] $allowanceCharge
     */
    private ?array $allowanceCharge = null;
    /**
     * @var AllowanceCharge[] $allAllowanceCharges
     */
    public ?array $allAllowanceCharges = null;
    public ?string $unitCodeListId = null;
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
                        $instance->unitCode = $parsed["attributes"]["unitCode"];
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
                    '. AllowanceCharge::GetTestXML() . AllowanceCharge::GetTestXML() . '
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
        if ($instance->unitCode !== "C62")
        {
            $reason = "UnitCode is not C62(unit)";
            return false;
        }
        if ($instance->unitCodeListId !== "UN/ECE rec 20")
        {
            $reason = "UnitCodeListID is not UN/ECE rec 20";
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
        if(!AllowanceCharge::TestDefaultValues($instance->allowanceCharge[0], $reason))
        {
            return false;
        }
        if(!ItemPrice::TestDefaultValues($instance->price, $reason))
        {
            return false;
        }
        if(!InvoiceItem::TestDefaultValues($instance->item, $reason))
        {
            return false;
        }
        return true;
    }

    public function HasShortMappedUnitCode():bool
    {
        if(empty($this->unitCode))
        {
            return true;// this will cause "BUC" to be used
        }
        if(!MappingsManager::GetInstance()->UnitCodeHasMapping($this->unitCode))
        {
            return  false;
        }
        return MappingsManager::GetInstance()->UnitCodeHasShortMapping($this->unitCode);
    }

    public function GetShortMappedUnitCode(): string
    {
        if(empty($this->unitCode))
        {
            return "BUC";
        }
        return MappingsManager::GetInstance()->GetUnitCodeMapping($this->unitCode);
    }

    public function HasMappedUnitCode():bool
    {
        if(empty($this->unitCode))
        {
            return false;
        }
        return MappingsManager::GetInstance()->UnitCodeHasMapping($this->unitCode);
    }
    public function getUnitCode(): string
    {
        if($this->HasShortMappedUnitCode())
        {
            return $this->GetShortMappedUnitCode();
        }
        return $this->unitCode;
    }

    public function getItemIDs(string $lineBreak=","):string
    {
        $ids=[];
        if(!empty($this->item->sellersItemIdentification))
        {
            $ids[]="Vânz: $this->item->sellersItemIdentification";
        }
        if(!empty($this->item->buyersItemIdentification))
        {
            $ids[]="Cump: $this->item->buyersItemIdentification";
        }
        return implode($lineBreak, $ids);
    }

    public function getVATRate():string
    {
        if(!isset($this->item->classifiedTaxCategory->percent))
        {
            return "0%";
        }
        if(empty($this->item->classifiedTaxCategory->percent))
        {
            return "0%";
        }
        return $this->item->classifiedTaxCategory->percent."%";
    }
    protected function DeserializeComplete(): void
    {
        $nestedAllowanceCharges = $this->price->allowanceCharge ?? [];
        $lineCharge = $this->allowanceCharge ?? [];
        $this->allAllowanceCharges = array_merge($lineCharge, $nestedAllowanceCharges);
    }
}