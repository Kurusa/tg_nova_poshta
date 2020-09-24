<?php

namespace App\Commands;

use App\Models\Good;

class GoodInfo extends BaseCommand
{

    function processCommand($par = false)
    {
        $good = Good::find($this->parser::getByKey('id'));
        $good_info = '<b>Код:</b> ' . $good['code'] . "\n";
        $good_info .= '<b>Назва:</b> ' . $good['title'] . "\n";
        $good_info .= '<b>Кількість:</b> ' . $good['amount'] . "\n";
        $good_info .= '<b>Вимір</b>: ' . $good['unit'] . "\n";
        $good_info .= '<b>Характеристика</b>: ' . $good['char'] . "\n";

        $this->tg->sendMessage($good_info);
    }

}