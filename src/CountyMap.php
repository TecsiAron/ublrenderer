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

namespace EdituraEDU\UBLRenderer;

class CountyMap
{
    private const MAP = [
        "RO-AB" => "Alba",
        "RO-AR" => "Arad",
        "RO-AG" => "Argeș",
        "RO-BC" => "Bacău",
        "RO-BH" => "Bihor",
        "RO-BN" => "Bistrița-Năsăud",
        "RO-BT" => "Botoșani",
        "RO-BR" => "Brăila",
        "RO-BV" => "Brașov",
        "RO-BZ" => "Buzău",
        "RO-CS" => "Caraș-Severin",
        "RO-CL" => "Călărași",
        "RO-CJ" => "Cluj",
        "RO-CT" => "Constanța",
        "RO-CV" => "Covasna",
        "RO-DB" => "Dâmbovița",
        "RO-DJ" => "Dolj",
        "RO-GL" => "Galați",
        "RO-GR" => "Giurgiu",
        "RO-GJ" => "Gorj",
        "RO-HR" => "Harghita",
        "RO-HD" => "Hunedoara",
        "RO-IL" => "Ialomița",
        "RO-IS" => "Iași",
        "RO-IF" => "Ilfov",
        "RO-MM" => "Maramureș",
        "RO-MH" => "Mehedinți",
        "RO-MS" => "Mureș",
        "RO-NT" => "Neamț",
        "RO-OT" => "Olt",
        "RO-PH" => "Prahova",
        "RO-SJ" => "Sălaj",
        "RO-SM" => "Satu Mare",
        "RO-SB" => "Sibiu",
        "RO-SV" => "Suceava",
        "RO-TR" => "Teleorman",
        "RO-TM" => "Timiș",
        "RO-TL" => "Tulcea",
        "RO-VL" => "Vâlcea",
        "RO-VS" => "Vaslui",
        "RO-VN" => "Vrancea",
        "RO-B" => "București"
    ];

    public static function GetCounty(string $code): ?string
    {
        $code=trim($code);
        if (isset(self::MAP[$code]))
        {
            return self::MAP[$code];
        }
        return null;
    }
}
