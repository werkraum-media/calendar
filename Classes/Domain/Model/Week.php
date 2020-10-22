<?php

namespace WerkraumMedia\Calendar\Domain\Model;

/*
 * Copyright (C) 2020 Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

class Week
{
    /**
     * @var int
     */
    private $week;

    /**
     * @var int
     */
    private $year;

    /**
     * @var Day[]
     */
    private $days = [];

    public function __construct(
        int $week,
        int $year
    ) {
        $this->week = $week;
        $this->year = $year;
    }

    public function isActive(): bool
    {
        foreach ($this->getDays() as $day) {
            if ($day->isActive()) {
                return true;
            }
        }

        return false;
    }

    public function getDays(): array
    {
        if ($this->days !== []) {
            return $this->days;
        }

        $currentDay = $this->getWeek()->modify('monday this week');
        $endOfWeek = $currentDay->modify('sunday this week');

        while ($currentDay <= $endOfWeek) {
            $this->days[] = new Day(\DateTime::createFromImmutable($currentDay));
            $currentDay = $currentDay->modify('+1 day');
        }

        return $this->days;
    }

    public function getPreviousWeek(): Week
    {
        $newDay = $this->getWeek()->modify('-1 week');

        return new self(
            (int) $newDay->format('W'),
            (int) $newDay->format('Y')
        );
    }

    public function getNextWeek(): Week
    {
        $newDay = $this->getWeek()->modify('+1 week');

        return new self(
            (int) $newDay->format('W'),
            (int) $newDay->format('Y')
        );
    }

    public function getDateTimeInstance(): \DateTimeImmutable
    {
        return $this->getWeek();
    }

    public function getAsUrlArgument(): array
    {
        return [
            'week' => $this->week,
            'year' => $this->year,
        ];
    }

    private function getWeek(): \DateTimeImmutable
    {
        $week = new \DateTimeImmutable();
        $week = $week->setISODate($this->year, $this->week);
        $week = $week->modify('thursday');

        return $week;
    }
}
