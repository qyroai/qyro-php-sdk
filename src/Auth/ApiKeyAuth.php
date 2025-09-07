<?php
namespace QyroSdk\Auth;


class ApiKeyAuth
{
    private string $apiKeyId;
    private string $apiKeySecret;


    public function __construct(string $apiKeyId, string $apiKeySecret)
    {
        $this->apiKeyId = $apiKeyId;
        $this->apiKeySecret = $apiKeySecret;
    }


    public function headerValue(): string
    {
        return sprintf('ApiKey %s', $this->apiKeySecret);
    }


    public function getKeyId(): string
    {
        return $this->apiKeyId;
    }
}