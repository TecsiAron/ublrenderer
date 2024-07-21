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

use EdituraEDU\UBLRenderer\UBLObjectDefinitions\UBLDeserializable;
use Exception;

/**
 * Class UBLRenderException
 * Special exception class to be thrown when UBLDeserializable CanRender returns false
 * @see UBLDeserializable::CanRender
 * @package EdituraEDU\UBLRenderer
 */
class UBLRenderException extends Exception
{
    public array $Reasons;

    public function __construct(string $message, array $reasons)
    {
        parent::__construct($message, 0, null);
        $this->Reasons = $reasons;
    }
}