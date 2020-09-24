<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordName extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::NAME || $this->user->status == UserStatusService::E_NAME) {
            if ($this->user->status == UserStatusService::NAME) {
                Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                    'fio' => $this->parser::getMessage()
                ]);
                $this->triggerCommand(RecordPayedStatus::class);
            } else {
                Record::where('id', $this->user->edit_id)->update([
                    'fio' => $this->parser::getMessage()
                ]);
                $this->triggerCommand(RecordEdit::class);
            }
        } else {
            if ($this->parser::getByKey('a') == 'e_fio') {
                $this->user->status = UserStatusService::E_NAME;
                $this->user->edit_id = $this->parser::getByKey('id');
            } else {
                $this->user->status = UserStatusService::NAME;
            }
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['record_name'], [
                [$this->text['cancel']]
            ]);
        }
    }
}