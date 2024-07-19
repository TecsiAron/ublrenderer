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

use DateTime;
use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class Delivery extends UBLDeserializable
{
    public ?DateTime $ActualDeliveryDate = null;
    public DeliveryLocation $DeliveryLocation;
    private ?string $DeliveryPartyName = null;

    public static function XMLDeserialize(Reader $reader): UBLDeserializable
    {
        $instance = new self();
        $parsedDelivery = $reader->parseInnerTree();
        if (!is_array($parsedDelivery))
        {
            return $instance;
        }
        for ($i = 0; $i < count($parsedDelivery); $i++)
        {
            $parsed = $parsedDelivery[$i];
            $localName = $instance->getLocalName($parsed["name"]);
            switch ($localName)
            {
                case "ActualDeliveryDate":
                    $instance->ActualDeliveryDate = DateTime::createFromFormat("Y-m-d", $parsed["value"]);
                    break;
                case "DeliveryLocation":
                    $instance->DeliveryLocation = $parsed["value"];
                    break;
                case "DeliveryParty":
                    $instance->DeliveryPartyName = $parsed["value"][0]["value"][0]["value"];
                    break;
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "Delivery";
    }

    public static function GetTestXML(): string
    {
        return '<cac:Delivery ' . self::NS_DEFINTIONS . '>
                    <cbc:ActualDeliveryDate>2021-01-01</cbc:ActualDeliveryDate>
                    '.DeliveryLocation::GetTestXML().'
                    <cac:DeliveryParty>
                        <cac:PartyName>
                            <cbc:Name>Test</cbc:Name>
                        </cac:PartyName>
                    </cac:DeliveryParty>
                </cac:Delivery>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof Delivery))
        {
            $reason = "Instance is not Delivery";
            return false;
        }
        if ($instance->ActualDeliveryDate->format("Y-m-d") !== "2021-01-01")
        {
            $reason = "ActualDeliveryDate is not 2021-01-01";
            return false;
        }
        if ($instance->DeliveryPartyName !== "Test")
        {
            $reason = "Failed to parse DeliveryPartyName";
            return false;
        }
        if (!DeliveryLocation::TestDefaultValues($instance->DeliveryLocation, $reason))
        {
            return false;
        }
        return true;
    }

    public function CanRender(): true|array
    {
        return true;
    }
}