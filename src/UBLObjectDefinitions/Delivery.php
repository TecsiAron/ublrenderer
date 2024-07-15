<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use DateTime;
use Sabre\Xml\Reader;

class Delivery extends UBLDeserializable
{
    public ?DateTime $actualDeliveryDate = null;
    private ?Address $deliveryLocation = null;
    public  ?string $deliveryLocationID = null;
    private ?string $deliveryPartyName = null;

    public static function XMLDeserialize(Reader $reader): UBLDeserializable
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != \XMLReader::END_ELEMENT || $reader->depth > $depth) {
            if ($reader->nodeType == \XMLReader::ELEMENT) {
                switch ($reader->localName) {
                    case "ActualDeliveryDate":
                        $instance->actualDeliveryDate = DateTime::createFromFormat("Y-m-d", $reader->readString());
                        $reader->next();
                        break;
                    case "DeliveryLocation":
                        $parsed= $reader->parseCurrentElement();
                        $instance->deliveryLocationID = $parsed["value"][0]["value"];
                        $instance->deliveryLocation = $parsed["value"][1]["value"];
                        $reader->next();
                        break;
                    case "DeliveryParty":
                        $parsed= $reader->parseCurrentElement();
                        $instance->deliveryPartyName = $parsed["value"][0]["value"][0]["value"];
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
        return self::CAC_SCHEMA."Delivery";
    }

    public static function GetTestXML(): string
    {
        return '<cac:Delivery '.self::NS_DEFINTIONS.'>
                    <cbc:ActualDeliveryDate>2021-01-01</cbc:ActualDeliveryDate>
                    <cac:DeliveryLocation>
                        <cbc:ID>1</cbc:ID>
                        '.Address::GetTestXML().'
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
        if($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if(!($instance instanceof Delivery))
        {
            $reason = "Instance is not Delivery";
            return false;
        }
        if($instance->actualDeliveryDate->format("Y-m-d") !== "2021-01-01")
        {
            $reason = "ActualDeliveryDate is not 2021-01-01";
            return false;
        }
        if(Address::TestDefaultValues($instance->deliveryLocation, $reason) === false)
        {
            $reason = "Failed to parse DeliveryLocation";
            return false;
        }
        if($instance->deliveryPartyName !== "Test")
        {
            $reason = "Failed to parse DeliveryPartyName";
            return false;
        }
        if($instance->deliveryLocationID !== "1")
        {
            $reason = "Failed to parse DeliveryLocationID";
            return false;
        }
        return true;
    }
}