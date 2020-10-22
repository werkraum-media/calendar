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
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use WerkraumMedia\Calendar\Domain\Model\Day;
use WerkraumMedia\Calendar\Domain\Model\ForeignDataFactory;
use WerkraumMedia\Calendar\Domain\Model\IsDayActive;
use WerkraumMedia\Calendar\Domain\Model\NullDataFactory;
use WerkraumMedia\Calendar\Tests\ForcePropertyTrait;

/**
 * @covers WerkraumMedia\Calendar\Domain\Model\Day
 * @testdox A day
 */
class DayTest extends TestCase
{
    use ProphecyTrait;
    use ForcePropertyTrait;

    public function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $subject = new Day(
            new \DateTime()
        );

        self::assertInstanceOf(Day::class, $subject);
    }

    /**
     * @test
     */
    public function providesDateTimeInstance(): void
    {
        $dateTimeInstance = new \DateTime();
        $subject = new Day(
            $dateTimeInstance
        );

        self::assertInstanceOf(\DateTimeImmutable::class, $subject->getDateTimeInstance());
        self::assertSame($dateTimeInstance->format('U'), $subject->getDateTimeInstance()->format('U'));
    }

    /**
     * @test
     */
    public function providedDateTimeInstanceHasMidnight(): void
    {
        $dateTimeInstance = new \DateTime();
        $subject = new Day(
            $dateTimeInstance
        );

        self::assertSame('00:00:00', $subject->getDateTimeInstance()->format('H:i:s'));
    }

    /**
     * @test
     */
    public function providesItselfAsUrlArgument(): void
    {
        $subject = new Day(new \DateTime('2020-10-19'));

        self::assertSame(['day' => '2020-10-19'], $subject->getAsUrlArgument());
    }

    /**
     * @test
     */
    public function isNotActiveIfNoForeignDataWithInterfaceExists(): void
    {
        $subject = new Day(new \DateTime('2020-10-19'));

        $this->forceProperty($subject, 'initialized', true);

        self::assertFalse($subject->isActive());
    }

    /**
     * @test
     */
    public function isNotActiveIfForeignDataIsNotActive(): void
    {
        $subject = new Day(new \DateTime('2020-10-19'));

        $foreignData = $this->prophesize(IsDayActive::class);
        $foreignData->isActive(Argument::any())->willReturn(false);

        $this->forceProperty($subject, 'initialized', true);
        $this->forceProperty($subject, 'foreignData', $foreignData->reveal());

        self::assertFalse($subject->isActive());
    }

    /**
     * @test
     */
    public function initializesForeignDataViaFactory(): void
    {
        $subject = new Day(new \DateTime('2020-10-19'));

        $foreignData = $this->prophesize(IsDayActive::class);
        $foreignData->isActive(Argument::any())->willReturn(true);

        $factory = $this->prophesize(ForeignDataFactory::class);
        $factory->getData($subject)->willReturn($foreignData->reveal());

        GeneralUtility::addInstance(ForeignDataFactory::class, $factory->reveal());

        self::assertTrue($subject->isActive());
    }
}
