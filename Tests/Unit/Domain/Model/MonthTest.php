<?php

namespace WerkraumMedia\Calendar\Tests\Unit\Domain\Model;

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

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WerkraumMedia\Calendar\Domain\Model\Month;
use WerkraumMedia\Calendar\Domain\Model\Week;
use WerkraumMedia\Calendar\Tests\ForcePropertyTrait;

#[TestDox('A month')]
class MonthTest extends TestCase
{
    use ForcePropertyTrait;

    #[Test]
    public function canBeCreated(): void
    {
        $subject = new Month(1, 2020);

        self::assertInstanceOf(Month::class, $subject);
    }

    #[Test]
    public function returnsPreviousMonthForSameYear(): void
    {
        $subject = new Month(2, 2020);

        self::assertSame('01', $subject->getPreviousMonth()->getDateTimeInstance()->format('m'));
        self::assertSame('2020', $subject->getPreviousMonth()->getDateTimeInstance()->format('Y'));
    }

    #[Test]
    public function returnsPreviousMonthForPreviousYear(): void
    {
        $subject = new Month(1, 2020);

        self::assertSame('12', $subject->getPreviousMonth()->getDateTimeInstance()->format('m'));
        self::assertSame('2019', $subject->getPreviousMonth()->getDateTimeInstance()->format('Y'));
    }

    #[Test]
    public function returnsNextMonthForSameYear(): void
    {
        $subject = new Month(1, 2020);

        self::assertSame('02', $subject->getNextMonth()->getDateTimeInstance()->format('m'));
        self::assertSame('2020', $subject->getNextMonth()->getDateTimeInstance()->format('Y'));
    }

    #[Test]
    public function returnsNextMonthForNextYear(): void
    {
        $subject = new Month(12, 2020);

        self::assertSame('01', $subject->getNextMonth()->getDateTimeInstance()->format('m'));
        self::assertSame('2021', $subject->getNextMonth()->getDateTimeInstance()->format('Y'));
    }

    #[Test]
    public function returnsFiveWeeksForDecember2020(): void
    {
        $subject = new Month(12, 2020);
        $weeks = $subject->getWeeks();

        self::assertCount(5, $weeks);
        self::assertSame('2020-11-30', $weeks[0]->getDays()[0]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2021-01-03', $weeks[4]->getDays()[6]->getDateTimeInstance()->format('Y-m-d'));
    }

    #[Test]
    public function returnsSixWeeksForNovember2020(): void
    {
        $subject = new Month(11, 2020);
        $weeks = $subject->getWeeks();

        self::assertCount(6, $weeks);
        self::assertSame('2020-10-26', $weeks[0]->getDays()[0]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2020-12-06', $weeks[5]->getDays()[6]->getDateTimeInstance()->format('Y-m-d'));
    }

    #[Test]
    public function returnsSameWeeksOnSecondCall(): void
    {
        $subject = new Month(11, 2020);

        self::assertSame($subject->getWeeks(), $subject->getWeeks());
    }

    #[Test]
    public function returnsAllDaysOfTheFebruaryMonth2021(): void
    {
        $subject = new Month(02, 2021);

        $result = $subject->getDays();

        self::assertCount(28, $result);
        self::assertSame('2021-02-01', $result[0]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2021-02-28', $result[27]->getDateTimeInstance()->format('Y-m-d'));
    }

    #[Test]
    public function returnsWeeksIfLastDecemberWeekIsInNextYear(): void
    {
        $subject = new Month(12, 2024);

        $result = $subject->getWeeks();

        self::assertCount(6, $result);

        $week = array_pop($result);
        $days = $week->getDays();
        self::assertSame('2024-12-30', $days[0]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2025-01-05', $days[6]->getDateTimeInstance()->format('Y-m-d'));

        $week = array_pop($result);
        $days = $week->getDays();
        self::assertSame('2024-12-23', $days[0]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2024-12-29', $days[6]->getDateTimeInstance()->format('Y-m-d'));
    }

    #[Test]
    public function returnsAllDaysOfTheJuneMonth2021(): void
    {
        $subject = new Month(06, 2021);

        $result = $subject->getDays();

        self::assertCount(30, $result);
        self::assertSame('2021-06-01', $result[0]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2021-06-30', $result[29]->getDateTimeInstance()->format('Y-m-d'));
    }

    #[Test]
    public function returnsSameDaysOnSecondCall(): void
    {
        $subject = new Month(06, 2021);

        self::assertSame($subject->getDays(), $subject->getDays());
    }

    #[Test]
    public function providesDateTimeInstance(): void
    {
        $subject = new Month(02, 2018);

        self::assertSame('2018-02-01', $subject->getDateTimeInstance()->format('Y-m-d'));
    }

    #[Test]
    public function returnsAsUrlArguments(): void
    {
        $subject = new Month(02, 2018);

        self::assertSame([
            'month' => 2,
            'year' => 2018,
        ], $subject->getAsUrlArgument());
    }

    #[Test]
    public function returnsNotActiveIfAllWeeksAreInactive(): void
    {
        $subject = new Month(02, 2018);

        $week = $this->createStub(Week::class);
        $week->method('isActive')->willReturn(false);
        $weeks = [$week];
        $this->forceProperty($subject, 'weeks', $weeks);

        self::assertFalse($subject->isActive());
    }

    #[Test]
    public function returnsActiveIfASingleWeekIsActive(): void
    {
        $subject = new Month(02, 2018);

        $week = $this->createStub(Week::class);
        $week->method('isActive')->willReturn(true);
        $week2 = $this->createStub(Week::class);
        $week2->method('isActive')->willReturn(false);
        $weeks = [$week, $week2];
        $this->forceProperty($subject, 'weeks', $weeks);

        self::assertTrue($subject->isActive());
    }
}
