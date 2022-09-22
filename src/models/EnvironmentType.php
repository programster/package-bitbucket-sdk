<?php

namespace Programster\Bitbucket\Models;

use Programster\Bitbucket\Exceptions\ExceptionMissingRequiredKeys;


class EnvironmentType implements \JsonSerializable
{
    private readonly string $type;


    public static function createFromResponseArray(array $data) : EnvironmentType
    {
        $requiredKeys = ['name', 'rank'];
        $missingKeys = array_diff($requiredKeys, array_keys($data));

        if (count($missingKeys) > 0)
        {
            throw new ExceptionMissingRequiredKeys($missingKeys);
        }

        return new EnvironmentType($data['name'], $data['rank']);
    }


    public function __construct(private readonly string $name, private readonly int $rank)
    {
        $this->type = "deployment_environment_type";
    }


    public function toArray() : array
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'rank' => $this->rank
        ];
    }


    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}