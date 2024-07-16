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
    public ?DateTime $actualDeliveryDate = null;
    private ?Address $deliveryLocation = null;
    public ?string $deliveryLocationID = null;
    private ?string $deliveryPartyName = null;

    public static function XMLDeserialize(Reader $reader): UBLDeserializable
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
                    case "ActualDeliveryDate":
                        $instance->actualDeliveryDate = DateTime::createFromFormat("Y-m-d", $reader->readString());
                        $reader->next();
                        break;
                    case "DeliveryLocation":
                        $parsed = $reader->parseCurrentElement();
                        $instance->deliveryLocationID = $parsed["value"][0]["value"];
                        $instance->deliveryLocation = $parsed["value"][1]["value"];
                        $reader->next();
                        break;
                    case "DeliveryParty":
                        $parsed = $reader->parseCurrentElement();
                        $instance->deliveryPartyName = $parsed["value"][0]["value"][0]["value"];
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
        return self::CAC_SCHEMA . "Delivery";
    }

    public static function GetTestXML(): string
    {
        return '<cac:Delivery ' . self::NS_DEFINTIONS . '>
                    <cbc:ActualDeliveryDate>2021-01-01</cbc:ActualDeliveryDate>
                    <cac:DeliveryLocation>
                        <cbc:ID>1</cbc:ID>
                        ' . Address::GetTestXML() . '
                    </cac:DeliveryLocation>
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
        if ($instance->actualDeliveryDate->format("Y-m-d") !== "2021-01-01")
        {
            $reason = "ActualDeliveryDate is not 2021-01-01";
            return false;
        }
        if (Address::TestDefaultValues($instance->deliveryLocation, $reason) === false)
        {
            $reason = "Failed to parse DeliveryLocation";
            return false;
        }
        if ($instance->deliveryPartyName !== "Test")
        {
            $reason = "Failed to parse DeliveryPartyName";
            return false;
        }
        if ($instance->deliveryLocationID !== "1")
        {
            $reason = "Failed to parse DeliveryLocationID";
            return false;
        }
        return true;
    }
}