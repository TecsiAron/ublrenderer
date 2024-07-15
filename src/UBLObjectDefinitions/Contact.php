<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use Sabre\Xml\Reader;

class Contact extends UBLDeserializable
{
    public ?string $name = null;
    public ?string $telephone = null;
    public ?string $telefax = null;
    public ?string $electronicMail = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != \XMLReader::END_ELEMENT || $reader->depth > $depth) {
            if ($reader->nodeType == \XMLReader::ELEMENT) {
                switch ($reader->localName) {
                    case "Name":
                        $instance->name = $reader->readString();
                        $reader->next();
                        break;
                    case "Telephone":
                        $instance->telephone = $reader->readString();
                        $reader->next();
                        break;
                    case "Telefax":
                        $instance->telefax = $reader->readString();
                        $reader->next();
                        break;
                    case "ElectronicMail":
                        $instance->electronicMail = $reader->readString();
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
        return self::CAC_SCHEMA."Contact";
    }

    public static function GetTestXML(): string
    {
        return '<cac:Contact '.self::NS_DEFINTIONS.'>
                    <cbc:Name>John Doe</cbc:Name>
                    <cbc:Telephone>123456789</cbc:Telephone>
                    <cbc:Telefax>987654321</cbc:Telefax>
                    <cbc:ElectronicMail>mymail@mymail.ro</cbc:ElectronicMail>
                </cac:Contact>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if(!($instance instanceof Contact))
        {
            $reason = "Instance is not Contact";
            return false;
        }
        if($instance->name !== "John Doe")
        {
            $reason = "Name is not John Doe";
            return false;
        }
        if($instance->telephone !== "123456789")
        {
            $reason = "Telephone is not 123456789";
            return false;
        }
        if($instance->telefax !== "987654321")
        {
            $reason = "Telefax is not 987654321";
            return false;
        }
        if($instance->electronicMail !== "mymail@mymail.ro")
        {
            $reason = "ElectronicMail is not mymail@mymail.ro";
            return false;
        }
        return true;
    }
}