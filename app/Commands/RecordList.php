<?php

namespace App\Commands;

use App\Models\Good;
use App\Models\Record;
use App\Models\RecordStatusChange;
use App\Models\User;
use App\Services\RecordStatusService;
use App\TgHelpers\NovaPoshtaApi;
use App\TgHelpers\TelegramKeyboard;

class RecordList extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->parser::getByKey('a') == 'record_info' || $this->parser::getByKey('a') == 'record_edit_back') {
            $nova_poshta = new NovaPoshtaApi();
            $record = Record::find($this->parser::getByKey('id'));
            $user = User::find($record['user_id']);
            $status_change = RecordStatusChange::where('record_id', $record['id'])->get();

            $record_info = '<b> Статус замовлення</b>: ' . $record['record_status'] . "\n";
            if ($status_change->count()) {
                $record_info .= '<b> Історія змін: </b>' . "\n";
                foreach ($status_change as $change) {
                    $user = User::find($change['user_id']);
                    $record_info .= $user->user_name . ' => ' . $change['status'] . "\n";
                }
            }
            $record_info .= '<b> Створив:</b> ' . $user->user_name . "\n";
            $record_info .= '<b> Спосіб отримання:</b> ' . $record['delivery_type'] . "\n";
            $record_info .= '<b> Телефон отримувача:</b> ' . $record['phone'] . "\n";
            $record_info .= '<b> Ім\'я і прізвище отримувача:</b> ' . $record['fio'] . "\n";
            $record_info .= '<b> Статус оплати</b>: ' . $record['payed_status'] . "\n";
            if ($record['payed_status'] == 'частково оплачено') {
                $record_info .= '<b> Оплачена сума</b>: ' . $record['payed_sum'] . "\n";
            }
            if ($record['delivery_type'] == 'автодоставка') {
                $record_info .= '<b> Адреса отримувача</b>: ' . $record['delivery_address'] . "\n";
            }
            if ($record['delivery_type'] == 'пошта') {
                $record_info .= '<b> Місто</b>: ' . $nova_poshta->getCityByRef($record['city'])['data'][0]['DescriptionRu'] . "\n";
                $record_info .= '<b> Номер відділення</b>: ' . $nova_poshta->getWarehouseByRef($record['city'], $record['post']) . "\n";
            }
            $record_info .= '<b> Коментар</b>: ' . $record['comment'] . "\n";
            $record_info .= '<b> Дата відправки</b>: ' . $record['delivery_date'] . "\n";

            TelegramKeyboard::addButton($this->text['edit'], ['a' => 'record_edit_butt', 'id' => $this->parser::getByKey('id')]);
            $goods = Good::where('record_id', $record['id'])->get();
            foreach ($goods as $good) {
                $record_info .= "\n";
                $record_info .= '<b>Код:</b> ' . $good['code'] . "\n";
                $record_info .= '<b>Назва:</b> ' . $good['title'] . "\n";
                $record_info .= '<b>Кількість:</b> ' . $good['amount'] . "\n";
                $record_info .= '<b>Вимір</b>: ' . $good['unit'] . "\n";
                $record_info .= '<b>Характеристика</b>: ' . $good['char'] . "\n";
            }
            if ($this->parser::getByKey('a') == 'record_edit_back') {
                $this->tg->updateMessageKeyboard($this->parser::getMsgId(), $record_info, TelegramKeyboard::get());
            } else {
                $this->tg->sendMessageWithInlineKeyboard($record_info, TelegramKeyboard::get());
            }
        } else {
            $record_list = Record::where('status', RecordStatusService::DONE)->get();
            foreach ($record_list as $record) {
                TelegramKeyboard::addButton('#' . $record['id'] . ' ' . $record['record_status'] . ', ' . $record['created_at'], ['a' => 'record_info', 'id' => $record['id']]);
            }
            $this->tg->sendMessageWithInlineKeyboard($this->text['more_info'], TelegramKeyboard::get());
        }
    }

}