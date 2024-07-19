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

class SettlementPeriod extends UBLDeserializable
{
    public ?DateTime $StartDate = null;
    public ?DateTime $EndDate = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $parsedSettlementPeriod = $reader->parseInnerTree();
        if (!is_array($parsedSettlementPeriod))
        {
            return $instance;
        }
        for ($i = 0; $i < sizeof($parsedSettlementPeriod); $i++)
        {
            $parsed = $parsedSettlementPeriod[$i];
            if ($parsed["value"] == null)
            {
                continue;
            }
            $localName = $instance->getLocalName($parsed["name"]);
            switch ($localName)
            {
                case "StartDate":
                    $instance->StartDate = DateTime::createFromFormat("Y-m-d", $parsed["value"]);
                    break;
                case "EndDate":
                    $instance->EndDate = DateTime::createFromFormat("Y-m-d", $parsed["value"]);
                    break;
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "SettlementPeriod";
    }

    public static function GetTestXML(): string
    {
        return '<cac:SettlementPeriod ' . self::NS_DEFINTIONS . '>
                    <cbc:StartDate>2021-01-01</cbc:StartDate>
                    <cbc:EndDate>2021-01-01</cbc:EndDate>
                </cac:SettlementPeriod>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }

        if (!($instance instanceof SettlementPeriod))
        {
            $reason = "Instance is not SettlementPeriod";
            return false;
        }

        if ($instance->StartDate->format("Y-m-d") != "2021-01-01")
        {
            $reason = "Start date is not 2021-01-01";
            return false;
        }

        if ($instance->EndDate->format("Y-m-d") != "2021-01-01")
        {
            $reason = "End date is not 2021-01-01";
            return false;
        }

        return true;

    }
    public function CanRender(): true|array
    {
        return true;
    }
}