<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProviderOne implements TaskProviderInterface
{
    private $client;
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getTasks(): array{
        $response = $this->client->request('GET', 'https://raw.githubusercontent.com/WEG-Technology/mock/refs/heads/main/mock-one');
        if ($response->getStatusCode() !== 200) {
            return [];
        }else{
            $content = $response->getContent();
            return json_decode($content, true);
        }
    }
}