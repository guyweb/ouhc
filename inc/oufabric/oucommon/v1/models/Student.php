<?php

namespace OUFabric\OUCommon\Models;

use OUFabric\OUCommon\URICache;
use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;

class Student extends User {

    const STATUS_TYPE_ACCESS                = 'access';
    const STATUS_TYPE_OPENINGS              = 'openings';
    const STATUS_TYPE_RESERVED              = 'reserved';
    const STATUS_TYPE_RESERVED_MOD          = 'reserved_mod';
    const STATUS_TYPE_REGISTERED            = 'registered';
    
    const LEVEL_UNDERGRADUATE               = 'undergraduate';
    const LEVEL_POSTGRADUATE                = 'postgraduate';
    
    const FRAMEWORK_TYPE_M                  = 'M';
    const FRAMEWORK_TYPE_Q0                 = 'Q0';
    const FRAMEWORK_TYPE_Q1                 = 'Q1';
    
    const PRICINGCODE_ENGLAND               = 'EN';
    const PRICINGCODE_SCOTLAND              = 'SC';
    const PRICINGCODE_WALES                 = 'WA';
    const PRICINGCODE_WALES_NEW             = 'WN';
    const PRICINGCODE_NORTHERNIRELAND       = 'NI';
    const PRICINGCODE_IRELAND               = 'IE';
    const PRICINGCODE_EUROPEANUNION         = 'EU';
    const PRICINGCODE_OTHEROVERSEAS         = 'OE';
    const PRICINGCODE_ENGLAND_TRANS         = 'ET';
    const PRICINGCODE_WALES_TRANS           = 'WT';
    const PRICINGCODE_IRELAND_TRANS         = 'IT';
    const PRICINGCODE_EUROPEANUNION_TRANS   = 'UT';
    const PRICINGCODE_OTHEROVERSEAS_TRANS   = 'OT';
    const PRICINGCODE_ENGLAND_F1617         = 'EB';
    const PRICINGCODE_IRELAND_F1617         = 'IB';
    const PRICINGCODE_EUROPEANUNION_F1617   = 'UB';
    const PRICINGCODE_OTHEROVERSEAS_F1617   = 'OB';

    const JOURNEY_APPLICANT                 = 'applicant';
    const JOURNEY_INDUCTION                 = 'induction';
    const JOURNEY_ON_MODULE                 = 'onModule';
    const JOURNEY_BETWEEN_MODULES           = 'betweenModules';
    const JOURNEY_UNKNOWN                   = 'unknown';

    public $framework;
    public $level;
    public $status;
    public $pricingCode;
    public $journey;
    public $qualifications = [];
    public $modules = [];

}

// EOF