<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use DateTime;

class OrderReference extends UBLDeserializable
{
    public ?string $id = null;
    public ?string$salesOrderId = null;
    public ?DateTime $issueDate = null;

    public static function XMLDeserialize(\Sabre\Xml\Reader $reader): self
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != \XMLReader::END_ELEMENT || $reader->depth > $depth) {
            if ($reader->nodeType == \XMLReader::ELEMENT) {
                switch ($reader->localName) {
                    case "ID":
                        $instance->id = $reader->readString();
                        $reader->next();
                        break;
                    case "SalesOrderID":
                        $instance->salesOrderId = $reader->readString();
                        $reader->next();
                        break;
                    case "IssueDate":
                        $instance->issueDate = DateTime::createFromFormat("Y-m-d", $reader->readString());
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
        return self::CAC_SCHEMA."OrderReference";
    }

    public static function GetTestXML(): string
    {
        return '<cac:OrderReference '.self::NS_DEFINTIONS.'>
                    <cbc:ID>1</cbc:ID>
                    <cbc:SalesOrderID>1</cbc:SalesOrderID>
                    <cbc:IssueDate>2021-01-01</cbc:IssueDate>
                </cac:OrderReference>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if(!($instance instanceof OrderReference))
        {
            $reason = "Instance is not of type OrderReference";
            return false;
        }
        if($instance->id !== "1")
        {
            $reason = "ID is not 1";
            return false;
        }
        if($instance->salesOrderId !== "1")
        {
            $reason = "SalesOrderID is not 1";
            return false;
        }
        if($instance->issueDate->format("Y-m-d") != "2021-01-01")
        {
            $reason = "IssueDate is not 2021-01-01";
        }
        return true;
    }
}