<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

class InvoiceItem extends UBLDeserializable
{
    public ?string $description = null;
    public ?string $name = null;
    public ?string $buyersItemIdentification = null;
    public ?string $sellersItemIdentification = null;
    public ?string $standardItemIdentification = null;
    public ?string $standardItemIdentificationListID = null;
    public ?string $commodityClassification = null;
    public ?string $commodityClassificationListID = null;
    public ?ClassifiedTaxCategory $classifiedTaxCategory = null;

    public static function XMLDeserialize(\Sabre\Xml\Reader $reader): self
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != \XMLReader::END_ELEMENT || $reader->depth > $depth)
        {
            if ($reader->nodeType == \XMLReader::ELEMENT)
            {
                switch ($reader->localName)
                {
                    case "Description":
                        $instance->description = $reader->readString();
                        $reader->next();
                        break;
                    case "Name":
                        $instance->name = $reader->readString();
                        $reader->next();
                        break;
                    case "BuyersItemIdentification":
                        $parsed = $reader->parseCurrentElement();
                        $instance->buyersItemIdentification = $parsed["value"][0]["value"];
                        break;
                    case "SellersItemIdentification":
                        $parsed = $reader->parseCurrentElement();
                        $instance->sellersItemIdentification = $parsed["value"][0]["value"];
                        break;
                    case "StandardItemIdentification":
                        $parsed = $reader->parseCurrentElement();
                        $instance->standardItemIdentification = $parsed["value"][0]["value"];
                        if (isset($parsed["value"][0]["attributes"]["listID"]))
                        {
                            $instance->standardItemIdentificationListID = $parsed["value"][0]["attributes"]["schemeID"];
                        }
                        break;
                    case "CommodityClassification":
                        $parsed = $reader->parseCurrentElement();
                        $instance->commodityClassification = $parsed["value"][0]["value"];
                        if (isset($parsed["value"][0]["attributes"]["listID"]))
                        {
                            $instance->commodityClassificationListID = $parsed["value"][0]["attributes"]["listID"];
                        }
                        break;
                    case "ClassifiedTaxCategory":
                        $parsed = $reader->parseCurrentElement();
                        $instance->classifiedTaxCategory = $parsed["value"];
                        break;
                }
            }

            if (!$reader->read())
            {
                throw new \Exception("Invalid XML format");
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
        if ($instance->description !== "Item description")
        {
            $reason = "Description is not Item description";
            return false;
        }
        if ($instance->name !== "Item name")
        {
            $reason = "Name is not Item name";
            return false;
        }
        if ($instance->buyersItemIdentification !== "1")
        {
            $reason = "BuyersItemIdentification is not 1";
            return false;
        }
        if ($instance->sellersItemIdentification !== "2")
        {
            $reason = "SellersItemIdentification is not 2";
            return false;
        }
        if ($instance->standardItemIdentification !== "3")
        {
            $reason = "StandardItemIdentification is not 3";
            return false;
        }
        if ($instance->standardItemIdentificationListID !== null)
        {
            $reason = "StandardItemIdentificationListID is not null";
            return false;
        }
        if ($instance->commodityClassification !== "03222000-3")
        {
            $reason = "CommodityClassification is not 03222000-3";
            return false;
        }
        if ($instance->commodityClassificationListID !== "STI")
        {
            $reason = "CommodityClassificationListID is not STI";
            return false;
        }
        if (!ClassifiedTaxCategory::TestDefaultValues($instance->classifiedTaxCategory, $reason))
        {
            return false;
        }
        return true;
    }
}