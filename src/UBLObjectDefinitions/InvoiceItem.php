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

class InvoiceItem extends UBLDeserializable
{
    public ?string $Description = null;
    public ?string $Name = null;
    public ?string $BuyersItemIdentification = null;
    public ?string $SellersItemIdentification = null;
    public ?string $StandardItemIdentification = null;
    public ?string $StandardItemIdentificationListID = null;
    public ?string $CommodityClassification = null;
    public ?string $CommodityClassificationListID = null;
    public ?ClassifiedTaxCategory $ClassifiedTaxCategory = null;

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
                    case "Description":
                        $instance->Description = $reader->readString();
                        $reader->next();
                        break;
                    case "Name":
                        $instance->Name = $reader->readString();
                        $reader->next();
                        break;
                    case "BuyersItemIdentification":
                        $parsed = $reader->parseCurrentElement();
                        $instance->BuyersItemIdentification = $parsed["value"][0]["value"];
                        break;
                    case "SellersItemIdentification":
                        $parsed = $reader->parseCurrentElement();
                        $instance->SellersItemIdentification = $parsed["value"][0]["value"];
                        break;
                    case "StandardItemIdentification":
                        $parsed = $reader->parseCurrentElement();
                        $instance->StandardItemIdentification = $parsed["value"][0]["value"];
                        if (isset($parsed["value"][0]["attributes"]["listID"]))
                        {
                            $instance->StandardItemIdentificationListID = $parsed["value"][0]["attributes"]["schemeID"];
                        }
                        break;
                    case "CommodityClassification":
                        $parsed = $reader->parseCurrentElement();
                        $instance->CommodityClassification = $parsed["value"][0]["value"];
                        if (isset($parsed["value"][0]["attributes"]["listID"]))
                        {
                            $instance->CommodityClassificationListID = $parsed["value"][0]["attributes"]["listID"];
                        }
                        break;
                    case "ClassifiedTaxCategory":
                        $parsed = $reader->parseCurrentElement();
                        $instance->ClassifiedTaxCategory = $parsed["value"];
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
        return self::CAC_SCHEMA . "Item";
    }

    public static function GetTestXML(): string
    {
        return '<cac:Item ' . self::NS_DEFINTIONS . '>
                    <cbc:Description>Item description</cbc:Description>
                    <cbc:Name>Item name</cbc:Name>
                    <cac:BuyersItemIdentification>
                        <cbc:ID>1</cbc:ID>
                    </cac:BuyersItemIdentification>
                    <cac:SellersItemIdentification>
                        <cbc:ID>2</cbc:ID>
                    </cac:SellersItemIdentification>
                    <cac:StandardItemIdentification>
                        <cbc:ID>3</cbc:ID>
                    </cac:StandardItemIdentification>
                    <cac:CommodityClassification>
                        <cbc:ItemClassificationCode listID="STI">03222000-3</cbc:ItemClassificationCode>
                    </cac:CommodityClassification>
                    ' . ClassifiedTaxCategory::GetTestXML() . '
                </cac:Item>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof InvoiceItem))
        {
            $reason = "Instance is not InvoiceItem";
            return false;
        }
        if ($instance->Description !== "Item description")
        {
            $reason = "Description is not Item description";
            return false;
        }
        if ($instance->Name !== "Item name")
        {
            $reason = "Name is not Item name";
            return false;
        }
        if ($instance->BuyersItemIdentification !== "1")
        {
            $reason = "BuyersItemIdentification is not 1";
            return false;
        }
        if ($instance->SellersItemIdentification !== "2")
        {
            $reason = "SellersItemIdentification is not 2";
            return false;
        }
        if ($instance->StandardItemIdentification !== "3")
        {
            $reason = "StandardItemIdentification is not 3";
            return false;
        }
        if ($instance->StandardItemIdentificationListID !== null)
        {
            $reason = "StandardItemIdentificationListID is not null";
            return false;
        }
        if ($instance->CommodityClassification !== "03222000-3")
        {
            $reason = "CommodityClassification is not 03222000-3";
            return false;
        }
        if ($instance->CommodityClassificationListID !== "STI")
        {
            $reason = "CommodityClassificationListID is not STI";
            return false;
        }
        if (!ClassifiedTaxCategory::TestDefaultValues($instance->ClassifiedTaxCategory, $reason))
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