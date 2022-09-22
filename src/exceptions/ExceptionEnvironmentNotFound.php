<?php

namespace Programster\Bitbucket\Exceptions;


class ExceptionEnvironmentNotFound extends \Exception
{
    public function __construct(string $name)
    {
        parent::__construct("Could not find environment: {$name}");
    }
}