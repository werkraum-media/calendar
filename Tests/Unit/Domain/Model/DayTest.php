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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WerkraumMedia\Calendar\Domain\Model\Day;
use WerkraumMedia\Calendar\Domain\Model\ForeignDataFactory;
use WerkraumMedia\Calendar\Domain\Model\IsDayActive;
use WerkraumMedia\Calendar\Tests\ForcePropertyTrait;

#[TestDox('A day')]
class DayTest extends TestCase
{
    use ForcePropertyTrait;

    public function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    #[Test]
    public function canBeCreated(): void
    {
        $subject = new Day(
            new \DateTime()
        );

        self::assertInstanceOf(Day::class, $subject);
    }

    #[Test]
    public function providesDateTimeInstance(): void
    {
        $dateTimeInstance = new \DateTime();
        $subject = new Day(
            $dateTimeInstance
        );

        self::assertInstanceOf(\DateTimeImmutable::class, $subject->getDateTimeInstance());
    }

    #[Test]
    public function providedDateTimeInstanceHasExpectedDay(): void
    {
        $dateTimeInstance = new \DateTime();
        $subject = new Day(
            $dateTimeInstance
        );

        self::assertSame($dateTimeInstance->format('d.m.Y'), $subject->getDateTimeInstance()->format('d.m.Y'));
    }

    #[Test]
    public function providedDateTimeInstanceHasMidnight(): void
    {
        $dateTimeInstance = new \DateTime();
        $subject = new Day(
            $dateTimeInstance
        );

        self::assertSame('00:00:00', $subject->getDateTimeInstance()->format('H:i:s'));
    }

    #[Test]
    public function providesItselfAsUrlArgument(): void
    {
        $subject = new Day(new \DateTime('2020-10-19'));

        self::assertSame(['day' => '2020-10-19'], $subject->getAsUrlArgument());
    }

    #[Test]
    public function isNotActiveIfNoForeignDataWithInterfaceExists(): void
    {
        $subject = new Day(new \DateTime('2020-10-19'));

        $this->forceProperty($subject, 'initialized', true);

        self::assertFalse($subject->isActive());
    }

    #[Test]
    public function isNotActiveIfForeignDataIsNotActive(): void
    {
        $subject = new Day(new \DateTime('2020-10-19'));

        $foreignData = $this->createStub(IsDayActive::class);
        $foreignData->method('isActive')->willReturn(false);

        $this->forceProperty($subject, 'initialized', true);
        $this->forceProperty($subject, 'foreignData', $foreignData);

        self::assertFalse($subject->isActive());
    }

    #[Test]
    public function initializesForeignDataViaFactory(): void
    {
        $subject = new Day(new \DateTime('2020-10-19'));

        $foreignData = $this->createStub(IsDayActive::class);
        $foreignData->method('isActive')->willReturn(true);

        $factory = $this->createStub(ForeignDataFactory::class);
        $factory->method('getData')->willReturn($foreignData);

        GeneralUtility::addInstance(ForeignDataFactory::class, $factory);

        self::assertTrue($subject->isActive());
    }
}
