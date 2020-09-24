<?php

namespace App\Commands;

class RecordManager extends BaseCommand
{

    function processCommand($par = false)
    {
        $this->triggerCommand(RecordDeliveryType::class);
    }
}