<?php

namespace Knevelina\Schoolvakanties;

use DateTime;

class Range
{
    public function __construct(
        public readonly DateTime $start,
        public readonly DateTime $end,

        /**
         * @var ?list<Region> $regions
         */
        public readonly ?array     $regions,
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