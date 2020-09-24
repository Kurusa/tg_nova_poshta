<?php

namespace App\Commands;

use App\Services\UserStatusService;
use App\TgHelpers\NovaPoshtaApi;
use App\TgHelpers\TelegramKeyboard;

class RecordDeliveryCity extends BaseCommand
{

    function processCommand($par = false)
    {
        if (($this->user->status == UserStatusService::DELIVERY_CITY && $this->parser::getByKey('a') !== 'wrongCity') ||
            ($this->user->status == UserStatusService::E_DELIVERY_CITY && $this->parser::getByKey('a') !== 'wrongCity')) {
            $poshta = new NovaPoshtaApi();
            $city_list = $poshta->getCities($this->parser::getMessage());
            if ($city_list['success'] && count($city_list['data'])) {
                foreach ($city_list['data'] as $city) {
                    TelegramKeyboard::addButton($city['DescriptionRu'], ['a' => 'citySelect', 'v' => $city['Ref']]);
                }
                TelegramKeyboard::addButton($this->text['try_again'], ['a' => 'wrongCity']);
                $this->tg->sendMessageWithInlineKeyboard($this->text['this_city'], TelegramKeyboard::get());
            } else {
                $this->tg->sendMessage($this->text['no_city']);
            }
        } else {
            if ($this->parser::getByKey('a') == 'wrongCity') {
                $this->tg->deleteMessage($this->parser::getMsgId());
            }

            if ($this->parser::getByKey('a') == 'e_city') {
                $this->user->status = UserStatusService::E_DELIVERY_CITY;
                $this->user->edit_id = $this->parser::getByKey('id');
            } else {
                $this->user->status = UserStatusService::DELIVERY_CITY;
            }
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['delivery_city'], [
                [$this->text['cancel']]
            ]);
        }
    }
}