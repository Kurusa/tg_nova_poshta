<?php


namespace App\Commands;


use App\Models\Record;
use App\Services\RecordStatusService;

class RecordFinish extends BaseCommand
{

    function processCommand($par = false)
    {
        Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
            'status' => RecordStatusService::DONE
        ]);
        $this->triggerCommand(MainMenu::class);
    }
}