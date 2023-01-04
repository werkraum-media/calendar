<?php

declare(strict_types=1);

/*
 * Copyright (C) 2023 Daniel Siepmann <coding@daniel-siepmann.de>
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

namespace WerkraumMedia\Calendar\Tests\Functional;

use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use WerkraumMedia\Calendar\Controller\Frontend\CalendarController;

/**
 * @coversNothing
 * @testdox Calendar controller renders with
 */
class CalendarControllerTest extends FunctionalTestCase
{
    protected $coreExtensionsToLoad = [
        'fluid_styled_content',
    ];

    protected $testExtensionsToLoad = [
        'typo3conf/ext/calendar',
        'typo3conf/ext/calendar/Tests/Fixtures/calendar_example',
    ];

    protected $pathsToLinkInTestInstance = [
        'typo3conf/ext/calendar/Tests/Fixtures/Sites' => 'typo3conf/sites',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importDataSet(__DIR__ . '/../Fixtures/BasicDatabase.xml');
    }

    /**
     * @test
     */
    public function modifiedVariablesForCurrentDay(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('modifiedVariable', $html);
    }

    /**
     * @test
     */
    public function customDataForCurrentDay(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString(date('d.m.Y'), $html);
        self::assertStringContainsString('exampleValue', $html);
    }

    /**
     * @test
     */
    public function customDataForProvidedDay(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[day][day]', '2020-11-03');
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('03.11.2020', $html);
        self::assertStringContainsString('exampleValue', $html);
    }

    /**
     * @test
     */
    public function modifiedVariablesForCurrentWeek(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'week');
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('modifiedVariable', $html);
    }

    /**
     * @test
     */
    public function customDataForCurrentWeek(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'week');
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString(date('W Y'), $html);
        self::assertStringContainsString('exampleValue', $html);
    }

    /**
     * @test
     */
    public function customDataForProvidedWeek(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'week');
        $request = $request->withQueryParameter('tx_calendar_example[week][week]', '02');
        $request = $request->withQueryParameter('tx_calendar_example[week][year]', '2020');
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('02 2020', $html);
        self::assertStringContainsString('exampleValue', $html);
    }

    /**
     * @test
     */
    public function modifiedVariablesForCurrentMonth(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'month');
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('modifiedVariable', $html);
    }

    /**
     * @test
     */
    public function customDataForCurrentMonth(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'month');
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString(strftime('%B %Y'), $html);
        self::assertStringContainsString('exampleValue', $html);
    }

    /**
     * @test
     */
    public function customDataForProvidedMonth(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'month');
        $request = $request->withQueryParameter('tx_calendar_example[month][month]', '11');
        $request = $request->withQueryParameter('tx_calendar_example[month][year]', '2020');
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('November 2020', $html);
        self::assertStringContainsString('exampleValue', $html);
    }

    /**
     * @test
     */
    public function modifiedVariablesForCurrentYear(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'year');
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('modifiedVariable', $html);
    }

    /**
     * @test
     */
    public function customDataForCurrentYear(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'year');
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString(date('Y'), $html);
        self::assertStringContainsString('exampleValue', $html);
    }

    /**
     * @test
     */
    public function customDataForProvidedYear(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'year');
        $request = $request->withQueryParameter('tx_calendar_example[year][year]', '2020');
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('2020', $html);
        self::assertStringContainsString('exampleValue', $html);
    }
}
