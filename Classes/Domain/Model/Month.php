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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Month
{
    /**
     * @var int
     */
    private $month;

    /**
     * @var int
     */
    private $year;

    /**
     * @var Week[]
     */
    private $weeks = [];

    public function __construct(
        int $month,
        int $year
    ) {
        $this->month = $month;
        $this->year = $year;
    }

    public function isActive(): bool
    {
        foreach ($this->getWeeks() as $week) {
            if ($week->isActive()) {
                return true;
            }
        }

        return false;
    }

    public function getWeeks(): array
    {
        if ($this->weeks !== []) {
            return $this->weeks;
        }

        $month = $this->getDateTimeInstance();
        $lastDay = $month->modify('last day of this month')->modify('sunday this week');
        $currentDay = $month->modify('monday this week');

        while ($currentDay <= $lastDay) {
            $this->weeks[] = new Week(
                (int) $currentDay->format('W'),
                (int) $currentDay->format('Y')
            );

            $currentDay = $currentDay->modify('+7 days');
        }

        return $this->weeks;
    }

    public function getPreviousMonth(): Month
    {
        $previousMonth = $this->month - 1;
        $previousYear = $this->year;

        if ($previousMonth <= 0) {
            $previousMonth = 12;
            --$previousYear;
        }

        return new self(
            $previousMonth,
            $previousYear
        );
    }

    public function getNextMonth(): Month
    {
        $nextMonth = $this->month + 1;
        $nextYear = $this->year;

        if ($nextMonth > 12) {
            $nextMonth = 1;
            ++$nextYear;
        }

        return new self(
            $nextMonth,
            $nextYear
        );
    }

    public function getAsUrlArgument(): array
    {
        return [
            'month' => $this->month,
            'year' => $this->year,
        ];
    }

    public function getDateTimeInstance(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->year . '-' . $this->month . '-01');
    }
}
