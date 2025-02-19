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

class PayeeFinancialAccount extends UBLDeserializable
{
    public ?string $ID = null;
    public ?string $Name = null;
    public ?string $FinancialInstitutionBranchID = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $parsedPayeeFinancialAccount = $reader->parseInnerTree();
        if (!is_array($parsedPayeeFinancialAccount))
        {
            return $instance;
        }
        for ($i = 0; $i < sizeof($parsedPayeeFinancialAccount); $i++)
        {
            $parsed = $parsedPayeeFinancialAccount[$i];
            if ($parsed["value"] == null)
            {
                continue;
            }
            $localName = $instance->getLocalName($parsed["name"]);
            switch ($localName)
            {
                case "ID":
                    $instance->ID = $parsed["value"];
                    break;
                case "Name":
                    $instance->Name = $parsed["value"];
                    break;
                case "FinancialInstitutionBranch":
                    $instance->FinancialInstitutionBranchID = $parsed["value"][0]["value"];
                    break;
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "PayeeFinancialAccount";
    }

    public static function GetTestXML(): string
    {
        return '<cac:PayeeFinancialAccount ' . self::NS_DEFINTIONS . '>
                    <cbc:ID>1</cbc:ID>
                    <cbc:Name>John Doe</cbc:Name>
                    <cac:FinancialInstitutionBranch>
                        <cbc:ID>2</cbc:ID>
                    </cac:FinancialInstitutionBranch>
                </cac:PayeeFinancialAccount>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof PayeeFinancialAccount))
        {
            $reason = "Instance is not of type PayeeFinancialAccount";
            return false;
        }
        if ($instance->ID !== "1")
        {
            $reason = "ID is not 1";
            return false;
        }
        if ($instance->Name !== "John Doe")
        {
            $reason = "Name is not John Doe";
            return false;
        }
        if ($instance->FinancialInstitutionBranchID !== "2")
        {
            $reason = "FinancialInstitutionBranch ID is not 2";
            return false;
        }
        return true;
    }

    public function CanRender(): true|array
    {
        return true;
    }
}