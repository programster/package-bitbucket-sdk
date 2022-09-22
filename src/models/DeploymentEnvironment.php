<?php

namespace Programster\Bitbucket\models;


class DeploymentEnvironment implements \JsonSerializable
{
    private string $m_type;
    private string $m_name;
    private string $m_slug;
    private int    $m_rank;
    private EnvironmentType $m_environmentType;
    private bool $m_hidden;
    private bool $m_environmentLockEnabled;
    private bool $m_deploymentGateEnabled;


    public function __construct(
        string $uuid,
        string $name,
        string $slug,
        int $rank,
        EnvironmentType $environmentType,
        bool $hidden = false,
        bool $environmentLockEnabled,
        bool $m_deploymentGateEnabled = false
    )
    {
        $this->m_uuid = $uuid;
        $this->m_name = $name;
        $this->m_slug = $slug;
        $this->m_rank = $rank;
        $this->m_environmentType = $environmentType;
        $this->m_hidden = $hidden;
        $this->m_environmentLockEnabled = $environmentLockEnabled;
        $this->m_deploymentGateEnabled = $m_deploymentGateEnabled;
    }


    public static function createFromResponseArray(array $responseData)
    {
        $deploymentEnvironment = new DeploymentEnvironment();
        $deploymentEnvironment->m_uuid = $responseData['uuid'];
        $deploymentEnvironment->m_type = $responseData['type']; // should always be 'deployment_environment',
        $deploymentEnvironment->m_name = $responseData['name'];
        $deploymentEnvironment->m_slug = $responseData['slug'];
        $deploymentEnvironment->m_rank = $responseData['rank'];
        $deploymentEnvironment->m_hidden = $responseData['hidden'];

        $deploymentEnvironment->m_environmentType = EnvironmentType::createFromResponseArray(
            $responseData['environment_type']
        );

        return $deploymentEnvironment;
    }


    public function toArray() : array
    {
        return [
            'uuid' => $this->m_uuid,
            'type' => $this->m_type, // should always be 'deployment_environment',
            'name' => $this->m_name,
            'slug' => $this->m_slug,
            'rank' => $this->m_rank,
            'hidden' => $this->m_hidden,
            'environment_type' => $this->m_environmentType->toArray(),
            'environment_lock_enabled' => $this->m_environmentLockEnabled,
            'deployment_gate_enabled' => $this->m_deploymentGateEnabled
        ];
    }


    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}