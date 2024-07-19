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

class Country extends UBLDeserializable
{
    public string $IdentificationCode;
    private ?string $ListID = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $parsedCountry = $reader->parseInnerTree();
        if (!is_array($parsedCountry))
        {
            return $instance;
        }
        for ($i = 0; $i < sizeof($parsedCountry); $i++)
        {
            $parsed = $parsedCountry[$i];
            if ($parsed["value"] == null)
            {
                continue;
            }
            $localName = $instance->getLocalName($parsed["name"]);
            switch ($localName)
            {
                case "IdentificationCode":
                    $instance->IdentificationCode = $parsed["value"];
                    if(isset($parsed["attributes"]["listID"]))
                    {
                        $instance->ListID = $parsed["attributes"]["listID"];
                    }
                    break;
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "Country";
    }

    public static function GetTestXML(): string
    {
        //todo check for listId?
        return '<cac:Country ' . self::NS_DEFINTIONS . '>
                    <cbc:IdentificationCode>RO</cbc:IdentificationCode>
                </cac:Country>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof Country))
        {
            $reason = "Instance is not Country";
            return false;
        }
        if ($instance->IdentificationCode !== "RO")
        {
            $reason = "IdentificationCode is not RO";
            return false;
        }
        return true;
    }

    public function CanRender(): true|array
    {
        return true;
    }
}