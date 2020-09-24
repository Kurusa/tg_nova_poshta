<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordAddress extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::DELIVERY_ADDRESS || $this->user->status == UserStatusService::E_DELIVERY_ADDRESS) {
            if ($this->user->status == UserStatusService::DELIVERY_ADDRESS) {
                Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                    'delivery_address' => $this->parser::getMessage()
                ]);
                $this->triggerCommand(RecordGoodCode::class);
            } else {
                Record::where('id', $this->user->edit_id)->update([
                    'delivery_address' => $this->parser::getMessage()
                ]);
                $this->triggerCommand(RecordEdit::class);
            }
        } else {
            if ($this->parser::getByKey('a') == 'e_delivery_address') {
                $this->user->status = UserStatusService::E_DELIVERY_ADDRESS;
                $this->user->edit_id = $this->parser::getByKey('id');
            } else {
                $this->user->status = UserStatusService::DELIVERY_ADDRESS;
            }
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['delivery_address'], [
                [$this->text['cancel']]
            ]);
        }
    }
}