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

class AdditionalDocumentReference extends UBLDeserializable
{
    public ?string $ID = null;
    public ?string $DocumentType = null;
    public ?string $DocumentTypeCode = null;
    public ?string $DocumentDescription = null;
    public AttachedFile|array|null $Attachment = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $parsedAdditionalDocumentReference = $reader->parseInnerTree();
        if (!is_array($parsedAdditionalDocumentReference))
        {
            return $instance;
        }
        for ($i = 0; $i < count($parsedAdditionalDocumentReference); $i++)
        {
            $parsed = $parsedAdditionalDocumentReference[$i];
            if ($parsed["value"] === null)
            {
                continue;
            }
            $localName = $instance->getLocalName($parsed["name"]);
            switch ($localName)
            {
                case "ID":
                    $instance->ID = $parsed["value"];
                    break;
                case "DocumentType":
                    $instance->DocumentType = $parsed["value"];
                    break;
                case "DocumentTypeCode":
                    $instance->DocumentTypeCode = $parsed["value"];
                    break;
                case "DocumentDescription":
                    $instance->DocumentDescription = $parsed["value"];
                    break;
                case "Attachment":
                    $instance->Attachment = $parsed["value"];
                    break;
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "AdditionalDocumentReference";
    }

    public static function GetTestXML(): string
    {
        return '<cac:AdditionalDocumentReference ' . self::NS_DEFINTIONS . '>
                    <cbc:ID>1</cbc:ID>
                    <cbc:DocumentType>Invoice</cbc:DocumentType>
                    <cbc:DocumentTypeCode>380</cbc:DocumentTypeCode>
                    <cbc:DocumentDescription>Invoice</cbc:DocumentDescription>
                    ' . AttachedFile::GetTestXML() . '
                </cac:AdditionalDocumentReference>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof AdditionalDocumentReference))
        {
            $reason = "Instance is not of type AdditionalDocumentReference";
            return false;
        }
        if ($instance->ID != "1")
        {
            $reason = "ID is not 1";
            return false;
        }
        if ($instance->DocumentType != "Invoice")
        {
            $reason = "DocumentType is not Invoice";
            return false;
        }
        if ($instance->DocumentTypeCode != "380")
        {
            $reason = "DocumentTypeCode is not 380";
            return false;
        }
        if ($instance->DocumentDescription != "Invoice")
        {
            $reason = "DocumentDescription is not Invoice";
            return false;
        }
        if (!AttachedFile::TestDefaultValues($instance->Attachment, $reason))
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