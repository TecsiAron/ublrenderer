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

class PaymentMeans extends UBLDeserializable
{
    public ?PaymentMeansCode $PaymentMeansCode = null;
    public ?string $PaymentMeansCodeName = null;
    public ?DateTime $PaymentDueDate = null;
    public ?string $InstructionId = null;
    public ?string $InstructionNote = null;
    public ?string $PaymentID = null;
    public ?PayeeFinancialAccount $PayeeFinancialAccount;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $parsedPaymentMeans = $reader->parseInnerTree();
        if (!is_array($parsedPaymentMeans))
        {
            return $instance;
        }
        for ($i = 0; $i < sizeof($parsedPaymentMeans); $i++)
        {
            $parsed = $parsedPaymentMeans[$i];
            if ($parsed["value"] == null)
            {
                continue;
            }
            $localName = $instance->getLocalName($parsed["name"]);
            switch ($localName)
            {
                case "PaymentMeansCode":
                    $instance->PaymentMeansCode = PaymentMeansCode::tryFrom(strtolower($parsed["value"])) ?? PaymentMeansCode::MutuallyDefined;
                    if (isset($parsed["attributes"]["name"]))
                    {
                        $instance->PaymentMeansCodeName = $parsed["attributes"]["name"];
                    }
                    break;
                case "PaymentDueDate":
                    $instance->PaymentDueDate = DateTime::createFromFormat("Y-m-d", $parsed["value"]);
                    break;
                case "InstructionID":
                    $instance->InstructionId = $parsed["value"];
                    break;
                case "InstructionNote":
                    $instance->InstructionNote = $parsed["value"];
                    break;
                case "PaymentID":
                    $instance->PaymentID = $parsed["value"];
                    break;
                case "PayeeFinancialAccount":
                    $instance->PayeeFinancialAccount = $parsed["value"];
                    break;
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "PaymentMeans";
    }

    public static function GetTestXML(): string
    {
        return '<cac:PaymentMeans ' . self::NS_DEFINTIONS . '>
                    <cbc:PaymentMeansCode name="test">zzz</cbc:PaymentMeansCode>
                    <cbc:PaymentDueDate>2021-01-01</cbc:PaymentDueDate>
                    <cbc:InstructionID>1</cbc:InstructionID>
                    <cbc:InstructionNote>Payment due in 30 days</cbc:InstructionNote>
                    <cbc:PaymentID>1</cbc:PaymentID>
                    ' . PayeeFinancialAccount::GetTestXML() . '
                </cac:PaymentMeans>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof PaymentMeans))
        {
            $reason = "Instance is not of type PaymentMeans";
            return false;
        }
        if ($instance->PaymentMeansCode != PaymentMeansCode::MutuallyDefined)
        {
            $reason = "PaymentMeansCode is not MutuallyDefined";
            return false;
        }
        if ($instance->PaymentMeansCodeName !== "test")
        {
            $reason = "PaymentMeansCodeName is not test";
            return false;
        }
        if ($instance->PaymentDueDate == null)
        {
            $reason = "PaymentDueDate is null";
            return false;
        }
        if ($instance->PaymentDueDate->format("Y-m-d") !== "2021-01-01")
        {
            $reason = "PaymentDueDate is not 2021-01-01";
            return false;
        }
        if ($instance->InstructionId !== "1")
        {
            $reason = "InstructionId is not 1";
            return false;
        }
        if ($instance->InstructionNote !== "Payment due in 30 days")
        {
            $reason = "InstructionNote is not Payment due in 30 days";
            return false;
        }
        if ($instance->PaymentID !== "1")
        {
            $reason = "PaymentId is not 1";
            return false;
        }
        if (!PayeeFinancialAccount::TestDefaultValues($instance->PayeeFinancialAccount, $reason))
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