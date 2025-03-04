<?php

namespace Knevelina\Schoolvakanties;

class Cache
{
    private static function getCachePath(): string
    {
        return __DIR__.'/../data/cache.json';
    }

    public static function loadFromCache(callable $callback, int $timeout = 86400): string
    {
        $cache = @file_get_contents(self::getCachePath());
        $cache = @json_decode($cache);

        if ($cache === false || $cache === null) {
            $cache = (object)[
                'data' => null,
                'ts' => 0
            ];
        }

        if (time() - $cache->ts >= $timeout) {
            try {
                $cache->data = $callback();
                $cache->ts = time();
            } catch (\Exception $exception) {
                //
            }
        }

        file_put_contents(self::getCachePath(), json_encode($cache));

        return $cache->data;
    }
}