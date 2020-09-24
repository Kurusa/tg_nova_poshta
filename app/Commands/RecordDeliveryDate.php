<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordDeliveryDate extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::DELIVERY_DATE || $this->user->status == UserStatusService::E_DELIVERY_DATE) {
            if ($this->user->status == UserStatusService::DELIVERY_DATE) {
                $record = Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->first();
                $record->delivery_date = $this->parser::getMessage();
                $record->save();

                switch ($record->delivery_type) {
                    case $this->text['self-pickup']:
                        $this->triggerCommand(RecordGoodCode::class);
                        break;
                    case $this->text['auto-delivery']:
                        $this->triggerCommand(RecordAddress::class);
                        break;
                    case $this->text['post']:
                        $this->triggerCommand(RecordDeliveryCity::class);
                        break;
                }
            } else {
                Record::where('id', $this->user->edit_id)->update([
                    'delivery_date' => $this->parser::getMessage()
                ]);
                $this->triggerCommand(RecordEdit::class);
            }
        } else {
            if ($this->parser::getByKey('a') == 'e_delivery_date') {
                $this->user->status = UserStatusService::E_DELIVERY_DATE;
                $this->user->edit_id = $this->parser::getByKey('id');
            } else {
                $this->user->status = UserStatusService::DELIVERY_DATE;
            }
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['delivery_date'], [
                [$this->text['cancel']]
            ]);
        }
    }
}