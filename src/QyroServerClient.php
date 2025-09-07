<?php


use QyroSdk\Auth\ApiKeyAuth;
use QyroSdk\Exceptions\{HTTPError, ConfigurationError};
use QyroSdk\Models\{Session, Message};
use QyroSdk\HttpClient;

class QyroServerClient
{
    private string $baseUrl;
    private ApiKeyAuth $auth;
    private HttpClient $http;


    public function __construct(string $baseUrl, string $apiKeyId, string $apiKeySecret, float $timeout = 30.0)
    {
        if (empty($baseUrl)) {
            throw new ConfigurationError('base_url is required');
        }
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->auth = new ApiKeyAuth($apiKeyId, $apiKeySecret);
        $this->http = new HttpClient($timeout);
    }


    private function url(string $path): string
    {
        return $this->baseUrl . $path;
    }


    private function raiseForStatus(int $status, ?string $body): void
    {
        if ($status >= 200 && $status < 300)
            return;
        $msg = $body ?? '';
        $decoded = json_decode($body, true);
        if (is_array($decoded)) {
            $msg = $decoded['message'] ?? json_encode($decoded);
        }
        throw new HTTPError($status, (string) $msg, $decoded ?? $body);
    }


    public function createSession(string $assistantId, array $context): Session
    {
        [$status, $resp] = $this->http->request('POST', $this->url("/server/api/v1/assistants/{$assistantId}/sessions"), ['context' => $context], ["Authorization: " . $this->auth->headerValue()]);
        $this->raiseForStatus($status, $resp);
        $data = json_decode($resp, true);
        return new Session($data['id']);
    }


    /** @return Message[] */
    public function fetchSessionMessages(string $assistantId, string $sessionId): array
    {
        [$status, $resp] = $this->http->request('GET', $this->url("/server/api/v1/assistants/{$assistantId}/sessions/{$sessionId}/messages"), null, ["Authorization: " . $this->auth->headerValue()]);
        $this->raiseForStatus($status, $resp);
        $messages = json_decode($resp, true);
        return array_map(fn($m) => new Message($m['id'], $m['role'], $m['content']), $messages ?? []);
    }


    /** @return Message[] */
    public function chat(string $assistantId, string $sessionId, string $message): array
    {
        [$status, $resp] = $this->http->request('POST', $this->url("/server/api/v1/assistants/{$assistantId}/sessions/{$sessionId}/chat"), ['message' => $message], ["Authorization: " . $this->auth->headerValue(), 'Content-Type: application/json']);
        $this->raiseForStatus($status, $resp);
        $messages = json_decode($resp, true);
        return array_map(fn($m) => new Message($m['id'], $m['role'], $m['content']), $messages ?? []);
    }
}