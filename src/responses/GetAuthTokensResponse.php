<?php

namespace Programster\Bitbucket\Responses;

use Psr\Http\Message\ResponseInterface;


final class GetAuthTokensResponse extends Response
{
    private readonly ?string $m_accessToken;
    private readonly ?string $m_refreshToken;
    private readonly ?int $m_expiresIn;
    private readonly ?string $m_tokenType;
    private readonly ?string $m_scopes;


    public function __construct(ResponseInterface $response)
    {
        parent::__construct($response);

        if ($this->wasSuccessful())
        {
            $responseBody = json_decode($response->getBody()->getContents(), true);

            $this->m_accessToken = $responseBody['access_token'];
            $this->m_refreshToken = $responseBody['refresh_token'];
            $this->m_expiresIn = $responseBody['expires_in'];
            $this->m_tokenType = $responseBody['token_type']; // should always be "bearer"
            $this->m_scopes = $responseBody['scopes'];
        }
        else
        {
            $this->m_accessToken = null;
            $this->m_refreshToken = null;
            $this->m_expiresIn = null;
            $this->m_tokenType = null;
            $this->m_scopes = null;
        }
    }


    public function getAccessToken() : string
    {
        if ($this->m_wasSuccessful === false)
        {
            throw new ExceptionRequestWasUnsuccessful("There are no tokens when the request was unsuccessful.");
        }

        return $this->m_accessToken;
    }


    public function getRefreshToken() : string
    {
        if ($this->m_wasSuccessful === false)
        {
            throw new ExceptionRequestWasUnsuccessful("There are no tokens when the request was unsuccessful.");
        }

        return $this->m_refreshToken;
    }


    public function getExpiresIn() : int
    {
        if ($this->m_wasSuccessful === false)
        {
            throw new ExceptionRequestWasUnsuccessful("There are no tokens when the request was unsuccessful.");
        }

        return $this->m_expiresIn;
    }


    public function getTokenType() : string
    {
        if ($this->m_wasSuccessful === false)
        {
            throw new ExceptionRequestWasUnsuccessful("There are no tokens when the request was unsuccessful.");
        }

        return $this->m_tokenType;
    }


    public function getScopes() : string
    {
        if ($this->m_wasSuccessful === false)
        {
            throw new ExceptionRequestWasUnsuccessful("There are no tokens when the request was unsuccessful.");
        }

        return $this->m_scopes;
    }
}
