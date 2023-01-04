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

class Day
{
    /**
     * @var \DateTimeImmutable
     */
    private $day;

    /**
     * @var mixed
     */
    private $foreignData;

    /**
     * @var bool
     */
    private $initialized = false;

    public function __construct(
        \DateTime $day
    ) {
        $this->periods = new ObjectStorage();
        $this->day = \DateTimeImmutable::createFromMutable($day)->modify('midnight');
    }

    public function isActive(): bool
    {
        $foreignData = $this->getForeignData();
        if ($foreignData instanceof IsDayActive) {
            return $foreignData->isActive($this->getDateTimeInstance());
        }

        return false;
    }

    public function getForeignData()
    {
        $this->initializeForeignData();

        return $this->foreignData;
    }

    public function getDateTimeInstance(): \DateTimeImmutable
    {
        return $this->day;
    }

    public function getAsUrlArgument(): array
    {
        return [
            'day' => $this->day->format('Y-m-d'),
        ];
    }

    private function initializeForeignData(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->foreignData = GeneralUtility::makeInstance(ForeignDataFactory::class)
            ->getData($this);
        $this->initialized = true;
    }
}
