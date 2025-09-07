<?php
namespace QyroSdk\Exceptions;


class HTTPError extends QyroError
{
    public int $statusCode;
    public $response;


    public function __construct(int $statusCode, string $message, $response = null)
    {
        parent::__construct("HTTP {$statusCode}: {$message}");
        $this->statusCode = $statusCode;
        $this->response = $response;
    }
}