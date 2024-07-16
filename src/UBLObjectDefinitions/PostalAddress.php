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

class PostalAddress extends Address
{

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "PostalAddress";
    }

    public static function XMLDeserialize(Reader $reader, ?Address $instance = null): PostalAddress
    {
        if ($instance != null)
        {
            throw new Exception("PostalAddress cannot be deserialized into an existing instance.");
        }
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Address::XMLDeserialize($reader, new PostalAddress());

    }

    public static function GetTestXML(): string
    {
        return '<cac:PostalAddress ' . self::NS_DEFINTIONS . '>
                    <cbc:StreetName>Strada</cbc:StreetName>
                    <cbc:AdditionalStreetName>Strada2</cbc:AdditionalStreetName>
                    <cbc:BuildingNumber>1</cbc:BuildingNumber>
                    <cbc:CityName>Oras</cbc:CityName>
                    <cbc:PostalZone>123456</cbc:PostalZone>
                    <cbc:CountrySubentity>Judet</cbc:CountrySubentity>
                    <cac:Country>
                        <cbc:IdentificationCode>RO</cbc:IdentificationCode>
                    </cac:Country>
                </cac:PostalAddress>';
    }

}