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

class Contact extends UBLDeserializable
{
    public ?string $Name = null;
    public ?string $Telephone = null;
    public ?string $Telefax = null;
    public ?string $ElectronicMail = null;

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
                    case "Name":
                        $instance->Name = $reader->readString();
                        $reader->next();
                        break;
                    case "Telephone":
                        $instance->Telephone = $reader->readString();
                        $reader->next();
                        break;
                    case "Telefax":
                        $instance->Telefax = $reader->readString();
                        $reader->next();
                        break;
                    case "ElectronicMail":
                        $instance->ElectronicMail = $reader->readString();
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
        return self::CAC_SCHEMA . "Contact";
    }

    public static function GetTestXML(): string
    {
        return '<cac:Contact ' . self::NS_DEFINTIONS . '>
                    <cbc:Name>John Doe</cbc:Name>
                    <cbc:Telephone>123456789</cbc:Telephone>
                    <cbc:Telefax>987654321</cbc:Telefax>
                    <cbc:ElectronicMail>mymail@mymail.ro</cbc:ElectronicMail>
                </cac:Contact>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof Contact))
        {
            $reason = "Instance is not Contact";
            return false;
        }
        if ($instance->Name !== "John Doe")
        {
            $reason = "Name is not John Doe";
            return false;
        }
        if ($instance->Telephone !== "123456789")
        {
            $reason = "Telephone is not 123456789";
            return false;
        }
        if ($instance->Telefax !== "987654321")
        {
            $reason = "Telefax is not 987654321";
            return false;
        }
        if ($instance->ElectronicMail !== "mymail@mymail.ro")
        {
            $reason = "ElectronicMail is not mymail@mymail.ro";
            return false;
        }
        return true;
    }
}