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

class AllowanceCharge extends UBLDeserializable
{
    public bool $ChargeIndicator = false;
    public ?string $AllowanceChargeReasonCode = null;
    public ?string $AllowanceChargeReason = null;
    public ?string $MultiplierFactorNumeric = null;
    public ?string $BaseAmount = null;
    public ?string $Amount = null;
    public ?string $AmountCurrency;
    public ?string $BaseAmountCurrency;
    public ?TaxTotal $TaxTotal = null;
    public ?TaxCategory $TaxCategory = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $parsedAllowanceCharge = $reader->parseInnerTree();
        if (!is_array($parsedAllowanceCharge))
        {
            return $instance;
        }
        for ($i = 0; $i < count($parsedAllowanceCharge); $i++)
        {
            $node = $parsedAllowanceCharge[$i];
            if ($node["value"] == null)
            {
                continue;
            }
            $localName = $instance->getLocalName($node["name"]);
            switch ($localName)
            {
                case "ChargeIndicator":
                    $instance->ChargeIndicator = $node["value"] === 'true';
                    break;
                case "AllowanceChargeReasonCode":
                    $instance->AllowanceChargeReasonCode = $node["value"];
                    break;
                case "AllowanceChargeReason":
                    $instance->AllowanceChargeReason = $node["value"];
                    break;
                case "MultiplierFactorNumeric":
                    $instance->MultiplierFactorNumeric = $node["value"];
                    break;
                case "BaseAmount":
                    $instance->BaseAmount = $node["value"];
                    $instance->BaseAmountCurrency = $node["attributes"]["currencyID"];
                    break;
                case "Amount":
                    $instance->Amount = $node["value"];
                    $instance->AmountCurrency = $node["attributes"]["currencyID"];
                    break;
                case "TaxTotal":
                    $instance->TaxTotal = $node["value"];
                    break;
                case "TaxCategory":
                    $instance->TaxCategory = $node["value"];
                    break;
            }
        }
        return $instance;
    }

    /**
     * Converts the object to a string representation
     * Will return null if MultiplierFactorNumeric, Amount and BaseAmount are not set!
     * @return string|null
     * @throws Exception
     * @see MappingsManager::GetAllowanceChargeReasonCodeMapping()
     * @see MappingsManager::AllowanceChargeReasonCodeHasMapping()
     */
    public function ToString(): ?string
    {
        if (isset($this->AllowanceChargeReason))
        {
            $name = $this->AllowanceChargeReason;
        }
        else if (isset($this->AllowanceChargeReasonCode)
            && MappingsManager::GetInstance()->AllowanceChargeReasonCodeHasMapping($this->AllowanceChargeReasonCode))
        {
            $name = MappingsManager::GetInstance()->GetAllowanceChargeReasonCodeMapping($this->AllowanceChargeReasonCode);
        }
        else
        {
            $name = MappingsManager::GetInstance()->GetAllowanceChargeReasonCodeMapping("UNKNOWN");
        }
        $isValid = false;
        if (isset($this->MultiplierFactorNumeric) && !empty($this->MultiplierFactorNumeric))
        {
            $percent = str_ends_with($this->MultiplierFactorNumeric, "%") ? $this->MultiplierFactorNumeric : $this->MultiplierFactorNumeric . "%";
            $name = $name . " (" . $percent . ")";
            $isValid = true;
        }
        $hasValue = false;
        if (isset($this->Amount))
        {
            $value = isset($this->Amount) ? $this->Amount : "0.00";
            $currency = $this->GetCurrency($this->AmountCurrency);
            $hasValue = true;
            $isValid = true;
        }
        else if (isset($this->BaseAmount))
        {
            $value = isset($this->BaseAmount) ? $this->BaseAmount : "0.00";
            $currency = $this->GetCurrency($this->BaseAmountCurrency);
            $hasValue = true;
            $isValid = true;
        }
        if (!$isValid)
        {
            return null;
        }
        if ($hasValue)
        {
            return $name . ": " . $value . " " . $currency;
        }
        return $name;
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
        if ($instance->ChargeIndicator !== false)
        {
            $reason = "ChargeIndicator is not false";
            return false;
        }
        if ($instance->AllowanceChargeReasonCode !== "string")
        {
            $reason = "AllowanceChargeReasonCode is not 'string'";
            return false;
        }
        if ($instance->AllowanceChargeReason !== "string")
        {
            $reason = "AllowanceChargeReason is not 'string'";
            return false;
        }
        if ($instance->MultiplierFactorNumeric !== "0.00")
        {
            $reason = "MultiplierFactorNumeric is not 0.00";
            return false;
        }
        if ($instance->BaseAmount !== "0.00")
        {
            $reason = "BaseAmount is not 0.00";
            return false;
        }
        if ($instance->Amount !== "0.00")
        {
            $reason = "Amount is not 0.00";
            return false;
        }
        if ($instance->AmountCurrency !== "CAD")
        {
            $reason = "AmountCurrency is not CAD";
            return false;
        }
        if ($instance->BaseAmountCurrency !== "USD")
        {
            $reason = "BaseAmountCurrency is not USD";
            return false;
        }
        if (!TaxTotal::TestDefaultValues($instance->TaxTotal, $reason))
        {
            $reason = "TaxTotal failed with reason: " . $reason;
            return false;
        }
        if (!TaxCategory::TestDefaultValues($instance->TaxCategory, $reason))
        {
            $reason = "TaxCategory failed with reason: " . $reason;
            return false;
        }
        return true;
    }

    public function CanRender(): true|array
    {
        if ($this->ToString() != null)
        {
            return true;
        }
        return ["[AllowanceCharge] Failed to render"];
    }
}