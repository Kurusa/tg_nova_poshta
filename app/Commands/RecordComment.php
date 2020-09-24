<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordComment extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::COMMENT || $this->user->status == UserStatusService::E_COMMENT) {
            if ($this->parser::getMessage() !== $this->text['skip']) {
                if ($this->user->status == UserStatusService::COMMENT) {
                    Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                        'comment' => $this->parser::getMessage()
                    ]);
                    $this->triggerCommand(RecordDeliveryDate::class);
                } else {
                    Record::where('id', $this->user->edit_id)->update([
                        'comment' => $this->parser::getMessage()
                    ]);
                    $this->triggerCommand(RecordEdit::class);
                }
            } else {
                $this->triggerCommand(RecordDeliveryDate::class);
            }
        } else {
            if ($this->parser::getByKey('a') == 'e_comment') {
                $buttons = [
                    [$this->text['cancel']]
                ];
                $this->user->status = UserStatusService::E_COMMENT;
                $this->user->edit_id = $this->parser::getByKey('id');
            } else {
                $buttons = [
                    [$this->text['skip']], [$this->text['cancel']]
                ];
                $this->user->status = UserStatusService::COMMENT;
            }
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['delivery_comment'], $buttons);
        }
    }
}