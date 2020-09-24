<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;
use App\TgHelpers\NovaPoshtaApi;
use App\TgHelpers\TelegramKeyboard;

class RecordDeliverySettlements extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->parser::getByKey('a') == 'citySelect') {
            if ($this->user->status == UserStatusService::E_DELIVERY_CITY) {
                Record::where('id', $this->user->edit_id)->update([
                    'city' => $this->parser::getByKey('v')
                ]);
                $this->triggerCommand(RecordEdit::class);
            } else {
                Record::where('user_id' , $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                    'city' => $this->parser::getByKey('v')
                ]);
                $this->user->status = UserStatusService::DELIVERY_SETTLEMENT;
                $this->user->save();
                $this->tg->sendMessage($this->text['enter_poshta_number']);
            }
        } elseif ($this->parser::getByKey('a') == 'thisSett') {
            Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                'post' => $this->parser::getByKey('v')
            ]);
            $this->triggerCommand(RecordGoodCode::class);
        } elseif ($this->user->status == UserStatusService::DELIVERY_SETTLEMENT && $this->parser::getByKey('a') !== 'wrongSet') {
            $poshta = new NovaPoshtaApi();
            $record = Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->first();
            $settlements_list = $poshta->getWarehouses($record->city);
            if ($settlements_list['success']) {
                foreach ($settlements_list['data'] as $settlement) {
                    if ($settlement['Number'] == $this->parser::getMessage()) {
                        TelegramKeyboard::addButton($settlement['DescriptionRu'], ['a' => 'thisSett', 'v' => $settlement['Ref']]);
                        TelegramKeyboard::addButton($this->text['try_again'], ['a' => 'wrongSet']);
                        $this->tg->sendMessageWithInlineKeyboard($this->text['this_settlement'], TelegramKeyboard::get());
                        exit;
                    }
                }
                $this->tg->sendMessage($this->text['no_settlements']);
            } else {
                $this->tg->sendMessage($this->text['no_settlements']);
            }
        } elseif ($this->parser::getByKey('a') == 'wrongSet') {
            $this->tg->deleteMessage($this->parser::getMsgId());
            $this->tg->sendMessage($this->text['enter_poshta_number']);
        }
    }
}