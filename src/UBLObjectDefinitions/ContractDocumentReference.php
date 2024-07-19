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

class ContractDocumentReference extends UBLDeserializable
{
    public ?string $ID = null;

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
                        $instance->ID = $reader->readString();
                        //$reader->next(); // Move past the current text node
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
        if ($instance->ID !== "10")
        {
            $reason = "ID is not 10";
            return false;
        }
        return true;
    }
    public function CanRender(): true|array
    {
        return true;
    }
}