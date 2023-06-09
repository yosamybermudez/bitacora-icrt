<?php

namespace App\MessageHandler;

use App\Message\SendTelegramMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendTelegramMessageHandler implements MessageHandlerInterface
{
    public function __invoke(SendTelegramMessage $message)
    {
        // do something with your message
    }
}
