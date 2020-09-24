<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordPayedStatus extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::DELIVERY_STATUS || $this->user->status == UserStatusService::E_DELIVERY_STATUS) {
            if ($this->user->status == UserStatusService::DELIVERY_STATUS) {
                Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                    'payed_status' => $this->parser::getMessage()
                ]);
            } else {
                Record::where('id', $this->user->edit_id)->update([
                    'payed_status' => $this->parser::getMessage()
                ]);
            }

            if ($this->parser::getMessage() == $this->text['half_pay']) {
                $this->tg->sendMessageWithKeyboard($this->text['half_pay_sum'], [
                    [$this->text['cancel']]
                ]);
                if ($this->user->status == UserStatusService::DELIVERY_STATUS) {
                    $this->user->status = UserStatusService::HALF_PAY_SUM;
                } else {
                    $this->user->status = UserStatusService::E_HALF_PAY_SUM;
                }
                $this->user->save();
                exit;
            } elseif ($this->user->status == UserStatusService::E_DELIVERY_STATUS) {
                $this->triggerCommand(RecordEdit::class);
            } else {
                $this->triggerCommand(RecordComment::class);
            }
        } elseif ($this->user->status == UserStatusService::HALF_PAY_SUM || $this->user->status == UserStatusService::E_HALF_PAY_SUM) {
            if ($this->user->status == UserStatusService::HALF_PAY_SUM) {
                Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                    'payed_sum' => $this->parser::getMessage()
                ]);
                $this->triggerCommand(RecordComment::class);
            } else {
                Record::where('id', $this->user->edit_id)->update([
                    'payed_sum' => $this->parser::getMessage()
                ]);
                $this->triggerCommand(RecordEdit::class);
            }
        } else {
            if ($this->parser::getByKey('a') == 'e_payed_status') {
                $this->user->status = UserStatusService::E_DELIVERY_STATUS;
                $this->user->edit_id = $this->parser::getByKey('id');
            } else {
                $this->user->status = UserStatusService::DELIVERY_STATUS;
            }
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['delivery_status'], [
                [$this->text['unknown'], $this->text['no_pay']], [$this->text['half_pay'], $this->text['pay']], [$this->text['cancel']]
            ]);
        }
    }
}