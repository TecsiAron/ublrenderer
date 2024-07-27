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

/**
 * Class MappingsManager
 * Manages mappings for various codes used in UBL invoices
 * By default reads the mappings from defaultMappings.json (in the root of this project)
 * Uses singleton pattern with an explicit need to call Init
 * @package EdituraEDU\UBLRenderer
 */
class MappingsManager
{
    private static MappingsManager|null $Instance = null;
    public static bool $Initialized = false;

    /**
     * Initializes the MappingsManager singleton
     * @param string|null $json if null the defaultMappings.json file will be used
     * @return void
     */
    public static function Init(?string $json = null): void
    {
        self::$Instance = new MappingsManager($json);
        self::$Initialized = true;
    }

    /**
     * @return MappingsManager
     * @throws Exception if the instance is not initialized
     */
    public static function GetInstance(): MappingsManager
    {
        if (self::$Instance === null)
        {
            throw(new Exception(("MappingsManager not initialized, call Init!")));
        }
        return self::$Instance;
    }

    /**
     * @var string[] $UnitCodes maps unit codes to their names
     */
    private array $UnitCodes = [];
    /**
     * @var string[] $AllowanceChargeReasonCodes maps allowance charge reason codes to their names
     */
    private array $AllowanceChargeReasonCodes = [];
    /**
     * @var string[] $PaymentMeansCodes maps payment means codes to their names
     */
    private array $PaymentMeansCodes = [];

    /**
     * Reads and populates the mappings from the json file
     * If no file is provided, the defaultMappings.json file will be used
     * @param string|null $json
     */
    private function __construct(?string $json)
    {
        if ($json == null)
        {
            $json = file_get_contents(dirname(__FILE__) . "/../defaultMappings.json");
        }
        $mappings = json_decode($json, true);
        if (isset($mappings["UnitCodes"]))
        {
            $this->UnitCodes = $mappings["UnitCodes"];
        }
        if (isset($mappings["AllowanceChargeReasonCodes"]))
        {
            $this->AllowanceChargeReasonCodes = $mappings["AllowanceChargeReasonCodes"];
        }
        if (isset($mappings["PaymentMeansCodes"]))
        {
            $this->PaymentMeansCodes = $mappings["PaymentMeansCodes"];
        }
    }

    /**
     * Checks if a reason code has a mapping
     * @param string $reasonCode
     * @return bool
     */
    public function AllowanceChargeReasonCodeHasMapping(string $reasonCode): bool
    {
        return isset($this->AllowanceChargeReasonCodes[$reasonCode]);
    }

    /**
     * @param string $reasonCode
     * @return string
     * @throws Exception if the reason code has no mapping
     */
    public function GetAllowanceChargeReasonCodeMapping(string $reasonCode): string
    {
        if (!$this->AllowanceChargeReasonCodeHasMapping($reasonCode))
        {
            throw new Exception("Reason code $reasonCode has no mapping");
        }
        return $this->AllowanceChargeReasonCodes[$reasonCode];
    }

    /**
     * Checks if a unit code has a mapping
     * @param string $unitCode
     * @return bool
     */
    public function UnitCodeHasMapping(string $unitCode): bool
    {
        return isset($this->UnitCodes[$unitCode]);
    }

    /**
     * Checks if the given unit code has a short mapping (<=4 characters)
     * @param string $unitCode
     * @param int $maxLen
     * @return bool
     * @throws Exception if the unit code has no mapping
     */
    public function UnitCodeHasShortMapping(string $unitCode, int $maxLen = 4): bool
    {
        if (!$this->UnitCodeHasMapping($unitCode))
        {
            throw new Exception("Unit code $unitCode has no mapping");
        }
        return strlen($this->UnitCodes[$unitCode]) <= $maxLen;
    }

    /**
     * @param string $paymentMeansCode
     * @return bool
     */
    public function PaymentMeansCodeHasMapping(string $paymentMeansCode): bool
    {
        return isset($this->PaymentMeansCodes[$paymentMeansCode]);
    }

    /**
     * @param string $paymentMeansCode
     * @return string
     * @throws Exception if the payment means code has no mapping
     */
    public function GetPaymentMeansCodeMapping(string $paymentMeansCode): string
    {
        if (!$this->PaymentMeansCodeHasMapping($paymentMeansCode))
        {
            throw new Exception("Payment means code $paymentMeansCode has no mapping");
        }
        return $this->PaymentMeansCodes[$paymentMeansCode];
    }

    /**
     * @param string $unitCode
     * @return string
     * @throws Exception if the unit code has no mapping
     */
    public function GetUnitCodeMapping(string $unitCode): string
    {
        if (!$this->UnitCodeHasMapping($unitCode))
        {
            throw new Exception("Unit code $unitCode has no mapping");
        }
        return $this->UnitCodes[$unitCode];
    }

    /**
     * Adds a new unit code mapping
     * @param string $unitCode
     * @param string $unitName
     * @return void
     */
    public function AddUnitCodeMapping(string $unitCode, string $unitName): void
    {
        $this->UnitCodes[$unitCode] = $unitName;
    }

    /**
     * Adds a new allowance charge reason code mapping
     * @param string $reasonCode
     * @param string $reasonName
     * @return void
     */
    public function AddAllowanceChargeReasonCodeMapping(string $reasonCode, string $reasonName): void
    {
        $this->AllowanceChargeReasonCodes[$reasonCode] = $reasonName;
    }

    /**
     * Adds a new payment means code mapping
     * @param string $paymentMeansCode
     * @param string $paymentMeansName
     * @return void
     */
    public function AddPaymentMeansCodeMapping(string $paymentMeansCode, string $paymentMeansName): void
    {
        $this->PaymentMeansCodes[$paymentMeansCode] = $paymentMeansName;
    }
}