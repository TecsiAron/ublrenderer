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
    public ?string $ID = null;
    public ?string $InvoicedQuantity = null;

    public ?string $LineExtensionAmount = null;
    public ?string $LineExtensionAmountCurrencyID = null;
    public ?string $UnitCode = null;
    /**
     * @var AllowanceCharge[] $AllowanceCharge
     */
    private ?array $AllowanceCharge = null;
    /**
     * @var AllowanceCharge[] $AllAllowanceCharges
     */
    public ?array $AllAllowanceCharges = null;
    public ?string $UnitCodeListId = null;
    public ?string $Note = null;
    public ?InvoiceItem $Item = null;
    public ?ItemPrice $Price = null;

    public ?InvoicePeriod $InvoicePeriod;

    public ?string $AccountingCostCode = null;
    public ?string $AccountingCost = null;

    public static function XMLDeserialize(Reader $reader): UBLDeserializable
    {
        $instance = new self();
        $parsedInvoiceLine = $reader->parseInnerTree();
        if (!is_array($parsedInvoiceLine))
        {
            return $instance;
        }
        for ($i = 0; $i < count($parsedInvoiceLine); $i++)
        {
            $parsed = $parsedInvoiceLine[$i];
            $localName = $instance->getLocalName($parsed["name"]);
            switch ($localName)
            {
                case "ID":
                    $instance->ID = $parsed["value"];
                    break;
                case "InvoicedQuantity":
                    $instance->InvoicedQuantity = $parsed["value"];
                    $instance->UnitCode = $parsed["attributes"]["unitCode"];
                    if (isset($parsed["attributes"]["unitCodeListID"]))
                    {
                        $instance->UnitCodeListId = $parsed["attributes"]["unitCodeListID"];
                    }
                    break;
                case "LineExtensionAmount":
                    $instance->LineExtensionAmount = $parsed["value"];
                    if (isset($parsed["attributes"]["currencyID"]))
                    {
                        $instance->LineExtensionAmountCurrencyID = $parsed["attributes"]["currencyID"];
                    }
                    break;
                case "Note":
                    $instance->Note = $parsed["value"];
                    break;
                case "Item":
                    $instance->Item = $parsed["value"];
                    break;
                case "Price":
                    $instance->Price = $parsed["value"];
                    break;
                case "AccountingCostCode":
                    $instance->AccountingCostCode = $parsed["value"];
                    break;
                case "AccountingCost":
                    $instance->AccountingCost = $parsed["value"];
                    break;
                case "InvoicePeriod":
                    $instance->InvoicePeriod = $parsed["value"];
                    break;
                case "AllowanceCharge":
                    if (!isset($instance->AllowanceCharge))
                    {
                        $instance->AllowanceCharge = [];
                    }
                    $instance->AllowanceCharge[] = $parsed["value"];
                    break;
            }
        }
        $instance->DeserializeComplete();;
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
                    ' . InvoiceItem::GetTestXML() . ItemPrice::GetTestXML() . InvoicePeriod::GetTestXML() . '                    
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
        if ($instance->ID !== "1")
        {
            $reason = "ID is not 1";
            return false;
        }
        if ($instance->InvoicedQuantity != "1")
        {
            $reason = "InvoicedQuantity is not 1";
            return false;
        }
        if ($instance->LineExtensionAmount != "100")
        {
            $reason = "LineExtensionAmount is not 100";
            return false;
        }
        if ($instance->LineExtensionAmountCurrencyID !== "RON")
        {
            $reason = "LineExtensionAmountCurrencyID is not RON";
            return false;
        }
        if ($instance->UnitCode !== "C62")
        {
            $reason = "UnitCode is not C62(unit)";
            return false;
        }
        if ($instance->UnitCodeListId !== "UN/ECE rec 20")
        {
            $reason = "UnitCodeListID is not UN/ECE rec 20";
            return false;
        }
        if ($instance->Note !== "Test note")
        {
            $reason = "Note is not Test note";
            return false;
        }
        if ($instance->AccountingCostCode !== "123")
        {
            $reason = "AccountingCostCode is not 123";
            return false;
        }
        if ($instance->AccountingCost !== "100")
        {
            $reason = "AccountingCost is not 100";
            return false;
        }
        if(!AllowanceCharge::TestDefaultValues($instance->AllowanceCharge[0], $reason))
        {
            return false;
        }
        if(!ItemPrice::TestDefaultValues($instance->Price, $reason))
        {
            return false;
        }
        if(!InvoiceItem::TestDefaultValues($instance->Item, $reason))
        {
            return false;
        }
        if(!InvoicePeriod::TestDefaultValues($instance->InvoicePeriod, $reason))
        {
            return false;
        }
        return true;
    }

    public function HasShortMappedUnitCode():bool
    {
        if(empty($this->UnitCode))
        {
            return true;// this will cause "BUC" to be used
        }
        if(!MappingsManager::GetInstance()->UnitCodeHasMapping($this->UnitCode))
        {
            return  false;
        }
        return MappingsManager::GetInstance()->UnitCodeHasShortMapping($this->UnitCode);
    }

    public function GetShortMappedUnitCode(): string
    {
        if(empty($this->UnitCode))
        {
            return "BUC";
        }
        return MappingsManager::GetInstance()->GetUnitCodeMapping($this->UnitCode);
    }

    public function HasMappedUnitCode():bool
    {
        if(empty($this->UnitCode))
        {
            return false;
        }
        return MappingsManager::GetInstance()->UnitCodeHasMapping($this->UnitCode);
    }
    public function GetUnitCode(): string
    {
        if($this->HasShortMappedUnitCode())
        {
            return $this->GetShortMappedUnitCode();
        }
        return $this->UnitCode;
    }

    public function GetItemIDs(string $lineBreak=","):string
    {
        $ids=[];
        if(!empty($this->Item->SellersItemIdentification))
        {
            $ids[]="Vânz: ".$this->Item->SellersItemIdentification;
        }
        if(!empty($this->Item->BuyersItemIdentification))
        {
            $ids[]="Cump: ".$this->Item->BuyersItemIdentification;
        }
        return implode($lineBreak, $ids);
    }

    public function GetVATRate(bool $includePercent=true):string
    {
        if(!isset($this->Item->ClassifiedTaxCategory->Percent))
        {
            return "0".($includePercent?"%":"");
        }
        if(empty($this->Item->ClassifiedTaxCategory->Percent))
        {
            return "0".($includePercent?"%":"");
        }
        return $this->Item->ClassifiedTaxCategory->Percent.($includePercent?"%":"");
    }

    public function GetNoVATValue():?string
    {
        if(empty($this->LineExtensionAmount))
        {
            return null;
        }
        return $this->LineExtensionAmount." ". $this->GetCurrency($this->LineExtensionAmountCurrencyID);
    }

    public function GetNoVATUnitValue():?string
    {
        if(!isset($this->Price->PriceAmount) || empty($this->Price->PriceAmount))
        {
            return null;
        }
        return $this->Price->PriceAmount . " " . $this->GetCurrency($this->Price->PriceCurrencyID);
    }

    public function HasAllowanceCharges():bool
    {
        return sizeof($this->AllAllowanceCharges)!=0;
    }

    public function GetVATValue():?string
    {
        $vatRate=$this->GetVATRate(false);
        if($vatRate==0)
        {
            return "0";
        }
        if(!isset($this->LineExtensionAmount) || empty($this->LineExtensionAmount))
        {
            return null;
        }
        $vatMultiplier=bcdiv($vatRate,100,2);
        $vatValue=bcmul($this->LineExtensionAmount,$vatMultiplier,2);
        return $vatValue." ".$this->GetCurrency($this->LineExtensionAmountCurrencyID);
    }

    public function CanRender():true|array
    {
        $result=[];
        $allowanceChargeCount=count($this->AllAllowanceCharges);
        $subComponentsOk=true;
        if($allowanceChargeCount!=0)
        {
            for($i=0; $i<$allowanceChargeCount; $i++)
            {
                $validation=$this->AllAllowanceCharges[$i]->CanRender();
                if($validation!==true)
                {
                    $result=array_merge($result,$validation);
                    $subComponentsOk=false;
                }
            }

        }
        if($this->Item== null)
        {
            $result[]="[InvoiceLine] No Item";
            $subComponentsOk=false;
        }
        if($subComponentsOk===true)
        {
            if (!$this->ContainsNull([
                $this->GetNoVATValue(),
                $this->GetNoVATUnitValue(),
                $this->GetUnitCode(),
                $this->Item->Name,
                $this->GetVATRate(),
                $this->InvoicedQuantity,
                $this->GetVATValue()
            ]))
            {
                return true;
            }
        }
        if($this->GetNoVATValue())
        {
            $result[]="[InvoiceLine] No VAT Value";
        }
        if($this->GetNoVATUnitValue())
        {
            $result[]="[InvoiceLine] No VAT Unit Value";
        }
        if($this->GetUnitCode())
        {
            $result[]="[InvoiceLine] No Unit Code";
        }
        if($this->Item!=null && $this->Item->Name == null)
        {
            $result[]="[InvoiceLine] No Item Name";
        }
        if($this->GetVATRate())
        {
            $result[]="[InvoiceLine] No VAT Rate";
        }
        if($this->InvoicedQuantity == null)
        {
            $result[]="[InvoiceLine] No Invoiced Quantity";
        }
        if($this->GetVATValue())
        {
            $result[]="[InvoiceLine] No VAT Value";
        }

        return $result;
    }
    protected function DeserializeComplete(): void
    {
        $nestedAllowanceCharges = $this->Price->AllowanceCharge ?? [];
        $lineCharge = $this->AllowanceCharge ?? [];
        $this->AllAllowanceCharges = array_merge($lineCharge, $nestedAllowanceCharges);
    }
}