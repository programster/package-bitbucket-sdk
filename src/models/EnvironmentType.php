<?php

namespace Programster\Bitbucket\models;


class EnvironmentType implements \JsonSerializable
{
    private readonly string $type;


    public static function createFromResponseArray(array $data) : EnvironmentType
    {
        $requiredKeys = ['name', 'rank'];
        $missingKeys = array_diff(array_keys($data), $requiredKeys);

        if (count($missingKeys) > 0)
        {
            $msg = 'EnvironmentType::createFromResponseArray - missing required keys: ' . implode(', ', $missingKeys);
            throw new \Exception($msg);
        }

        return new EnvironmentType($data['name'], $data['rank']);
    }


    public function __construct(private readonly string $name, private readonly int $rank)
    {
        $this->m_type = "deployment_environment_type";
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