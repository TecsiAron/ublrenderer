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

use DateTime;
use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class PaymentTerms extends UBLDeserializable
{
    public ?string $Note = null;
    public ?string $SettlementDiscountPercent = null;
    public ?string $Amount = null;
    public ?string $AmountCurrencyID;
    public ?SettlementPeriod $SettlementPeriod;

    public DateTime $PaymentDueDate;

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
                    case "Note":
                        $instance->Note = $reader->readString();
                        $reader->next();
                        break;
                    case "SettlementDiscountPercent":
                        $string = trim($reader->readString(), "%");
                        $instance->SettlementDiscountPercent = $string;
                        $reader->next();
                        break;
                    case "Amount":
                        $instance->Amount = $reader->readString();
                        if ($reader->hasAttributes)
                        {
                            $instance->AmountCurrencyID = $reader->getAttribute("currencyID");
                        }
                        $reader->next();
                        break;
                    case "SettlementPeriod":
                        $instance->SettlementPeriod = $reader->parseCurrentElement()["value"];
                        break;
                    case "PaymentDueDate":
                        $instance->PaymentDueDate = new DateTime($reader->readString());
                        $reader->next();
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
        return self::CAC_SCHEMA . "PaymentTerms";
    }

    public static function GetTestXML(): string
    {
        return '<cac:PaymentTerms ' . self::NS_DEFINTIONS . '>
                    <cbc:Note>Payment terms</cbc:Note>
                    <cbc:SettlementDiscountPercent>10</cbc:SettlementDiscountPercent>
                    <cbc:Amount currencyID="RON">100</cbc:Amount>
                    <cbc:PaymentDueDate>2024-01-01</cbc:PaymentDueDate>
                    ' . SettlementPeriod::GetTestXML() . '
                </cac:PaymentTerms>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof PaymentTerms))
        {
            $reason = "Instance is not of type PaymentTerms";
            return false;
        }
        if ($instance->Note !== "Payment terms")
        {
            $reason = "Note is not Payment terms";
            return false;
        }
        if ($instance->SettlementDiscountPercent !== "10")
        {
            $reason = "SettlementDiscountPercent is not 10";
            return false;
        }
        if ($instance->Amount !== "100")
        {
            $reason = "Amount is not 100";
            return false;
        }
        if ($instance->AmountCurrencyID !== "RON")
        {
            $reason = "AmountCurrencyID is not RON";
            return false;
        }
        if ($instance->PaymentDueDate->format("Y-m-d") !== "2024-01-01")
        {
            $reason = "PaymentDueDate is not 2024-01-01";
            return false;
        }
        if (!SettlementPeriod::TestDefaultValues($instance->SettlementPeriod, $reason))
        {
            return false;
        }
        return true;
    }

    public function CanRender(): true|array
    {
        return true;
    }
}