<?php

namespace Vk;

use Closure;

class CallbackApi
{
    private $messageHandler;
    private $errorHandler;

    private $confirmationToken;

    const CALLBACK_API_EVENT_CONFIRMATION = 'confirmation';
    const CALLBACK_API_EVENT_MESSAGE_NEW = 'message_new';

    public function __construct(string $confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
        if (!isset($_REQUEST)) {
            exit;
        }
    }

    public function setMessageHandler(Closure $cb)
    {
        $this->messageHandler = $cb;
        return $this;
    }

    public function setErrorHandler(Closure $cb)
    {
        $this->errorHandler = $cb;
        return $this;
    }

    public function handleEvent()
    {
        $event = $this->getEvent();

        try {
            switch ($event['type']) {
                //Подтверждение сервера
                case static::CALLBACK_API_EVENT_CONFIRMATION:
                    $this->handleConfirmation();
                    break;

                //Получение нового сообщения
                case static::CALLBACK_API_EVENT_MESSAGE_NEW:
                    $this->handleMessageNew($event);
                    break;

                default:
                    $this->response('Unsupported event');
                    break;
            }
        } catch (Exception $e) {
            $this->handleError($e);
        }

        $this->okResponse();
    }

    private function getEvent()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    private function handleConfirmation()
    {
        $this->response($this->confirmationToken);
    }

    private function handleMessageNew($event)
    {
        $callback = $this->messageHandler;
        if ($callback instanceof Closure) {
            $callback($event);
        }
        $this->okResponse();
    }

    private function handleError($e)
    {
        $callback = $this->erroCallback;
        if ($callback instanceof Closure) {
            $callback($e);
        }
    }

    private function okResponse()
    {
        $this->response('ok');
    }

    private function response($data)
    {
        echo $data;
        exit();
    }
}
