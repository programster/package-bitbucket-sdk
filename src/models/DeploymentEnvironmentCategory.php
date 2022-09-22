<?php

namespace Programster\Bitbucket\Models;

class DeploymentEnvironmentCategory implements \JsonSerializable
{
    public function __construct(private readonly string $name)
    {

    }


    public function toArray() : array
    {
        return [
            'name' => $this->name,
        ];
    }


    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }


    # Accessors
    public function getName() : string { return $this->name; }
}