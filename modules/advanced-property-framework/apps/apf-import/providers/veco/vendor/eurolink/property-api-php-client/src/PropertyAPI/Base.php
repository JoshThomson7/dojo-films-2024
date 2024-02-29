<?php

namespace PropertyAPI;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

/**
* Property API Base class
*/
class Base
{
    protected $client;
    protected $baseURI = 'https://passport.eurolink.co/api/properties/v1/';

    public function __construct($params)
    {
        if (! isset($params['accessToken'])) {
            throw new \Exception('Access token is missing.');
        }

        if (! $params['accessToken']) {
            throw new \Exception('Access token is empty.');
        }

        $this->client = new GuzzleClient([
            'base_uri' => $this->baseURI,
            'headers' => [
                'Authorization' => 'Bearer ' . $params['accessToken'],
                'X-Source' => 'PHP Library',
            ],
        ]);
    }

    protected function request($path, $params = [])
    {
        try {
            $response = $this->client->get($path, [
                'query' => $params,
            ]);

            $contents = $response->getBody()->getContents();

            return json_decode($contents);
        } catch (RequestException $event) {
            throw $event;
        }
    }
}