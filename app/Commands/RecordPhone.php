<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordPhone extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::PHONE || $this->user->status == UserStatusService::E_PHONE) {
            if ($this->user->status == UserStatusService::PHONE) {
                Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                    'phone' => $this->parser::getMessage()
                ]);
                $this->triggerCommand(RecordName::class);
            } else {
                Record::where('id', $this->user->edit_id)->update([
                    'phone' => $this->parser::getMessage()
                ]);
                $this->triggerCommand(RecordEdit::class);
            }
        } else {
            if ($this->parser::getByKey('a') == 'e_phone') {
                $this->user->status = UserStatusService::E_PHONE;
                $this->user->edit_id = $this->parser::getByKey('id');
            } else {
                $this->user->status = UserStatusService::PHONE;
            }
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['record_phone'], [
                [$this->text['cancel']]
            ]);
        }
    }
}