<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordDeliveryType extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::DELIVERY_TYPE || $this->user->status == UserStatusService::E_DELIVERY_TYPE) {
            if ($this->user->status == UserStatusService::E_DELIVERY_TYPE) {
                Record::where('id', $this->user->edit_id)->update([
                    'delivery_type' => $this->parser::getMessage()
                ]);
                $this->triggerCommand(RecordEdit::class);
            } else {
                Record::create([
                    'user_id' => $this->user->id,
                    'delivery_type' => $this->parser::getMessage()
                ]);
                $this->triggerCommand(RecordPhone::class);
            }
        } else {
            if ($this->parser::getByKey('a') == 'e_delivery_type') {
                $this->user->status = UserStatusService::E_DELIVERY_TYPE;
                $this->user->edit_id = $this->parser::getByKey('id');
            } else {
                $this->user->status = UserStatusService::DELIVERY_TYPE;
            }
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['delivery_type'], [
                [$this->text['self-pickup'], $this->text['auto-delivery']], [$this->text['post']]
            ]);
        }
    }
}