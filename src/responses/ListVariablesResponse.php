<?php

namespace Programster\Bitbucket\Responses;

use Programster\Bitbucket\Models\BitbucketVariable;
use Psr\Http\Message\ResponseInterface;


class ListVariablesResponse extends Response
{
    protected array $m_variables;


    public function __construct(ResponseInterface $response)
    {
        parent::__construct($response);
        $this->m_variables = [];

        if ($response->getStatusCode() === 200)
        {
            $responseData = json_decode($response->getBody()->getContents(), true);

            if (array_key_exists('values', $responseData) === FALSE)
            {
                throw new Exception("List variables response does not contain values.");
            }

            $page = $responseData['page'];
            $size = $responseData['size'];
            $length = $responseData['pagelen'];

            foreach ($responseData['values'] as $responseVariable)
            {
                $this->m_variables[] = new BitbucketVariable(
                    uuid: $responseVariable['uuid'],
                    key: $responseVariable['key'],
                    value: (array_key_exists('value', $responseVariable)) ? $responseVariable['value'] : null, // may not be set if value is secured/null
                    secured: $responseVariable['secured'],
                );
            }
        }
    }


    # Accessors
    public function getVariables() : array
    {
        if ($this->m_wasSuccessful === false)
        {
            throw new ExceptionRequestWasUnsuccessful("There are no response variables when the request was unsuccessful.");
        }

        return $this->m_variables;
    }
}