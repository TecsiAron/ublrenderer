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
    public ?PaymentMeansCode $paymentMeansCode = null;
    public ?string $paymentMeansCodeName = null;
    public ?DateTime $paymentDueDate = null;
    public ?string $instructionId = null;
    public ?string $instructionNote = null;
    public ?string $paymentId = null;
    public ?PayeeFinancialAccount $payeeFinancialAccount;

    public static function XMLDeserialize(Reader $reader): self
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
                    case "PaymentMeansCode":
                        $parsed = $reader->parseCurrentElement();
                        $instance->paymentMeansCode = PaymentMeansCode::tryFrom(strtolower($parsed["value"])) ?? PaymentMeansCode::MutuallyDefined;
                        if (isset($parsed["attributes"]["name"]))
                        {
                            $instance->paymentMeansCodeName = $parsed["attributes"]["name"];
                        }
                        break;
                    case "PaymentDueDate":
                        $instance->paymentDueDate = DateTime::createFromFormat("Y-m-d", $reader->readString());
                        $reader->next();
                        break;
                    case "InstructionID":
                        $instance->instructionId = $reader->readString();
                        $reader->next();
                        break;
                    case "InstructionNote":
                        $instance->instructionNote = $reader->readString();
                        $reader->next();
                        break;
                    case "PaymentID":
                        $instance->paymentId = $reader->readString();
                        $reader->next();
                        break;
                    case "PayeeFinancialAccount":
                        $instance->payeeFinancialAccount = $reader->parseCurrentElement()["value"];
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
        if ($instance->paymentMeansCode != PaymentMeansCode::MutuallyDefined)
        {
            $reason = "PaymentMeansCode is not MutuallyDefined";
            return false;
        }
        if ($instance->paymentMeansCodeName !== "test")
        {
            $reason = "PaymentMeansCodeName is not test";
            return false;
        }
        if ($instance->paymentDueDate == null)
        {
            $reason = "PaymentDueDate is null";
            return false;
        }
        if ($instance->paymentDueDate->format("Y-m-d") !== "2021-01-01")
        {
            $reason = "PaymentDueDate is not 2021-01-01";
            return false;
        }
        if ($instance->instructionId !== "1")
        {
            $reason = "InstructionId is not 1";
            return false;
        }
        if ($instance->instructionNote !== "Payment due in 30 days")
        {
            $reason = "InstructionNote is not Payment due in 30 days";
            return false;
        }
        if ($instance->paymentId !== "1")
        {
            $reason = "PaymentId is not 1";
            return false;
        }
        if (!PayeeFinancialAccount::TestDefaultValues($instance->payeeFinancialAccount, $reason))
        {
            return false;
        }
        return true;
    }
}