<?php

namespace Knevelina\Schoolvakanties;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

class RijksoverheidApi
{
    private const URL = 'https://opendata.rijksoverheid.nl/v1/sources/rijksoverheid/infotypes/schoolholidays?output=json';

    /**
     * @return list<Vacation>
     * @throws GuzzleException
     */
    public static function getVacations(): array
    {
        $body = Cache::loadFromCache(function (): string {
            $client = new Client();

            $response = $client->get(self::URL);

            if ($response->getStatusCode() !== 200) {
                throw new RuntimeException('Could not load data from Rijksoverheid API');
            }
            return (string) $response->getBody();
        });

        $data = json_decode($body);

        $vacations = [];

        foreach ($data as $collection) {
            if (
                !isset($collection->content) ||
                !is_array($collection->content) ||
                count($collection->content) !== 1
            ) {
                throw new RuntimeException('Unexpected response from Rijksoverheid API: content should be an array with length 1');
            }

            if (
                !isset($collection->content[0]->vacations) ||
                !is_array($collection->content[0]->vacations)
            ) {
                throw new RuntimeException('Unexpected response from Rijksoverheid API: content should contain an array vacations');
            }

            $vacations = array_merge($vacations, $collection->content[0]->vacations);
        }

        foreach ($vacations as $vacation) {
            if (!is_object($vacation)) {
                throw new RuntimeException('Unexpected response from Rijksoverheid API: vacation should be an object');
            }
        }

        return array_map(fn(object $vacation): Vacation => Vacation::fromData($vacation), $vacations);
    }
}