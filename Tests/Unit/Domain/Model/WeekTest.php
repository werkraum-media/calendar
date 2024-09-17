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
use WerkraumMedia\Calendar\Domain\Model\Day;
use WerkraumMedia\Calendar\Domain\Model\Week;
use WerkraumMedia\Calendar\Tests\ForcePropertyTrait;

#[TestDox('A week')]
class WeekTest extends TestCase
{
    use ForcePropertyTrait;

    #[Test]
    public function canBeCreated(): void
    {
        $subject = new Week(1, 2020);

        self::assertInstanceOf(Week::class, $subject);
    }

    #[Test]
    public function returnsSevenDays(): void
    {
        $subject = new Week(1, 2020);

        self::assertCount(7, $subject->getDays());
    }

    #[Test]
    public function returnsSameDaysOnSecondCall(): void
    {
        $subject = new Week(1, 2020);

        self::assertSame($subject->getDays(), $subject->getDays());
    }

    #[Test]
    public function returnsDaysForWeek1(): void
    {
        $subject = new Week(1, 2020);
        $days = $subject->getDays();

        self::assertSame('2019-12-30', $days[0]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2019-12-31', $days[1]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2020-01-01', $days[2]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2020-01-02', $days[3]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2020-01-03', $days[4]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2020-01-04', $days[5]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2020-01-05', $days[6]->getDateTimeInstance()->format('Y-m-d'));
    }

    #[Test]
    public function returnsDaysForWeek53(): void
    {
        $subject = new Week(53, 2020);
        $days = $subject->getDays();

        self::assertSame('2020-12-28', $days[0]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2020-12-29', $days[1]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2020-12-30', $days[2]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2020-12-31', $days[3]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2021-01-01', $days[4]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2021-01-02', $days[5]->getDateTimeInstance()->format('Y-m-d'));
        self::assertSame('2021-01-03', $days[6]->getDateTimeInstance()->format('Y-m-d'));
    }

    #[Test]
    public function returnsPreviousWeekForSameYear(): void
    {
        $subject = new Week(2, 2020);

        self::assertSame('01', $subject->getPreviousWeek()->getDateTimeInstance()->format('W'));
        self::assertSame('2020', $subject->getPreviousWeek()->getDateTimeInstance()->format('Y'));
    }

    #[Test]
    public function returnsPreviousWeekForYearSwitchFrom2019To2018(): void
    {
        $subject = new Week(1, 2019);

        self::assertSame('52', $subject->getPreviousWeek()->getDateTimeInstance()->format('W'));
        self::assertSame('2018', $subject->getPreviousWeek()->getDateTimeInstance()->format('Y'));
    }

    #[Test]
    public function returnsPreviousWeekForYearSwitchFrom2018To2017(): void
    {
        $subject = new Week(1, 2018);

        self::assertSame('52', $subject->getPreviousWeek()->getDateTimeInstance()->format('W'));
        self::assertSame('2017', $subject->getPreviousWeek()->getDateTimeInstance()->format('Y'));
    }

    #[Test]
    public function returnsPreviousWeekForPreviousYear(): void
    {
        $subject = new Week(1, 2021);

        self::assertSame('53', $subject->getPreviousWeek()->getDateTimeInstance()->format('W'));
        self::assertSame('2020', $subject->getPreviousWeek()->getDateTimeInstance()->format('Y'));
    }

    #[Test]
    public function returnsNextWeekForSameYear(): void
    {
        $subject = new Week(1, 2020);

        self::assertSame('02', $subject->getNextWeek()->getDateTimeInstance()->format('W'));
        self::assertSame('2020', $subject->getNextWeek()->getDateTimeInstance()->format('Y'));
    }

    #[Test]
    public function returnsNextWeekForNextYear(): void
    {
        $subject = new Week(53, 2020);

        self::assertSame('01', $subject->getNextWeek()->getDateTimeInstance()->format('W'));
        self::assertSame('2021', $subject->getNextWeek()->getDateTimeInstance()->format('Y'));
    }

    #[Test]
    public function returnsNextWeekForNextYearFrom2018To2019(): void
    {
        $subject = new Week(52, 2018);

        self::assertSame('01', $subject->getNextWeek()->getDateTimeInstance()->format('W'));
        self::assertSame('2019', $subject->getNextWeek()->getDateTimeInstance()->format('Y'));
    }

    #[Test]
    public function providesDateTimeInstance(): void
    {
        $subject = new Week(52, 2018);

        self::assertSame('52', $subject->getDateTimeInstance()->format('W'));
        self::assertSame('2018-12-27 Thursday', $subject->getDateTimeInstance()->format('Y-m-d l'));
    }

    #[Test]
    public function providesItselfAsUrlArgument(): void
    {
        $subject = new Week(52, 2018);

        self::assertSame([
            'week' => 52,
            'year' => 2018,
        ], $subject->getAsUrlArgument());
    }

    #[Test]
    public function returnsNotActiveIfAllDaysAreInactive(): void
    {
        $subject = new Week(02, 2018);

        $day = $this->createStub(Day::class);
        $day->method('isActive')->willReturn(false);
        $days = [$day];
        $this->forceProperty($subject, 'days', $days);

        self::assertFalse($subject->isActive());
    }

    #[Test]
    public function returnsActiveIfASingleDayActive(): void
    {
        $subject = new Week(02, 2018);

        $day = $this->createStub(Day::class);
        $day->method('isActive')->willReturn(true);
        $day2 = $this->createStub(Day::class);
        $day2->method('isActive')->willReturn(false);
        $days = [$day, $day2];
        $this->forceProperty($subject, 'days', $days);

        self::assertTrue($subject->isActive());
    }
}
