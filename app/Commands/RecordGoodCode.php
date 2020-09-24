<?php

namespace App\Commands;

use App\Models\Good;
use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordGoodCode extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::GOOD_CODE) {
            $record = Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->first();
            Good::create([
                'record_id' => $record->id,
                'code' => $this->parser::getMessage()
            ]);
            $this->triggerCommand(RecordGoodTitle::class);
        } else {
            $this->user->status = UserStatusService::GOOD_CODE;
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['good_code'], [
                [$this->text['cancel']]
            ]);
        }
    }
}