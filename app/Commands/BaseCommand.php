<?php

namespace App\Commands;

use App\Models\User;
use App\TgHelpers\TelegramParser;
use App\TgHelpers\TelegramApi;

/**
 * Class BaseCommand
 * @package App\Commands
 */
abstract class BaseCommand
{

    /**
     * @var TelegramParser
     */
    protected $parser;

    /**
     * @var TelegramApi
     */
    protected $tg;

    /**
     * @var User
     */
    protected $user;

    protected $text;

    /**
     * telegram update
     * @var
     */
    private $update;

    /**
     * @param array $update
     * @param bool $par
     */
    function handle(array $update, $par = false)
    {
        $this->update = $update;
        $this->parser = new TelegramParser($update);
        $this->tg = new TelegramApi();
        $this->tg->chat_id = $this->parser::getChatId();

        $this->text = require(__DIR__ . '/../config/text.php');
        $this->user = User::where('chat_id', $this->parser::getChatId())->first();
        if (!$this->user) {
            $this->user = User::create([
                'chat_id' => $this->parser::getChatId(),
                'user_name' => $this->parser::getUserName(),
            ]);
        }

        $this->processCommand($par ? $par : '');
    }

    /**
     * @param $class
     * @param bool $par
     */
    function triggerCommand($class, $par = false)
    {
        (new $class())->handle($this->update, $par);
    }

    abstract function processCommand($par = false);

}