<?php

namespace Programster\Bitbucket\models;


use Programster\Bitbucket\JsonSerializable;

class BitbucketVariable implements JsonSerializable
{
    private readonly string $type;


    public function __construct(
        private readonly string $uuid,
        private readonly string $key,
        private readonly mixed $value,
        private readonly bool $secured
    )
    {
        $this->type = "pipeline_variable";
    }


    public function toArray(): array
    {
        return array(
            "uuid" => $this->uuid,
            "type" => $this->type,
            "key" => $this->key,
            "value" => $this->value,
            "secured" => $this->secured
        );
    }


    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }


    # Accessors
    public function getUuid() : string     { return $this->uuid; }
    public function getKey() : string      { return $this->key; }
    public function getValue() : mixed     { return $this->value; }
    public function getSecured() : bool    { return $this->secured; }
}