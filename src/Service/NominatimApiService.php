<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class NominatimApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch coordinates lat and lon a address
     *
     * @param string $city search city
     * @param string $location search address
     * @return array|bool coordinates of city 
     */
    public function getCoordinates(string $city, string $location = ''): ?array
    {
        $response = $this->client->request(
            'GET',
            'https://nominatim.openstreetmap.org/search?',
            [
                'query' => [
                    'city' => $location . ' ' . $location,
                    'format' => 'jsonv2',
                ]
            ]
        );

        $content = $response->getContent();
        $data = json_decode($content, true);

        if (!$data) {
            $response = $this->client->request(
                'GET',
                'https://nominatim.openstreetmap.org/search?',
                [
                    'query' => [
                        'city' => $city,
                        'format' => 'jsonv2',
                    ]
                ]
            );

            $content = $response->getContent();
            $data = json_decode($content, true);

            if (!$data) {
                return false;
            }
        }

        $coordinates = [];
        $coordinates['lat'] = $data[0]['lat'];
        $coordinates['lon'] = $data[0]['lon'];

        return $coordinates;
    }
}
