<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class PayeeFinancialAccount extends UBLDeserializable
{
    public ?string $id = null;
    public ?string$name = null;
    public ?string $financialInstitutionBranchID = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != XMLReader::END_ELEMENT || $reader->depth > $depth) {
            if ($reader->nodeType == XMLReader::ELEMENT) {
                switch ($reader->localName) {
                    case "ID":
                        $instance->id = $reader->readString();
                        $reader->next();
                        break;
                    case "Name":
                        $instance->name = $reader->readString();
                        $reader->next();
                        break;
                    case "FinancialInstitutionBranch":
                        $parsed = $reader->parseCurrentElement();
                        $instance->financialInstitutionBranchID = $parsed["value"][0]["value"];
                        break;
                }
            }

            if (!$reader->read()) {
                throw new Exception("Invalid XML format");
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA."PayeeFinancialAccount";
    }

    public static function GetTestXML(): string
    {
        return '<cac:PayeeFinancialAccount '.self::NS_DEFINTIONS.'>
                    <cbc:ID>1</cbc:ID>
                    <cbc:Name>John Doe</cbc:Name>
                    <cac:FinancialInstitutionBranch>
                        <cbc:ID>2</cbc:ID>
                    </cac:FinancialInstitutionBranch>
                </cac:PayeeFinancialAccount>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if(!($instance instanceof PayeeFinancialAccount))
        {
            $reason = "Instance is not of type PayeeFinancialAccount";
            return false;
        }
        if($instance->id !== "1")
        {
            $reason = "ID is not 1";
            return false;
        }
        if($instance->name !== "John Doe")
        {
            $reason = "Name is not John Doe";
            return false;
        }
        if($instance->financialInstitutionBranchID !== "2")
        {
            $reason = "FinancialInstitutionBranch ID is not 2";
            return false;
        }
        return true;
    }
}