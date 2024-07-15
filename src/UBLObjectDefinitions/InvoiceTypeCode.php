<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

enum InvoiceTypeCode: string
{
    case INVOICE = "380";
    case CREDIT_NOTE = "381";
    case DEBIT_NOTE = "383";
    case CORRECTED_INVOICE = "384";
    case ADVANCE_INVOICE = "386";
    case SELF_BILLING_INVOICE = "389";

    case INVALID="FAILED_TO_PARSE";
}