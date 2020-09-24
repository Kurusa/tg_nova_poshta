<?php

namespace App\Commands;

use App\Models\Record;
use App\Models\RecordStatusChange;
use App\Services\RecordStatusService;
use App\TgHelpers\TelegramKeyboard;

class RecordStatus extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->parser::getByKey('a') == 'e_record_status') {
            TelegramKeyboard::addButton('Новий', ['a' => 'rstatus', 'v' => 'new', 'id' => $this->parser::getByKey('id')]);
            TelegramKeyboard::addButton('В роботі', ['a' => 'rstatus', 'v' => 'working', 'id' => $this->parser::getByKey('id')]);
            TelegramKeyboard::addButton('Зроблено', ['a' => 'rstatus', 'v' => 'done', 'id' => $this->parser::getByKey('id')]);
            TelegramKeyboard::addButton('Упаковано', ['a' => 'rstatus', 'v' => 'pack', 'id' => $this->parser::getByKey('id')]);
            TelegramKeyboard::addButton('Відправлено', ['a' => 'rstatus', 'v' => 'send', 'id' => $this->parser::getByKey('id')]);
            $this->user->edit_id = $this->parser::getByKey('id');
            $this->user->save();
            $this->tg->sendMessageWithInlineKeyboard('Виберіть статус замовлення', TelegramKeyboard::get());
        } else {
            $status = [
                'new' => 'Новий',
                'working' => 'В роботі',
                'done' => 'Зроблено',
                'pack' => 'Упаковано',
                'send' => 'Відправлено',
            ];

            Record::where('id', $this->parser::getByKey('id'))->update([
                'record_status' => $status[$this->parser::getByKey('v')]
            ]);
            RecordStatusChange::create([
                'user_id' => $this->user->id,
                'record_id' => $this->parser::getByKey('id'),
                'status' => $status[$this->parser::getByKey('v')]
            ]);
            $this->tg->deleteMessage($this->parser::getMsgId());
            $this->triggerCommand(RecordEdit::class);
        }
    }
}