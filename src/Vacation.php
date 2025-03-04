<?php

namespace Knevelina\Schoolvakanties;

use DateMalformedStringException;
use DateTime;
use RuntimeException;

readonly class Vacation
{

    public array $ranges;

    /**
     * @throws DateMalformedStringException
     */
    public function __construct(
        public string $type,
        array         $regionData
    )
    {
        $this->ranges = self::parseRegionData($regionData);
    }

    /**
     * @param array $regionData
     * @return list<Range>
     * @throws DateMalformedStringException
     */
    private static function parseRegionData(array $regionData): array
    {
        $points = [];

        foreach ($regionData as $region) {
            if (
                !isset($region->startdate, $region->enddate, $region->region) ||
                !is_string($region->startdate) ||
                !is_string($region->enddate) ||
                !$region->region
            ) {
                throw new RuntimeException('Unexpected response from Rijksoverheid API: region should be an object with region, startdate and enddate');
            }

            $points[] = [
                'time' => new DateTime($region->startdate),
                'type' => 'start',
                'region' => Region::tryFrom($region->region),
            ];
            $points[] = [
                'time' => new DateTime($region->enddate),
                'type' => 'end',
                'region' => Region::tryFrom($region->region),
            ];
        }

        usort($points, function ($a, $b) {
            if ($a['time'] === $b['time']) {
                return $a['type'] === 'end' ? -1 : 1;
            }
            return $a['time'] <=> $b['time'];
        });

        $result = [];
        $activeRegions = [];
        $prevTime = null;

        foreach ($points as $point) {
            if ($prevTime !== null && !empty($activeRegions)) {
                $result[] = new Range(
                    clone $prevTime,
                    clone $point['time'],
                    $activeRegions === [null] ? null : $activeRegions,
                );
            }

            if ($point['type'] === 'start') {
                $activeRegions[] = $point['region'];
            } else {
                $activeRegions = array_udiff($activeRegions, [$point['region']], fn(?Region $region1, ?Region $region2): int => $region1?->value <=> $region2?->value);
            }

            $prevTime = $point['time'];
        }

        return $result;
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function fromData(object $vacation): self
    {
        if (
            !isset($vacation->type, $vacation->regions) ||
            !is_string($vacation->type) ||
            !is_array($vacation->regions)
        ) {
            throw new RuntimeException('Unexpected response from Rijksoverheid API: vacation should be an object with string type and array regions');
        }

        return new self(trim($vacation->type), $vacation->regions);
    }
}