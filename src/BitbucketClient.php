<?php

namespace Programster\Bitbucket;

use GuzzleHttp\Client;
use Programster\Bitbucket\models\BitbucketVariable;
use Programster\Bitbucket\models\DeploymentEnvironment;
use Programster\Bitbucket\responses\ListDeploymentEnvironmentsResponse;
use Programster\Bitbucket\responses\ListVariablesResponse;
use Programster\Bitbucket\responses\Response;
use Programster\Http\HttpMethod;
use Psr\Http\Message\ResponseInterface;


class BitbucketClient
{
    private readonly CLient $m_client;
    private readonly string $m_accessToken;
    private readonly string $m_baseUrl;


    public function __construct(
        private readonly string $accessToken
    )
    {
        $this->m_baseUrl = "https://api.bitbucket.org/2.0";
        $this->m_accessToken = $this->accessToken;
        $this->m_client = new Client();
    }


    /**
     * Create a deployment variable in Bitbucket.
     * Bitbucket Docs: https://bit.ly/3RQJ9P2
     * @param string $repoSlug - the repository to create the deployment variable for.
     * @param string $environmentId - the ID of the environment to create the variable within.
     * @param BitbucketVariable $deploymentVariable - the variable to create.
     * @return mixed
     */
    public function createDeploymentVariable(
        string $workspaceId,
        string $repoSlug,
        string $environmentId,
        BitbucketVariable $deploymentVariable
    ) : Response
    {
        $path = "/repositories/{$workspaceId}/{$repoSlug}/deployments_config/environments/{$environmentId}/variables";
        $guzzleResponse = $this->sendRequest(HttpMethod::POST, $path, $deploymentVariable->toArray());
        return new Response($guzzleResponse);
    }


    /**
     * Create a variable for a repository. Note: this will appear in all deployments. If you want to set a variable
     * specific to a deployment environment (production/staging etc), then use createDeploymentVariable instead.
     * Bitbucket docs: https://bit.ly/3RZ9Ot6
     * @param string $workspaceId
     * @param string $repoSlug
     * @return mixed
     */
    public function createRepositoryVariable(
        string $workspaceId,
        string $repoSlug,
        BitbucketVariable $variable
    ) : Response
    {
        $path = "/repositories/{$workspaceId}/{$repoSlug}/pipelines_config/variables/";
        return new Response($this->sendRequest(HttpMethod::POST, $path, $variable->toArray));
    }


    /**
     * Delete a deployment variable.
     * Bitbucket docs: https://bit.ly/3RUaJuK
     * @param string $workspaceId
     * @param string $repoSlug
     * @param string $environmentId
     * @param string $variableId
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteDeploymentVariable(
        string $workspaceId,
        string $repoSlug,
        string $environmentId,
        string $variableId
    ) : Response
    {
        $path =
            "/repositories/{$workspaceId}/{$repoSlug}/deployments_config/environments/{$environmentId}" .
            "/variables/{$variableId}";

        return new Response($this->sendRequest('DELETE', $path));
    }


    /**
     * Get the URL you need to redirect the user to, in order for the user to log in, and return an authorization_code
     * @param string $clientId - Your bitbucket OAuth client ID.
     * @return string - The URL to redirect the user to.
     */
    public static function getAuthorizationCodeFlowUrl(string $clientId) : string
    {
        // Landing page, redirect the user to bitbucket to authenticate.
        $queryParameters = [
            'client_id' => $clientId,
            'response_type' => 'code',
        ];

        return 'https://bitbucket.org/site/oauth2/authorize?' . http_build_query($queryParameters);
    }


    /**
     * Send a request to Bitbucket to exchange the one-time use code we have been given, for the user tokens,
     * including the access_token.
     * @param string $code - the authorization code returned to us from the OIDC login (forms part of the
     * authorization_code flow grant).
     * @param string $clientId - the client ID that Bitbucket gave us.
     * @param string $clientSecret - the secret that Bitbucket gave us.
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function exchangeAuthCodeForTokens(string $code, string $clientId, string $clientSecret)
    {
        $client = new Client();

        $options = [
            'http_errors' => false, // dont raise an exception on 400/500 errors etc.
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($clientId . ":" . $clientSecret),
            ],
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $code
            ]
        ];

        $response = $client->sendRequest('POST', 'https://bitbucket.org/site/oauth2/access_token', $options);

        if ($response->getStatusCode() !== 200)
        {
            die("Auth code was non 200. Try again.");
        }

        $responseBody = json_decode($response->getBody()->getContents(), true);

        /* @TODO return these in an object */
        $accessToken = $responseBody['access_token'];
        $refreshToken = $responseBody['refresh_token'];
        $expiresIn = $responseBody['expires_in'];
        $tokenType = $responseBody['token_type']; // should always be "bearer"
        $scopes = $responseBody['scopes'];

        return $response;
    }


    /**
     * Fetches the UUID of an environment, by its human name. E.g. "production" or "staging".
     * @param string $workspaceId
     * @param string $repoSlug
     * @param string $environmentName
     * @return string
     */
    public function getEnvironmentId(string $workspaceId, string $repoSlug, string $environmentName) : string
    {
        $uuid = null;
        $environments = $this->listDeploymentEnvironments($workspaceId, $repoSlug);

        foreach ($environments as $environment)
        {
            if ($environment['slug'] === strtolower($environmentName))
            {
                $uuid = $environment['uuid'];
                break;
            }
        }

        if ($uuid === null)
        {
            throw new Exception("Failed to find environment: {$environmentName}");
        }

        return $uuid;
    }


    /**
     * List the deployment variables for an environment.
     * Bitbucket docs: https://bit.ly/3BKQMkm
     * @param string $repoSlug
     * @param string $environmentId
     * @param BitbucketVariable $deploymentVariable
     * @return mixed
     */
    public function listDeploymentVariables(
        string $workspaceId,
        string $repoSlug,
        string $environmentId
    ) : ListVariablesResponse
    {
        $path = "/repositories/{$workspaceId}/{$repoSlug}/deployments_config/environments/{$environmentId}/variables";
        return new ListVariablesResponse($this->sendRequest('GET', $path));
    }


    /**
     * List the environments in a repository.
     * Bitbucket docs: https://bit.ly/3xyvDaz
     * @param string $workspaceId - The ID of the workspace. This can be the unique string that looks like a slug, or
     * it can be a UUID, wrapped in {} characters.
     * @param string $repoSlug - the slug of the repository we wish to list the environments for.
     * @return mixed
     */
    public function listDeploymentEnvironments(string $workspaceId, string $repoSlug)
    {
        $path = "/repositories/{$workspaceId}/{$repoSlug}/environments/";
        return new ListDeploymentEnvironmentsResponse($this->sendRequest(HttpMethod::GET, $path));
    }


    /**
     * List the environments in a repository.
     * Bitbucket docs: https://bit.ly/3xyvDaz
     * @param string $workspaceId - The ID of the workspace. This can be the unique string that looks like a slug, or
     * it can be a UUID, wrapped in {} characters.
     * @param string $repoSlug - the slug of the repository we wish to list the environments for.
     * @return mixed
     */
    public function createDeploymentEnvironment(
        string $workspaceId,
        string $repoSlug,
        DeploymentEnvironment $deploymentEnvironment
    ) : ResponseInterface
    {
        $path = "/repositories/{$workspaceId}/{$repoSlug}/environments/";
        return $this->sendRequest(HttpMethod::POST, $path, $deploymentEnvironment);
    }


    /**
     * Delete a deployment environment
     * Bitbucket docs: https://bit.ly/3DE88kh
     * @param string $workspaceId - The ID of the workspace. This can be the unique string that looks like a slug, or
     * it can be a UUID, wrapped in {} characters.
     * @param string $repoSlug - the slug of the repository we wish to list the environments for.
     * @return mixed
     */
    public function deleteDeploymentEnvironment(
        string $workspaceId,
        string $repoSlug,
        DeploymentEnvironment $deploymentEnvironment
    ) : ResponseInterface
    {
        $path = "/repositories/{$workspaceId}/{$repoSlug}/environments/";
        return $this->sendRequest(HttpMethod::DELETE, $path, $deploymentEnvironment);
    }


    /**
     * Update a deployment variable in Bitbucket.
     * Bitbucket Docs: https://bit.ly/3RUaJuK
     * @param string $repoSlug
     * @param string $environmentId
     * @param string $existingDeploymentVariableId
     * @param BitbucketVariable $deploymentVariable
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateDeploymentVariable(
        string $workspaceId,
        string $repoSlug,
        string $environmentId,
        string $existingDeploymentVariableId,
        BitbucketVariable $deploymentVariable
    ) : Response
    {
        $path =
            "/repositories/{$workspaceId}/{$repoSlug}/deployments_config/environments/{$environmentId}" .
            "/variables/{$existingDeploymentVariableId}";

        return new Response($this->sendRequest('PUT', $path, $deploymentVariable->toArray()));
    }


    /**
     * Helper method that sends the request, adding the required auth headers etc.
     * @param HttpMethod $method
     * @param string $path
     * @param array|null $body
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function sendRequest(HttpMethod $method, string $path, array $body = null) : ResponseInterface
    {
        $options = [
            'http_errors' => false, // dont raise an exception on 400/500 errors etc.

            'headers' => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => "Bearer {$this->m_accessToken}",
            ]
        ];

        if ($body !== null)
        {
            $options = [
                'json' => $body
            ];
        }

        $url = $this->m_baseUrl . $path;
        return $this->m_client->request($method->value, $url, $options);
    }
}
