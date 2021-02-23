<?php

namespace WerkraumMedia\Calendar\Tests\Unit\Controller\Frontend;

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
use Prophecy\Argument as ProphetArgument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\Argument;
use TYPO3\CMS\Extbase\Mvc\Controller\Arguments;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;
use WerkraumMedia\Calendar\Controller\Frontend\CalendarController;
use WerkraumMedia\Calendar\Domain\Model\Day;
use WerkraumMedia\Calendar\Domain\Model\Month;
use WerkraumMedia\Calendar\Domain\Model\Week;
use WerkraumMedia\Calendar\Domain\Model\Year;
use WerkraumMedia\Calendar\Events\AssignTemplateVariables;
use WerkraumMedia\Calendar\Tests\ForcePropertyTrait;

/**
 * @covers WerkraumMedia\Calendar\Controller\Frontend\CalendarController
 * @testdox The calendar controller
 */
class CalendarControllerTest extends TestCase
{
    use ProphecyTrait;
    use ForcePropertyTrait;

    /**
     * @test
     */
    public function setsCurrentYearAsDefaultArgument(): void
    {
        $subject = new CalendarController();

        $arguments = $this->allowsMappingOfAllPropertiesForArgument('year')['arguments'];

        $request = $this->prophesize(Request::class);
        $request->hasArgument('year')->willReturn(false);
        $request->setArguments([
            'year' => [
                'year' => date('Y'),
            ],
        ])->shouldBeCalled();

        $this->forceProperty($subject, 'request', $request->reveal());
        $this->forceProperty($subject, 'arguments', $arguments->reveal());

        $subject->initializeYearAction();
        $request->checkProphecyMethodsPredictions();
    }

    /**
     * @test
     */
    public function allowsYearToBeMapped(): void
    {
        $subject = new CalendarController();

        $arguments = $this->allowsMappingOfAllPropertiesForArgument('year')['arguments'];

        $request = $this->prophesize(Request::class);
        $request->hasArgument('year')->willReturn(true);

        $this->forceProperty($subject, 'request', $request->reveal());
        $this->forceProperty($subject, 'arguments', $arguments->reveal());

        $subject->initializeYearAction();
        $arguments->checkProphecyMethodsPredictions();
    }

    /**
     * @test
     */
    public function addsYearToView(): void
    {
        $subject = new CalendarController();

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(ProphetArgument::type(AssignTemplateVariables::class))->shouldBeCalled();
        $subject->injectEventDispatcher($eventDispatcher->reveal());

        $year = $this->prophesize(Year::class);
        $view = $this->prophesize(ViewInterface::class);
        $view->assignMultiple([
            'year' => $year->reveal(),
        ])->shouldBeCalled();

        $this->forceProperty($subject, 'view', $view->reveal());

        $subject->yearAction($year->reveal());
        $view->checkProphecyMethodsPredictions();
    }

    /**
     * @test
     */
    public function setsCurrentMonthAsDefaultArgument(): void
    {
        $subject = new CalendarController();

        $arguments = $this->allowsMappingOfAllPropertiesForArgument('month')['arguments'];

        $request = $this->prophesize(Request::class);
        $request->hasArgument('month')->willReturn(false);
        $request->setArguments([
            'month' => [
                'month' => date('m'),
                'year' => date('Y'),
            ],
        ])->shouldBeCalled();

        $this->forceProperty($subject, 'request', $request->reveal());
        $this->forceProperty($subject, 'arguments', $arguments->reveal());

        $subject->initializeMonthAction();
        $request->checkProphecyMethodsPredictions();
    }

    /**
     * @test
     */
    public function allowsMonthToBeMapped(): void
    {
        $subject = new CalendarController();

        $arguments = $this->allowsMappingOfAllPropertiesForArgument('month')['arguments'];

        $request = $this->prophesize(Request::class);
        $request->hasArgument('month')->willReturn(true);

        $this->forceProperty($subject, 'request', $request->reveal());
        $this->forceProperty($subject, 'arguments', $arguments->reveal());

        $subject->initializeMonthAction();
        $arguments->checkProphecyMethodsPredictions();
    }

    /**
     * @test
     */
    public function addsMonthToView(): void
    {
        $subject = new CalendarController();

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(ProphetArgument::type(AssignTemplateVariables::class))->shouldBeCalled();
        $subject->injectEventDispatcher($eventDispatcher->reveal());

        $month = $this->prophesize(Month::class);
        $view = $this->prophesize(ViewInterface::class);
        $view->assignMultiple([
            'month' => $month->reveal(),
        ])->shouldBeCalled();

        $this->forceProperty($subject, 'view', $view->reveal());

        $subject->monthAction($month->reveal());
        $view->checkProphecyMethodsPredictions();
    }

    /**
     * @test
     */
    public function setsCurrentWeekAsDefaultArgument(): void
    {
        $subject = new CalendarController();

        $arguments = $this->allowsMappingOfAllPropertiesForArgument('week')['arguments'];

        $request = $this->prophesize(Request::class);
        $request->hasArgument('week')->willReturn(false);
        $request->setArguments([
            'week' => [
                'week' => date('W'),
                'year' => date('Y'),
            ],
        ])->shouldBeCalled();

        $this->forceProperty($subject, 'request', $request->reveal());
        $this->forceProperty($subject, 'arguments', $arguments->reveal());

        $subject->initializeWeekAction();
        $request->checkProphecyMethodsPredictions();
    }

    /**
     * @test
     */
    public function allowsWeekToBeMapped(): void
    {
        $subject = new CalendarController();

        $arguments = $this->allowsMappingOfAllPropertiesForArgument('week')['arguments'];

        $request = $this->prophesize(Request::class);
        $request->hasArgument('week')->willReturn(true);

        $this->forceProperty($subject, 'request', $request->reveal());
        $this->forceProperty($subject, 'arguments', $arguments->reveal());

        $subject->initializeWeekAction();
        $arguments->checkProphecyMethodsPredictions();
    }

    /**
     * @test
     */
    public function addsWeekToView(): void
    {
        $subject = new CalendarController();

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(ProphetArgument::type(AssignTemplateVariables::class))->shouldBeCalled();
        $subject->injectEventDispatcher($eventDispatcher->reveal());

        $week = $this->prophesize(Week::class);
        $view = $this->prophesize(ViewInterface::class);
        $view->assignMultiple([
            'week' => $week->reveal(),
        ])->shouldBeCalled();

        $this->forceProperty($subject, 'view', $view->reveal());

        $subject->weekAction($week->reveal());
        $view->checkProphecyMethodsPredictions();
    }

    /**
     * @test
     */
    public function setsCurrentDayAsDefaultArgument(): void
    {
        $subject = new CalendarController();

        $prophecies = $this->allowsMappingOfAllPropertiesForArgument('day');
        $propertyConfiguration = $prophecies['propertyMappingConfiguration'];
        $arguments = $prophecies['arguments'];

        $request = $this->prophesize(Request::class);
        $request->hasArgument('day')->willReturn(false);
        $request->setArguments(ProphetArgument::that(function (array $arguments) {
            return count($arguments) === 1
                && isset($arguments['day'])
                && $arguments['day'] instanceof \DateTimeImmutable
                && $arguments['day']->format('Y-m-d') === date('Y-m-d')
                ;
        }))->shouldBeCalled();

        $configuration = $this->prophesize(PropertyMappingConfiguration::class);
        $configuration->setTypeConverterOption(
            '',
            '',
            'Y-m-d'
        );
        $propertyConfiguration->forProperty('day')->willReturn($configuration);

        $this->forceProperty($subject, 'request', $request->reveal());
        $this->forceProperty($subject, 'arguments', $arguments->reveal());

        $subject->initializeDayAction();
        $request->checkProphecyMethodsPredictions();
    }

    /**
     * @test
     */
    public function configuredMappingForDay(): void
    {
        $subject = new CalendarController();

        $prophecies = $this->allowsMappingOfAllPropertiesForArgument('day');
        $propertyConfiguration = $prophecies['propertyMappingConfiguration'];
        $arguments = $prophecies['arguments'];

        $request = $this->prophesize(Request::class);
        $request->hasArgument('day')->willReturn(true);

        $configuration = $this->prophesize(PropertyMappingConfiguration::class);
        $configuration->setTypeConverterOption(
            '',
            '',
            'Y-m-d'
        );
        $propertyConfiguration->forProperty('day')->willReturn($configuration);

        $this->forceProperty($subject, 'request', $request->reveal());
        $this->forceProperty($subject, 'arguments', $arguments->reveal());

        $subject->initializeDayAction();
        $arguments->checkProphecyMethodsPredictions();
    }

    /**
     * @test
     */
    public function addsDayToView(): void
    {
        $subject = new CalendarController();

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(ProphetArgument::type(AssignTemplateVariables::class))->shouldBeCalled();
        $subject->injectEventDispatcher($eventDispatcher->reveal());

        $day = $this->prophesize(Day::class);
        $view = $this->prophesize(ViewInterface::class);
        $view->assignMultiple([
            'day' => $day->reveal(),
        ])->shouldBeCalled();

        $this->forceProperty($subject, 'view', $view->reveal());

        $subject->dayAction($day->reveal());
        $view->checkProphecyMethodsPredictions();
    }

    private function allowsMappingOfAllPropertiesForArgument(string $argumentName): array
    {
        $propertyMappingConfiguration = $this->prophesize(PropertyMappingConfiguration::class);
        $propertyMappingConfiguration->allowAllProperties()->shouldBeCalled();

        $argument = $this->prophesize(Argument::class);
        $argument->getPropertyMappingConfiguration()->willReturn($propertyMappingConfiguration);

        $arguments = $this->prophesize(Arguments::class);
        $arguments->getArgument($argumentName)->willReturn($argument->reveal());

        return [
            'propertyMappingConfiguration' => $propertyMappingConfiguration,
            'argument' => $argument,
            'arguments' => $arguments,
        ];
    }
}
