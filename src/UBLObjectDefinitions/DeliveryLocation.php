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

use EdituraEDU\UBLRenderer\UBLObjectDefinitions\UBLDeserializable;
use Sabre\Xml\Reader;

class DeliveryLocation extends UBLDeserializable
{

    public ?string $ID = null;
    public ?Address $Address = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $parsedDeliveryLocation = $reader->parseInnerTree();
        if (!is_array($parsedDeliveryLocation))
        {
            return $instance;
        }
        for ($i = 0; $i < count($parsedDeliveryLocation); $i++)
        {
            $node = $parsedDeliveryLocation[$i];
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
                case "Address":
                    $instance->Address = $node["value"];
                    break;
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "DeliveryLocation";
    }

    public function CanRender(): true|array
    {
        return true;
    }

    public static function GetTestXML(): string
    {
        return '<cac:DeliveryLocation ' . self::NS_DEFINTIONS . '>
                        <cbc:ID>1</cbc:ID>
                        ' . Address::GetTestXML() . '
                    </cac:DeliveryLocation>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof DeliveryLocation))
        {
            $reason = "Instance is not DeliveryLocation";
            return false;
        }
        if ($instance->ID != "1")
        {
            $reason = "ID is not 1";
            return false;
        }
        if (!Address::TestDefaultValues($instance->Address, $reason))
        {
            return false;
        }
        return true;
    }
}