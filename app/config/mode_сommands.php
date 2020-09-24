<?php

use App\Services\UserStatusService;

return [
    UserStatusService::DELIVERY_TYPE => \App\Commands\RecordDeliveryType::class,
    UserStatusService::E_DELIVERY_TYPE => \App\Commands\RecordDeliveryType::class,

    UserStatusService::PHONE => \App\Commands\RecordPhone::class,
    UserStatusService::E_PHONE => \App\Commands\RecordPhone::class,

    UserStatusService::NAME => \App\Commands\RecordName::class,
    UserStatusService::E_NAME => \App\Commands\RecordName::class,

    UserStatusService::DELIVERY_STATUS => \App\Commands\RecordPayedStatus::class,
    UserStatusService::E_DELIVERY_STATUS => \App\Commands\RecordPayedStatus::class,

    UserStatusService::HALF_PAY_SUM => \App\Commands\RecordPayedStatus::class,
    UserStatusService::E_HALF_PAY_SUM => \App\Commands\RecordPayedStatus::class,

    UserStatusService::COMMENT => \App\Commands\RecordComment::class,
    UserStatusService::E_COMMENT => \App\Commands\RecordComment::class,

    UserStatusService::DELIVERY_DATE => \App\Commands\RecordDeliveryDate::class,
    UserStatusService::E_DELIVERY_DATE => \App\Commands\RecordDeliveryDate::class,

    UserStatusService::DELIVERY_ADDRESS => \App\Commands\RecordAddress::class,
    UserStatusService::E_DELIVERY_ADDRESS => \App\Commands\RecordAddress::class,

    UserStatusService::DELIVERY_CITY => \App\Commands\RecordDeliveryCity::class,
    UserStatusService::E_DELIVERY_CITY => \App\Commands\RecordDeliveryCity::class,

    UserStatusService::DELIVERY_SETTLEMENT => \App\Commands\RecordDeliverySettlements::class,
    UserStatusService::E_DELIVERY_SETTLEMENT => \App\Commands\RecordDeliverySettlements::class,

    UserStatusService::GOOD_CODE => \App\Commands\RecordGoodCode::class,
    UserStatusService::GOOD_TITLE => \App\Commands\RecordGoodTitle::class,
    UserStatusService::GOOD_COUNT => \App\Commands\RecordGoodCount::class,
];