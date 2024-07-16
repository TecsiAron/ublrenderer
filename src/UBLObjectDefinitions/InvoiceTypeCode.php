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

enum InvoiceTypeCode: string
{
    case INVOICE = "380";
    case CREDIT_NOTE = "381";
    case DEBIT_NOTE = "383";
    case CORRECTED_INVOICE = "384";
    case ADVANCE_INVOICE = "386";
    case SELF_BILLING_INVOICE = "389";

    case INVALID = "FAILED_TO_PARSE";
}