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

use EdituraEDU\UBLRenderer\UBLObjectDefinitions\PaymentMeansCode;
use Exception;

class MappingsManager
{
    private static MappingsManager|null $Instance = null;
    public static bool $Initialized = false;
    public static function Init(?string $json=null): void
    {
        self::$Instance = new MappingsManager($json);
        self::$Initialized = true;
    }

    public static function GetInstance(): MappingsManager
    {
        if (self::$Instance === null)
        {
            throw(new Exception(("MappingsManager not initialized, call Init!")));
        }
        return self::$Instance;
    }
    private array $UnitCodes=[];
    private array $AllowanceChargeReasonCodes=[];
    private array $PaymentMeansCodes=[];

    private function __construct(?string $json)
    {
        if($json==null)
        {
            $json = file_get_contents(dirname(__FILE__) . "/../defaultMappings.json");
        }
        $mappings = json_decode($json, true);
        if(isset($mappings["UnitCodes"]))
        {
            $this->UnitCodes = $mappings["UnitCodes"];
        }
        if(isset($mappings["AllowanceChargeReasonCodes"]))
        {
            $this->AllowanceChargeReasonCodes = $mappings["AllowanceChargeReasonCodes"];
        }
        if(isset($mappings["PaymentMeansCodes"]))
        {
            $this->PaymentMeansCodes = $mappings["PaymentMeansCodes"];
        }
    }

    public function AllowanceChargeReasonCodeHasMapping(string $reasonCode): bool
    {
        return isset($this->AllowanceChargeReasonCodes[$reasonCode]);
    }

    public function GetAllowanceChargeReasonCodeMapping(string $reasonCode): string
    {
        if(!$this->AllowanceChargeReasonCodeHasMapping($reasonCode))
        {
            throw new Exception("Reason code $reasonCode has no mapping");
        }
        return $this->AllowanceChargeReasonCodes[$reasonCode];
    }

    public function UnitCodeHasMapping(string $unitCode): bool
    {
        return isset($this->UnitCodes[$unitCode]);
    }

    public function UnitCodeHasShortMapping(string $unitCode, int $maxLen=4): bool
    {
        if(!$this->UnitCodeHasMapping($unitCode))
        {
            throw new Exception("Unit code $unitCode has no mapping");
        }
        return strlen($this->UnitCodes[$unitCode]) <= $maxLen;
    }

    public function PaymentMeansCodeHasMapping(string $paymentMeansCode): bool
    {
        return isset($this->PaymentMeansCodes[$paymentMeansCode]);
    }

    public function GetPaymentMeansCodeMapping(string $paymentMeansCode): string
    {
        if(!$this->PaymentMeansCodeHasMapping($paymentMeansCode))
        {
            throw new Exception("Payment means code $paymentMeansCode has no mapping");
        }
        return $this->PaymentMeansCodes[$paymentMeansCode];
    }

    public function GetUnitCodeMapping(string $unitCode): string
    {
        if(!$this->UnitCodeHasMapping($unitCode))
        {
            throw new Exception("Unit code $unitCode has no mapping");
        }
        return $this->UnitCodes[$unitCode];
    }

    public function AddUnitCodeMapping(string $unitCode, string $unitName): void
    {
        $this->UnitCodes[$unitCode] = $unitName;
    }

    public function AddAllowanceChargeReasonCodeMapping(string $reasonCode, string $reasonName): void
    {
        $this->AllowanceChargeReasonCodes[$reasonCode] = $reasonName;
    }

    public function AddPaymentMeansCodeMapping(string $paymentMeansCode, string $paymentMeansName): void
    {
        $this->PaymentMeansCodes[$paymentMeansCode] = $paymentMeansName;
    }
}