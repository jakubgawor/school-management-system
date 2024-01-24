<?php

namespace App\Message;

class TokenNotification
{
    public function __construct(
        private string $sendTo,
        private string $content
    )
    {
    }

    public function getSendTo(): string
    {
        return $this->sendTo;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}