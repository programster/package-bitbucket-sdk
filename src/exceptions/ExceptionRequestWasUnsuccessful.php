<?php

namespace Programster\Bitbucket\Exceptions;


class ExceptionRequestWasUnsuccessful extends \Exception
{
    public function __construct(?string $message = null)
    {
        $message = $message ?? "Request was unsuccessful.";
        parent::__construct($message);
    }
}