<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use DateTime;

class InvoicePeriod extends UBLDeserializable
{
    public ?DateTime $startDate = null;
    public ?DateTime $endDate = null;
    public ?string $descriptionCode = null;

    public static function XMLDeserialize(\Sabre\Xml\Reader $reader): self
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != \XMLReader::END_ELEMENT || $reader->depth > $depth) {
            if ($reader->nodeType == \XMLReader::ELEMENT) {
                switch ($reader->localName) {
                    case "StartDate":
                        $instance->startDate = DateTime::createFromFormat("Y-m-d", $reader->readString());
                        $reader->next();
                        break;
                    case "EndDate":
                        $instance->endDate = DateTime::createFromFormat("Y-m-d", $reader->readString());
                        $reader->next();
                        break;
                    case "DescriptionCode":
                        $instance->descriptionCode = $reader->readString();
                        $reader->next();
                        break;
                }
            }

            if (!$reader->read()) {
                throw new \Exception("Invalid XML format");
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA."InvoicePeriod";
    }

    public static function GetTestXML(): string
    {
        return '<cac:InvoicePeriod '.self::NS_DEFINTIONS.'>
                    <cbc:StartDate>2021-01-01</cbc:StartDate>
                    <cbc:EndDate>2021-01-01</cbc:EndDate>
                    <cbc:DescriptionCode>1</cbc:DescriptionCode>
                </cac:InvoicePeriod>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if(!($instance instanceof InvoicePeriod))
        {
            $reason = "Instance is not of type InvoicePeriod";
            return false;
        }
        if($instance->startDate->format("Y-m-d") != "2021-01-01")
        {
            $reason = "Start date is not 2021-01-01";
            return false;
        }
        if($instance->endDate->format("Y-m-d") != "2021-01-01")
        {
            $reason = "End date is not 2021-01-01";
            return false;
        }
        if($instance->descriptionCode != "1")
        {
            $reason = "Description code is not 1";
            return false;
        }
        return true;
    }
}