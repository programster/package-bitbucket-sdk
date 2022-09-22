<?php

namespace Programster\Bitbucket\Models;

class DeploymentEnvironmentLock
{
    public function __construct(private readonly string $type, private readonly string $name)
    {

    }

    public function getName() : string { return $this->name; } // OPEN
    public function getType() : string { return $this->type; } // deployment_environment_lock_open
}