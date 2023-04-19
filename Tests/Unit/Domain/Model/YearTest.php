<?php

namespace WerkraumMedia\Calendar\Tests\Unit\Domain\Model;

/*
 * Copyright (C) 2021 Daniel Siepmann <coding@daniel-siepmann.de>
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

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WerkraumMedia\Calendar\Domain\Model\Month;
use WerkraumMedia\Calendar\Domain\Model\Year;
use WerkraumMedia\Calendar\Tests\ForcePropertyTrait;

#[TestDox('A year')]
class YearTest extends TestCase
{
    use ForcePropertyTrait;

    #[Test]
    public function canBeCreated(): void
    {
        $subject = new Year(2020);

        self::assertInstanceOf(Year::class, $subject);
    }

    #[Test]
    public function returnsPreviousYear(): void
    {
        $subject = new Year(2020);

        $result = $subject->getPreviousYear();

        self::assertInstanceOf(Year::class, $result);
        self::assertSame('2019', $result->getDateTimeInstance()->format('Y'));
    }

    #[Test]
    public function returnsNextYear(): void
    {
        $subject = new Year(2020);

        $result = $subject->getNextYear();

        self::assertInstanceOf(Year::class, $result);
        self::assertSame('2021', $result->getDateTimeInstance()->format('Y'));
    }

    #[Test]
    public function returnsAsUrlArguments(): void
    {
        $subject = new Year(2020);

        self::assertSame([
            'year' => 2020,
        ], $subject->getAsUrlArgument());
    }

    #[Test]
    public function returnsMonthsForYear2020(): void
    {
        $subject = new Year(2020);

        $result = $subject->getMonths();

        self::assertCount(12, $result);

        foreach ($result as $index => $month) {
            self::assertInstanceOf(Month::class, $month);
            $monthNumber = $index + 1;
            self::assertSame((string)$monthNumber, $month->getDateTimeInstance()->format('n'));
        }
    }

    #[Test]
    public function returnsSameMonthsOnSecondCall(): void
    {
        $subject = new Year(2020);

        self::assertSame($subject->getMonths(), $subject->getMonths());
    }

    #[Test]
    public function returnsAllDaysFor2020(): void
    {
        $subject = new Year(2020);

        $result = $subject->getDays();

        self::assertCount(366, $result);
        self::assertSame('2020-01-01', $result[0]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2020-12-31', $result[365]->getDateTimeInstance()->format('Y-m-d'));
    }

    #[Test]
    public function returnsNotActiveIfAllMonthsAreInactive(): void
    {
        $subject = new Year(2020);

        $month = $this->createStub(Month::class);
        $month->method('isActive')->willReturn(false);
        $months = [$month];
        $this->forceProperty($subject, 'months', $months);

        self::assertFalse($subject->isActive());
    }

    #[Test]
    public function returnsActiveIfASingleMonthIsActive(): void
    {
        $subject = new Year(2020);

        $month = $this->createStub(Month::class);
        $month->method('isActive')->willReturn(true);
        $months = [$month];
        $this->forceProperty($subject, 'months', $months);

        self::assertTrue($subject->isActive());
    }
}
