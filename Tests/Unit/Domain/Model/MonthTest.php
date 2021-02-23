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

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use WerkraumMedia\Calendar\Domain\Model\Month;
use WerkraumMedia\Calendar\Domain\Model\Week;
use WerkraumMedia\Calendar\Tests\ForcePropertyTrait;

/**
 * @covers WerkraumMedia\Calendar\Domain\Model\Month
 * @testdox A month
 */
class MonthTest extends TestCase
{
    use ProphecyTrait;
    use ForcePropertyTrait;

    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $subject = new Month(1, 2020);

        self::assertInstanceOf(Month::class, $subject);
    }

    /**
     * @test
     */
    public function returnsPreviousMonthForSameYear(): void
    {
        $subject = new Month(2, 2020);

        self::assertSame('01', $subject->getPreviousMonth()->getDateTimeInstance()->format('m'));
        self::assertSame('2020', $subject->getPreviousMonth()->getDateTimeInstance()->format('Y'));
    }

    /**
     * @test
     */
    public function returnsPreviousMonthForPreviousYear(): void
    {
        $subject = new Month(1, 2020);

        self::assertSame('12', $subject->getPreviousMonth()->getDateTimeInstance()->format('m'));
        self::assertSame('2019', $subject->getPreviousMonth()->getDateTimeInstance()->format('Y'));
    }

    /**
     * @test
     */
    public function returnsNextMonthForSameYear(): void
    {
        $subject = new Month(1, 2020);

        self::assertSame('02', $subject->getNextMonth()->getDateTimeInstance()->format('m'));
        self::assertSame('2020', $subject->getNextMonth()->getDateTimeInstance()->format('Y'));
    }

    /**
     * @test
     */
    public function returnsNextMonthForNextYear(): void
    {
        $subject = new Month(12, 2020);

        self::assertSame('01', $subject->getNextMonth()->getDateTimeInstance()->format('m'));
        self::assertSame('2021', $subject->getNextMonth()->getDateTimeInstance()->format('Y'));
    }

    /**
     * @test
     */
    public function returnsFiveWeeksForDecember2020(): void
    {
        $subject = new Month(12, 2020);
        $weeks = $subject->getWeeks();

        self::assertCount(5, $weeks);
        self::assertSame('2020-11-30', $weeks[0]->getDays()[0]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2021-01-03', $weeks[4]->getDays()[6]->getDateTimeInstance()->format('Y-m-d'));
    }

    /**
     * @test
     */
    public function returnsSixWeeksForNovember2020(): void
    {
        $subject = new Month(11, 2020);
        $weeks = $subject->getWeeks();

        self::assertCount(6, $weeks);
        self::assertSame('2020-10-26', $weeks[0]->getDays()[0]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2020-12-06', $weeks[5]->getDays()[6]->getDateTimeInstance()->format('Y-m-d'));
    }

    /**
     * @test
     */
    public function returnsSameWeeksOnSecondCall(): void
    {
        $subject = new Month(11, 2020);

        self::assertSame($subject->getWeeks(), $subject->getWeeks());
    }

    /**
     * @test
     */
    public function returnsAllDaysOfTheFebruaryMonth2021(): void
    {
        $subject = new Month(02, 2021);

        $result = $subject->getDays();

        self::assertCount(28, $result);
        self::assertSame('2021-02-01', $result[0]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2021-02-28', $result[27]->getDateTimeInstance()->format('Y-m-d'));
    }

    /**
     * @test
     */
    public function returnsAllDaysOfTheJuneMonth2021(): void
    {
        $subject = new Month(06, 2021);

        $result = $subject->getDays();

        self::assertCount(30, $result);
        self::assertSame('2021-06-01', $result[0]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2021-06-30', $result[29]->getDateTimeInstance()->format('Y-m-d'));
    }

    /**
     * @test
     */
    public function returnsSameDaysOnSecondCall(): void
    {
        $subject = new Month(06, 2021);

        self::assertSame($subject->getDays(), $subject->getDays());
    }

    /**
     * @test
     */
    public function providesDateTimeInstance(): void
    {
        $subject = new Month(02, 2018);

        self::assertSame('2018-02-01', $subject->getDateTimeInstance()->format('Y-m-d'));
    }

    /**
     * @test
     */
    public function returnsAsUrlArguments(): void
    {
        $subject = new Month(02, 2018);

        self::assertSame([
            'month' => 2,
            'year' => 2018,
        ], $subject->getAsUrlArgument());
    }

    /**
     * @test
     */
    public function returnsNotActiveIfAllWeeksAreInactive(): void
    {
        $subject = new Month(02, 2018);

        $week = $this->prophesize(Week::class);
        $week->isActive()->willReturn(false);
        $weeks = [$week->reveal()];
        $this->forceProperty($subject, 'weeks', $weeks);

        self::assertFalse($subject->isActive());
    }

    /**
     * @test
     */
    public function returnsActiveIfASingleWeekIsActive(): void
    {
        $subject = new Month(02, 2018);

        $week = $this->prophesize(Week::class);
        $week->isActive()->willReturn(true);
        $week2 = $this->prophesize(Week::class);
        $week2->isActive()->willReturn(false);
        $weeks = [$week->reveal(), $week2->reveal()];
        $this->forceProperty($subject, 'weeks', $weeks);

        self::assertTrue($subject->isActive());
    }
}
