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

class AttachedFile extends UBLDeserializable
{
    public ?string $FilePath = null;
    public ?string $ExternalReference = null;
    public ?string $ExternalReferenceMimeType = null;


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
                    case "FilePath":
                        $instance->FilePath = $reader->readString();
                        $reader->next();
                        break;
                    case "ExternalReference":
                        $parsed = $reader->parseCurrentElement();
                        $instance->ExternalReference = $parsed["value"];
                        if (isset($parsed["attributes"]["mimeCode"]))
                        {
                            $instance->ExternalReferenceMimeType = $parsed["attributes"]["mimeCode"];
                        }
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
        return self::CBC_SCHEMA . "Attachment";
    }

    public static function GetTestXML(): string
    {
        return '<cbc:Attachment ' . self::NS_DEFINTIONS . '>
                    <cbc:FilePath>file.txt</cbc:FilePath>
                    <cbc:ExternalReference>http://example.com/file.txt</cbc:ExternalReference>
                </cbc:Attachment>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof AttachedFile))
        {
            $reason = "Instance is not of type AttachedFile";
            return false;
        }
        if ($instance->FilePath !== "file.txt")
        {
            $reason = "FilePath is not 'file.txt'";
            return false;
        }
        if ($instance->ExternalReference !== "http://example.com/file.txt")
        {
            $reason = "ExternalReference is not 'http://example.com/file.txt'";
            return false;
        }
        return true;
    }
}