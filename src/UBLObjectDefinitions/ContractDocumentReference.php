<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class ContractDocumentReference extends UBLDeserializable
{
    public ?string $id = null;

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
                    case "ID":
                        $instance->id = $reader->readString();
                        $reader->next(); // Move past the current text node
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
        return self::CAC_SCHEMA . "ContractDocumentReference";
    }

    public static function GetTestXML(): string
    {
        return '<cac:ContractDocumentReference ' . self::NS_DEFINTIONS . '>
                    <cbc:ID>10</cbc:ID>
        </cac:ContractDocumentReference>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof ContractDocumentReference))
        {
            $reason = "Instance is not ContractDocumentReference";
            return false;
        }
        if ($instance->id !== "10")
        {
            $reason = "ID is not 10";
            return false;
        }
        return true;
    }
}