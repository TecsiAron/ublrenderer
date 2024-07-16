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

use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class LegalMonetaryTotal extends UBLDeserializable
{
    public ?string $LineExtensionAmount = null;
    public ?string $LineExtensionCurrency = null;
    public ?string $TaxExclusiveAmount = null;
    public ?string $TaxExclusiveCurrency = null;
    public ?string $TaxInclusiveAmount = null;
    public ?string $TaxInclusiveCurrency = null;
    public ?string $AllowanceTotalAmount = null;
    public ?string $AllowanceTotalCurrency = null;
    public ?string $PrepaidAmount = null;
    public ?string $PrepaidCurrency = null;
    public ?string $PayableAmount = null;
    public ?string $PayableCurrency = null;

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
                    case "LineExtensionAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->LineExtensionAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->LineExtensionCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "TaxExclusiveAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->TaxExclusiveAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->TaxExclusiveCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "TaxInclusiveAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->TaxInclusiveAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->TaxInclusiveCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "AllowanceTotalAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->AllowanceTotalAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->AllowanceTotalCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "PrepaidAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->PrepaidAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->PrepaidCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "PayableAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->PayableAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->PayableCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                }
            }

            if (!$reader->read())
            {
                throw new Exception("Invalid XML format");
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
        if ($instance->LineExtensionAmount !== "100")
        {
            $reason = "LineExtensionAmount is not 100";
            return false;
        }
        if ($instance->LineExtensionCurrency !== "RON")
        {
            $reason = "LineExtensionCurrency is not RON";
            return false;
        }
        if ($instance->TaxExclusiveAmount !== "100")
        {
            $reason = "TaxExclusiveAmount is not 100";
            return false;
        }
        if ($instance->TaxExclusiveCurrency !== "RON")
        {
            $reason = "TaxExclusiveCurrency is not RON";
            return false;
        }
        if ($instance->TaxInclusiveAmount !== "100")
        {
            $reason = "TaxInclusiveAmount is not 100";
            return false;
        }
        if ($instance->TaxInclusiveCurrency !== "RON")
        {
            $reason = "TaxInclusiveCurrency is not RON";
            return false;
        }
        if ($instance->AllowanceTotalAmount !== "100")
        {
            $reason = "AllowanceTotalAmount is not 100";
            return false;
        }
        if ($instance->AllowanceTotalCurrency !== "RON")
        {
            $reason = "AllowanceTotalCurrency is not RON";
            return false;
        }
        if ($instance->PrepaidAmount !== "100")
        {
            $reason = "PrepaidAmount is not 100";
            return false;
        }
        if ($instance->PrepaidCurrency !== "RON")
        {
            $reason = "PrepaidCurrency is not RON";
            return false;
        }
        if ($instance->PayableAmount !== "100")
        {
            $reason = "PayableAmount is not 100";
            return false;
        }
        if ($instance->PayableCurrency !== "RON")
        {
            $reason = "PayableCurrency is not RON";
            return false;
        }
        return true;
    }

    public function GetTaxExclusiveAmount(): ?string
    {
        if(!isset($this->TaxExclusiveAmount) || empty($this->TaxExclusiveAmount))
        {
            return null;
        }
        return $this->TaxExclusiveAmount. " ". $this->GetCurrency($this->TaxExclusiveCurrency);
    }

    public function GetTaxInclusiveAmount(): ?string
    {
        if(!isset($this->TaxInclusiveAmount) || empty($this->TaxInclusiveAmount))
        {
            return null;
        }
        return $this->TaxInclusiveAmount. " ". $this->GetCurrency($this->TaxInclusiveCurrency);
    }
}