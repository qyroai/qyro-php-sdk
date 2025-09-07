<?php
namespace QyroSdk;


use QyroSdk\Exceptions\{HTTPError, ConfigurationError};
use QyroSdk\Models\{Session, Message};


class QyroClient
{
    private string $baseUrl;
    private string $token;
    private HttpClient $http;


    public function __construct(string $baseUrl, string $token, float $timeout = 30.0)
    {
        if (empty($baseUrl)) {
            throw new ConfigurationError('base_url is required');
        }
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = $token;
        $this->http = new HttpClient($timeout);
    }


    private function url(string $path): string
    {
        return $this->baseUrl . $path;
    }


    private function clientHeaders(): array
    {
        return ["Authorization: Bearer {$this->token}", 'Content-Type: application/json'];
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
        [$status, $resp] = $this->http->request('POST', $this->url("/client/api/v1/assistants/{$assistantId}/sessions"), ['context' => $context], $this->clientHeaders());
        $this->raiseForStatus($status, $resp);
        $data = json_decode($resp, true);
        return new Session($data['id']);
    }


    /** @return Message[] */
    public function fetchSessionMessages(string $assistantId, string $sessionId): array
    {
        [$status, $resp] = $this->http->request('GET', $this->url("/client/api/v1/assistants/{$assistantId}/sessions/{$sessionId}/messages"), null, $this->clientHeaders());
        $this->raiseForStatus($status, $resp);
        $messages = json_decode($resp, true);
        return array_map(fn($m) => new Message($m['id'], $m['role'], $m['content']), $messages ?? []);
    }


    /** @return Message[] */
    public function chat(string $assistantId, string $sessionId, string $message): array
    {
        [$status, $resp] = $this->http->request('POST', $this->url("/client/api/v1/assistants/{$assistantId}/sessions/{$sessionId}/chat"), ['message' => $message], $this->clientHeaders());
        $this->raiseForStatus($status, $resp);
        $messages = json_decode($resp, true);
        return array_map(fn($m) => new Message($m['id'], $m['role'], $m['content']), $messages ?? []);
    }
}