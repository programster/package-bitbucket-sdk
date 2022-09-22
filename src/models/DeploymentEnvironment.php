<?php

namespace Programster\Bitbucket\Models;


final class DeploymentEnvironment implements \JsonSerializable
{
    private string $m_uuid;
    private string $m_type;
    private string $m_name;
    private string $m_slug;
    private EnvironmentType $m_environmentType;
    private bool $m_hidden;
    private DeploymentEnvironmentCategory $m_category;
    private ?int   $m_rank;
    private ?bool $m_deploymentGateEnabled;
    private ?bool $m_environmentLockEnabled;
    private ?DeploymentEnvironmentLock $m_lock;


    private function __construct()
    {
        // this always has to be this value and not to be confused with EnvironmentType
        $this->m_type = "deployment_environment";
    }


    /**
     * Create a new environment, for POSTing to Bitbucket.
     * @param string $uuid
     * @param string $name
     * @param string $slug
     * @param EnvironmentType $environmentType
     * @param string $categoryName
     * @param bool $hidden
     * @param int|null $rank
     * @return DeploymentEnvironment
     */
    public static function createNew(
        string $uuid,
        string $name,
        string $slug,
        EnvironmentType $environmentType,
        string $categoryName,
        bool $hidden = false,
        ?int $rank = null,
    ) : DeploymentEnvironment
    {
        $deploymentEnvironment = new DeploymentEnvironment();
        $deploymentEnvironment->m_uuid = $uuid;
        $deploymentEnvironment->m_name = $name;
        $deploymentEnvironment->m_slug = $slug;
        $deploymentEnvironment->m_category = new DeploymentEnvironmentCategory($categoryName);
        $deploymentEnvironment->m_environmentType = $environmentType;
        $deploymentEnvironment->m_hidden = $hidden;

        $deploymentEnvironment->m_rank = $rank;
        $deploymentEnvironment->m_lock = null;
        $deploymentEnvironment->m_environmentLockEnabled = null;
        $deploymentEnvironment->m_deploymentGateEnabled = null;
        return $deploymentEnvironment;
    }


    /**
     * Create an environment from a Bitbucket response array.
     * @param array $responseData
     * @return DeploymentEnvironment
     * @throws \Programster\Bitbucket\Exceptions\ExceptionMissingRequiredKeys
     */
    public static function createFromResponseArray(array $responseData) : DeploymentEnvironment
    {
        $deploymentEnvironment = new DeploymentEnvironment();
        $deploymentEnvironment->m_uuid = $responseData['uuid'];
        $deploymentEnvironment->m_name = $responseData['name'];
        $deploymentEnvironment->m_slug = $responseData['slug'];
        $deploymentEnvironment->m_rank = $responseData['rank'];
        $deploymentEnvironment->m_environmentType = EnvironmentType::createFromResponseArray($responseData['environment_type']);

        $deploymentEnvironment->m_category = new DeploymentEnvironmentCategory($responseData['category']['name']);

        $deploymentEnvironment->m_lock = new DeploymentEnvironmentLock(
            $responseData['lock']['type'],
            $responseData['lock']['name'],
        );

        $deploymentEnvironment->m_environmentLockEnabled = $responseData['environment_lock_enabled'];
        $deploymentEnvironment->m_hidden = $responseData['hidden'];

        return $deploymentEnvironment;
    }


    public function toArray() : array
    {
        $arrayForm = [
            'uuid' => $this->m_uuid,
            'type' => $this->m_type, // should always be 'deployment_environment',
            'name' => $this->m_name,
            'slug' => $this->m_slug,
            'hidden' => $this->m_hidden,
            'environment_type' => $this->m_environmentType->toArray(),
            'category' => $this->m_category,
        ];

        if ($this->m_rank !== null) { $arrayForm['rank'] = $this->m_rank; }
        if ($this->m_environmentLockEnabled !== null) { $arrayForm['environment_lock_enabled'] = $this->m_environmentLockEnabled; }
        if ($this->m_deploymentGateEnabled !== null) { $arrayForm['deployment_gate_enabled'] = $this->m_deploymentGateEnabled; }
        if ($this->m_lock !== null) { $arrayForm['lock'] = $this->m_lock; }

        return $arrayForm;
    }


    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}