<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class UnsplashApiService
{
    private $client;
    private $keyApi;

    public function __construct(HttpClientInterface $client, string $keyApi)
    {
        $this->client = $client;
        $this->keyApi = $keyApi;
    }

    public function fetchPhotoRandom(string $search)
    {
        $response = $this->client->request(
            'GET',
            'https://api.unsplash.com/photos/random',
            [
                'query' => [
                    'client_id' => $this->keyApi,
                    'query' => $search
                ]
            ]
        );

        $photoUrl = $response->toArray(['urls']['regular']);

        return $photoUrl;
    }
}
