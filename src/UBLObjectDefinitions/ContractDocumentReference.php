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
        $parsedContractDocumentReference = $reader->parseInnerTree();
        if (!is_array($parsedContractDocumentReference))
        {
            return $instance;
        }
        for ($i = 0; $i < count($parsedContractDocumentReference); $i++)
        {
            $node = $parsedContractDocumentReference[$i];
            if ($node["value"] == null)
            {
                continue;
            }
            $localName = $instance->getLocalName($node["name"]);
            switch ($localName)
            {
                case "ID":
                    $instance->ID = $node["value"];
                    break;
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