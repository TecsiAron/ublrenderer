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

class OrderReference extends UBLDeserializable
{
    public ?string $ID = null;
    public ?string $SalesOrderId = null;
    public ?DateTime $IssueDate = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $parsedOrderReference = $reader->parseInnerTree();
        if (!is_array($parsedOrderReference))
        {
            return $instance;
        }
        for ($i = 0; $i < count($parsedOrderReference); $i++)
        {
            $node = $parsedOrderReference[$i];
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
                case "SalesOrderID":
                    $instance->SalesOrderId = $node["value"];
                    break;
                case "IssueDate":
                    $instance->IssueDate = DateTime::createFromFormat("Y-m-d", $node["value"]);
                    break;
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "OrderReference";
    }

    public static function GetTestXML(): string
    {
        return '<cac:OrderReference ' . self::NS_DEFINTIONS . '>
                    <cbc:ID>1</cbc:ID>
                    <cbc:SalesOrderID>1</cbc:SalesOrderID>
                    <cbc:IssueDate>2021-01-01</cbc:IssueDate>
                </cac:OrderReference>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof OrderReference))
        {
            $reason = "Instance is not of type OrderReference";
            return false;
        }
        if ($instance->ID !== "1")
        {
            $reason = "ID is not 1";
            return false;
        }
        if ($instance->SalesOrderId !== "1")
        {
            $reason = "SalesOrderID is not 1";
            return false;
        }
        if ($instance->IssueDate->format("Y-m-d") != "2021-01-01")
        {
            $reason = "IssueDate is not 2021-01-01";
        }
        return true;
    }

    /**
     * Check if the OrderReference has an ID (ID or SalesOrderID) set to a non-empty value
     * @return bool
     */
    public function HasValidID(): bool
    {
        return (isset($this->ID) && !empty($this->ID)) || (isset($this->SalesOrderId) && !empty($this->SalesOrderId));
    }

    public function CanRender(): true|array
    {
        return true;
    }
}