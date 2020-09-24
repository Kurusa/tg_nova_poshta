<?php

namespace App\Commands;

use App\Models\Good;
use App\Models\Record;
use App\Services\GoodStatusService;
use App\Services\RecordStatusService;
use App\TgHelpers\TelegramKeyboard;

class RecordGoodChar extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->parser::getByKey('a') == 'char_done') {
            $record = Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->first();
            Good::where('record_id', $record->id)->where('status', GoodStatusService::FILLING)->update([
                'char' => $this->text['good_char_list'][$this->parser::getByKey('v')]
            ]);
            $this->tg->deleteMessage($this->parser::getMsgId());
            $this->tg->sendMessage($this->text['good_char_list'][$this->parser::getByKey('v')]);
            $this->triggerCommand(RecordGoodCount::class);
        } else {
            foreach ($this->text['good_char_list'] as $key => $char) {
                TelegramKeyboard::addButton($char, ['a' => 'char_done', 'v' => $key]);
            }
            $this->tg->sendMessageWithKeyboard($this->text['good_char'], [
                [$this->text['cancel']]
            ]);
            $this->tg->sendMessageWithInlineKeyboard($this->text['list'], TelegramKeyboard::get());
        }
    }
}