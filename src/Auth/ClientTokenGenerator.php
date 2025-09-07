<?php
namespace QyroSdk\Auth;


class ClientTokenGenerator
{
    private string $apiKeyId;
    private string $apiKeySecret;


    public function __construct(string $apiKeyId, string $apiKeySecret)
    {
        $this->apiKeyId = $apiKeyId;
        $this->apiKeySecret = $apiKeySecret;
    }


    public function generate(array $context): string
    {
        $subject = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $now = time();
        $payload = [
            'sub' => $subject,
            'iat' => $now,
            'exp' => $now + (24 * 30 * 3600),
            'type' => 'client',
            'iss' => (string) $this->apiKeyId,
            'aud' => 'qyro',
            'jti' => bin2hex(random_bytes(16)),
        ];
        $headers = ['kid' => (string) $this->apiKeyId];
        return $this->encodeJwt($payload, $this->apiKeySecret, $headers);
    }


    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }


    private function encodeJwt(array $payload, string $secret, array $headers = []): string
    {
        $header = array_merge(['alg' => 'HS256', 'typ' => 'JWT'], $headers);
        $segments = [];
        $segments[] = $this->base64UrlEncode(json_encode($header));
        $segments[] = $this->base64UrlEncode(json_encode($payload));
        $signingInput = implode('.', $segments);
        $signature = hash_hmac('sha256', $signingInput, $secret, true);
        $segments[] = $this->base64UrlEncode($signature);
        return implode('.', $segments);
    }
}