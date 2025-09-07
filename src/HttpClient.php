<?php
namespace QyroSdk;


use QyroSdk\Exceptions\QyroError;


class HttpClient
{
    private float $timeout;


    public function __construct(float $timeout = 30.0)
    {
        $this->timeout = $timeout;
    }


    public function request(string $method, string $url, ?array $body = null, array $headers = []): array
    {
        $ch = curl_init();
        $opts = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_TIMEOUT => (int) $this->timeout,
        ];


        if ($body !== null) {
            $opts[CURLOPT_POSTFIELDS] = json_encode($body);
            $headers[] = 'Content-Type: application/json';
        }


        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = $headers;
        }


        curl_setopt_array($ch, $opts);
        $resp = curl_exec($ch);
        $errno = curl_errno($ch);
        $err = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        if ($errno) {
            throw new QyroError("cURL error ({$errno}): {$err}");
        }


        return [$status, $resp ?: ''];
    }
}