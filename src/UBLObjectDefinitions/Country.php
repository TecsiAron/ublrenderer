<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

class Country extends UBLDeserializable
{
    public string $identificationCode;
    private ?string $listId = null;

    public static function XMLDeserialize(\Sabre\Xml\Reader $reader): self
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != \XMLReader::END_ELEMENT || $reader->depth > $depth) {
            if ($reader->nodeType == \XMLReader::ELEMENT) {
                switch ($reader->localName) {
                    case "IdentificationCode":
                        $instance->identificationCode = $reader->readString();
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
        return self::CAC_SCHEMA."Country";
    }

    public static function GetTestXML(): string
    {
        //todo check for listId?
        return '<cac:Country '.self::NS_DEFINTIONS.'>
                    <cbc:IdentificationCode>RO</cbc:IdentificationCode>
                </cac:Country>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if(!($instance instanceof Country))
        {
            $reason = "Instance is not Country";
            return false;
        }
        if($instance->identificationCode !== "RO")
        {
            $reason = "IdentificationCode is not RO";
            return false;
        }
        return true;
    }
}