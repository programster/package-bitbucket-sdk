<?php

namespace Programster\Bitbucket\Models;


class DeploymentEnvironment implements \JsonSerializable
{
    private string $m_uuid;
    private string $m_type;
    private string $m_name;
    private string $m_slug;
    private int    $m_rank;
    private EnvironmentType $m_environmentType;
    private bool $m_hidden;
    private bool $m_environmentLockEnabled;
    private bool $m_deploymentGateEnabled;
    private DeploymentEnvironmentCategory $m_category;
    private DeploymentEnvironmentLock $m_lock;


    public function __construct(
        string $uuid,
        string $name,
        string $slug,
        int $rank,
        EnvironmentType $environmentType,
        DeploymentEnvironmentCategory $category,
        DeploymentEnvironmentLock $lock,
        bool $environmentLockEnabled,
        bool $m_deploymentGateEnabled = false,
        bool $hidden = false,
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
        $this->m_category = $category;
    }


    public static function createFromResponseArray(array $responseData)
    {
        $deploymentEnvironment = new DeploymentEnvironment(
            uuid: $responseData['uuid'],
            name: $responseData['name'],
            slug: $responseData['slug'],
            rank: $responseData['rank'],
            environmentType: EnvironmentType::createFromResponseArray($responseData['environment_type']),
            category: new DeploymentEnvironmentCategory($responseData['category']['name']),
            lock: new DeploymentEnvironmentLock(
                $responseData['lock']['type'],
                $responseData['lock']['name'],
            ),
            environmentLockEnabled: $responseData['environment_lock_enabled'],
            hidden: $responseData['hidden'],
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
            'deployment_gate_enabled' => $this->m_deploymentGateEnabled,
            'category' => $this->m_category,
            'lock' => $this->m_lock,
        ];
    }


    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}