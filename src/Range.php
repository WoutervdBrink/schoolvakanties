<?php

namespace Knevelina\Schoolvakanties;

use DateTime;

readonly class Range
{
    public function __construct(
        public DateTime $start,
        public DateTime $end,

        /**
         * @var ?list<Region> $regions
         */
        public ?array   $regions,
    )
    {
        //
    }

    public function getHumanReadableRegions(): ?string
    {
        if ($this->regions === null) {
            return null;
        }

        return implode(', ', array_map(fn (?Region $region): string => $region?->value ?? 'heel Nederland', $this->regions));
    }
}