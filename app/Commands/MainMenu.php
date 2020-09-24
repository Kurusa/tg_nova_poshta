<?php

namespace App\Commands;

use App\Models\Good;
use App\Models\Record;
use App\Services\GoodStatusService;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

/**
 * Class MainMenu
 * @package App\Commands
 */
class MainMenu extends BaseCommand
{

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        // delete possible undone record
        $filling_record = Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->first();
        if ($filling_record) {
            Good::where('record_id', $filling_record->id)->where('status', GoodStatusService::FILLING)->delete();
            $filling_record->delete();
        }

        $this->user->status = UserStatusService::DONE;
        $this->user->save();

        $this->tg->sendMessageWithKeyboard($par ?: $this->text['main_menu'], [
            [$this->text['create_record']], [$this->text['record_list']]
        ]);
    }

}