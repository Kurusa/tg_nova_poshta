<?php

namespace App\Commands;

use App\Models\Record;
use App\Models\User;
use App\Services\UserStatusService;
use App\TgHelpers\NovaPoshtaApi;
use App\TgHelpers\TelegramKeyboard;

class RecordEdit extends BaseCommand
{

    function processCommand($par = false)
    {
        $nova_poshta = new NovaPoshtaApi();
        $record_id = $this->parser::getByKey('id') ?: $this->user->edit_id;
        $record = Record::find($record_id);
        $user = User::find($record['user_id']);

        $record_info = '<b> Статус замовлення</b>: ' . $record['record_status'] . "\n";
        $record_info .= '<b> Створив:</b> ' . $user->user_name . "\n";

        TelegramKeyboard::addButton('статус замовлення', ['a' => 'e_record_status', 'id' => $record_id]);

        $record_info .= '<b> Спосіб отримання:</b> ' . $record['delivery_type'] . "\n";
        TelegramKeyboard::addButton('спосіб отримання', ['a' => 'e_delivery_type', 'id' => $record_id]);

        $record_info .= '<b> Телефон отримувача:</b> ' . $record['phone'] . "\n";
        TelegramKeyboard::addButton('телефон', ['a' => 'e_phone', 'id' => $record_id]);

        $record_info .= '<b> Ім\'я і прізвище отримувача:</b> ' . $record['fio'] . "\n";
        TelegramKeyboard::addButton('ім\'я і прізвище', ['a' => 'e_fio', 'id' => $record_id]);

        $record_info .= '<b> Статус оплати</b>: ' . $record['payed_status'] . "\n";
        TelegramKeyboard::addButton('статус оплати', ['a' => 'e_payed_status', 'id' => $record_id]);

        if ($record['payed_status'] == 'частково оплачено') {
            $record_info .= '<b> Оплачена сума</b>: ' . $record['payed_sum'] . "\n";
            TelegramKeyboard::addButton('оплачена сума', ['a' => 'e_payed_sum', 'id' => $record_id]);
        }
        if ($record['delivery_type'] == 'автодоставка') {
            $record_info .= '<b> Адреса отримувача</b>: ' . $record['delivery_address'] . "\n";
            TelegramKeyboard::addButton('адреса отримувача', ['a' => 'e_delivery_address', 'id' => $record_id]);
        }
        if ($record['delivery_type'] == 'пошта') {
            $record_info .= '<b> Місто</b>: ' . $nova_poshta->getCityByRef($record['city'])['data'][0]['DescriptionRu'] . "\n";
            $record_info .= '<b> Номер відділення</b>: ' . $nova_poshta->getWarehouseByRef($record['city'], $record['post']) . "\n";
            TelegramKeyboard::addButton('місто', ['a' => 'e_city', 'id' => $record_id]);
            TelegramKeyboard::addButton('номер відділення', ['a' => 'e_post', 'id' => $record_id]);
        }

        $record_info .= '<b> Коментар</b>: ' . $record['comment'] . "\n";
        TelegramKeyboard::addButton('коментар', ['a' => 'e_comment', 'id' => $record_id]);

        $record_info .= '<b> Дата відправки</b>: ' . $record['delivery_date'] . "\n";
        TelegramKeyboard::addButton('дата відправки', ['a' => 'e_delivery_date', 'id' => $record_id]);

        TelegramKeyboard::addButton($this->text['back'], ['a' => 'record_edit_back', 'id' => $record['id']]);

        if ($this->user->edit_id > 0) {
            $this->tg->removeKeyboard('готово');
        }
        $this->tg->sendMessageWithInlineKeyboard($record_info, TelegramKeyboard::get());

        $this->user->edit_id = 0;
        $this->user->status = UserStatusService::DONE;
        $this->user->save();

    }

}