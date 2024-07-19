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
        $parsedContact = $reader->parseInnerTree();
        if(!is_array($parsedContact))
        {
            return $instance;
        }
        for($i=0;$i<count($parsedContact);$i++)
        {
            $node = $parsedContact[$i];
            if($node["value"] == null)
            {
                continue;
            }
            $localName=$instance->getLocalName($node["name"]);
            switch ($localName)
            {
                case "Name":
                    $instance->Name = $node["value"];
                    break;
                case "Telephone":
                    $instance->Telephone = $node["value"];
                    break;
                case "Telefax":
                    $instance->Telefax = $node["value"];
                    break;
                case "ElectronicMail":
                    $instance->ElectronicMail = $node["value"];
                    break;
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

    public function CanRender(): true|array
    {
        return true;
    }
}