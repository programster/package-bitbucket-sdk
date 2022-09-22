<?php

namespace Programster\Bitbucket\Models;

class DeploymentEnvironmentCategory
{
    public function __construct(private readonly string $name)
    {

    }

    public function getName() : string { return $this->name; }
}