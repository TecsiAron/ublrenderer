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

class ItemPrice extends UBLDeserializable
{
    public ?string $PriceAmount = null;
    public ?string $PriceCurrencyID = null;
    public ?string $BaseQuantity = null;
    public ?string $UnitCode = null;
    public ?string $UnitCodeListID = null;
    /**
     * @var AllowanceCharge[] $AllowanceCharge
     */
    public array $AllowanceCharge = [];

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
                    case "PriceAmount":
                        $parsed = $reader->parseCurrentElement();
                        $instance->PriceAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->PriceCurrencyID = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "BaseQuantity":
                        $parsed = $reader->parseCurrentElement();
                        $instance->BaseQuantity = $parsed["value"];
                        if ($parsed["attributes"]["unitCode"] !== null)
                        {
                            $instance->UnitCode = $parsed["attributes"]["unitCode"];
                        }
                        break;
                    case "AllowanceCharge":
                        if (!isset($instance->AllowanceCharge))
                        {
                            $instance->AllowanceCharge = [];
                        }
                        $instance->AllowanceCharge[] = AllowanceCharge::XMLDeserialize($reader);
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
        return self::CAC_SCHEMA . "Price";
    }

    public static function GetTestXML(): string
    {
        return '<cac:Price ' . self::NS_DEFINTIONS . '>
                    <cbc:PriceAmount currencyID="RON">100</cbc:PriceAmount>
                    <cbc:BaseQuantity unitCode="C62" unitCodeListID="UN/ECE rec 20" unitCodeListAgencyID="6">1</cbc:BaseQuantity>
                    ' . AllowanceCharge::GetTestXML() . AllowanceCharge::GetTestXML() . '
                </cac:Price>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof ItemPrice))
        {
            $reason = "Instance is not ItemPrice";
            return false;
        }
        if ($instance->PriceAmount !== "100")
        {
            $reason = "PriceAmount is not 100";
            return false;
        }
        if ($instance->PriceCurrencyID !== "RON")
        {
            $reason = "PriceCurrencyID is not RON";
            return false;
        }
        if ($instance->BaseQuantity !== "1")
        {
            $reason = "BaseQuantity is not 1";
            return false;
        }
        if ($instance->UnitCode !== "C62")
        {
            $reason = "UnitCode is not C62(unit)";
            return false;
        }
        if (count($instance->AllowanceCharge) != 2)
        {
            $reason = "AllowanceCharge count is not 2";
            return false;
        }
        if (!AllowanceCharge::TestDefaultValues($instance->AllowanceCharge[0], $reason))
        {
            return false;
        }
        return true;
    }
}