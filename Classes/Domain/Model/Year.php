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

class Year
{
    /**
     * @var int
     */
    private $year;

    /**
     * @var Month[]
     */
    private $months = [];

    public function __construct(
        int $year
    ) {
        $this->year = $year;
    }

    public function isActive(): bool
    {
        foreach ($this->getMonths() as $month) {
            if ($month->isActive()) {
                return true;
            }
        }

        return false;
    }

    public function getMonths(): array
    {
        if ($this->months !== []) {
            return $this->months;
        }

        $lastMonth = new \DateTimeImmutable($this->year . '-12-31');
        $currentMonth = new \DateTimeImmutable($this->year . '-01-01');

        while ($currentMonth <= $lastMonth) {
            $this->months[] = new Month(
                (int) $currentMonth->format('n'),
                $this->year
            );

            $currentMonth = $currentMonth->modify('+1 month');
        }

        return $this->months;
    }

    public function getDays(): array
    {
        $days = [];

        foreach ($this->getMonths() as $month) {
            $days = array_merge($days, $month->getDays());
        }

        return $days;
    }

    public function getPreviousYear(): Year
    {
        return new self($this->year - 1);
    }

    public function getNextYear(): Year
    {
        return new self($this->year + 1);
    }

    public function getAsUrlArgument(): array
    {
        return [
            'year' => $this->year,
        ];
    }

    public function getDateTimeInstance(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->year . '-01-01');
    }
}
