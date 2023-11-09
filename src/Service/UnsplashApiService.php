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

    /**
     * Fetch a random pictures
     *
     * @param string $search theme to search
     * @return string|bool url of picture
     */
    public function fetchPhotoRandom(string $search): ?string
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

        $content = $response->getContent();
        $data = json_decode($content, true);

        if (!$data) {
            return false;
        }

        $url = $data['urls']['regular'];

        return $url;
    }
}
