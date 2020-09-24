<?php

namespace App\Commands;

use App\Models\Good;
use App\Models\Record;
use App\Services\GoodStatusService;
use App\Services\RecordStatusService;

class RecordGoodUnit extends BaseCommand
{

    function processCommand($par = false)
    {
        $record = Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->first();
        Good::where('record_id', $record->id)->where('status', GoodStatusService::FILLING)->update([
            'unit' => 'шт.'
        ]);
        $this->tg->sendMessageWithKeyboard($this->text['done'], [
            [$this->text['finish']], [$this->text['add_one_more']]
        ]);
    }
}