<?php

namespace EdituraEDU\UBLRenderer;

use Exception;

class MappingsManager
{
    private static MappingsManager|null $Instance = null;

    public static function Init(?string $json=null): void
    {
        self::$Instance = new MappingsManager($json);
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

    public function GetUnitCodeMapping(string $unitCode): string
    {
        if(!$this->UnitCodeHasMapping($unitCode))
        {
            throw new Exception("Unit code $unitCode has no mapping");
        }
        return $this->UnitCodes[$unitCode];
    }
}