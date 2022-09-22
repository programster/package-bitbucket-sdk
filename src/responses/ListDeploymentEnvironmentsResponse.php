<?php

namespace Programster\Bitbucket\Responses;

use Programster\Bitbucket\models\DeploymentEnvironment;
use Psr\Http\Message\ResponseInterface;


final class ListDeploymentEnvironmentsResponse extends Response
{
    private array $m_deploymentEnvironments = [];


    public function __construct(ResponseInterface $response)
    {
        parent::__construct($response);

        if ($response->getStatusCode() === 201)
        {
            $environmentData = json_decode($response->getBody()->getContents(), true);

            foreach ($environmentData['values'] as $deploymentEnvironmentArray)
            {
                $this->m_deploymentEnvironments[] =
                    DeploymentEnvironment::createFromResponseArray($deploymentEnvironmentArray);
            }
        }
    }


    /**
     * Get the deployment environments
     * @return DeploymentEnvironment[] - a collection of DeploymentEnvironment[] objects.
     * @throws \Exception
     */
    public function getDeploymentEnvironments() : array
    {
        if ($this->wasSuccessful() === false)
        {
            throw new ExceptionRequestWasUnsuccessful("There are no deployment environments because the request was unsuccessful.");
        }

        return $this->m_deploymentEnvironments;
    }
}