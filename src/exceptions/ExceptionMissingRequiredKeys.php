<?php

namespace Programster\Bitbucket\Exceptions;


class ExceptionMissingRequiredKeys extends \Exception
{
    public function __construct(private readonly array $keys, ?string $message = null)
    {
        $message = $message ?? "Missing required keys: " .  implode(", ", $keys);
        parent::__construct($message);
    }


    public function getKeys() : array
    {
        return $this->keys;
    }
}