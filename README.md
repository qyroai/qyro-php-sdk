# Qyro PHP SDK

Qyro PHP SDK for interacting with assistants, sessions, and chat APIs.

## Installation

Install via Composer:

```bash
composer require qyroai/qyro-php-sdk
```

## Usage

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use QyroSdk\Auth\ClientTokenGenerator;
use QyroSdk\Server\QyroServerClient;
use QyroSdk\Client\QyroClient;

$BASE_URL = "https://qyroai.com";
$API_KEY_ID = "<>";
$API_KEY_SECRET = "<>";
$ASSISTANT_ID = "<>";

// --- Server SDK Usage ---
$serverClient = new QyroServerClient(
    baseUrl: $BASE_URL,
    apiKeyId: $API_KEY_ID,
    apiKeySecret: $API_KEY_SECRET,
    timeout: 120.0
);

$session = $serverClient->createSession($ASSISTANT_ID, ["userId" => "123"]);
$sessionId = $session->id;

$outputMessages = $serverClient->chat(
    assistantId: $ASSISTANT_ID,
    sessionId: $sessionId,
    message: "Hello, who are you?"
);

print_r($outputMessages);

// --- Client SDK Usage ---
$clientTokenGenerator = new ClientTokenGenerator($API_KEY_ID, $API_KEY_SECRET);
$clientToken = $clientTokenGenerator->generate([
    "userId" => "123"
]);

$client = new QyroClient(
    baseUrl: $BASE_URL,
    token: $clientToken
);

$session = $client->createSession($ASSISTANT_ID, ["userId" => "123"]);
$sessionId = $session->id;

$outputMessages = $client->chat(
    assistantId: $ASSISTANT_ID,
    sessionId: $sessionId,
    message: "Hello, who are you?"
);

print_r($outputMessages);
```

## Requirements
- PHP >= 8.0
- Composer

## License
MIT
