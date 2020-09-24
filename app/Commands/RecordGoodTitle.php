<?php

namespace App\Commands;

use App\Models\Good;
use App\Models\Record;
use App\Services\GoodStatusService;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordGoodTitle extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::GOOD_TITLE) {
            $record = Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->first();
            Good::where('record_id', $record->id)->where('status', GoodStatusService::FILLING)->update([
                'title' => $this->parser::getMessage()
            ]);
            $this->triggerCommand(RecordGoodChar::class);
        } else {
            $this->user->status = UserStatusService::GOOD_TITLE;
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['good_title'], [
                [$this->text['cancel']]
            ]);
        }
    }
}