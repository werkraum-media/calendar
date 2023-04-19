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

use Codappix\Typo3PhpDatasets\TestingFramework;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use TYPO3\CMS\Core\Localization\DateFormatter;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[TestDox('Calendar controller renders with')]
class CalendarControllerTest extends FunctionalTestCase
{
    use TestingFramework;

    protected array $coreExtensionsToLoad = [
        'fluid_styled_content',
    ];

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/calendar',
        'typo3conf/ext/calendar/Tests/Fixtures/calendar_example',
    ];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/calendar/Tests/Fixtures/Sites' => 'typo3conf/sites',
    ];

    protected array $configurationToUseInTestInstance = [
        'FE' => [
            'cacheHash' => [
                'excludedParameters' => [
                    '^tx_calendar_example[',
                    '^typoScriptDefaults',
                ],
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../Fixtures/BasicDatabase.php');
    }

    #[Test]
    public function modifiedVariablesForCurrentDay(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('modifiedVariable', $html);
    }

    #[Test]
    public function pluginNameForCurrentDay(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('PluginName: Example', $html);
    }

    #[Test]
    public function contextForCurrentDay(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('Table: pages', $html);
        self::assertStringContainsString('Title: Page Title', $html);
    }

    #[Test]
    public function customDataForCurrentDay(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString(date('d.m.Y'), $html);
        self::assertStringContainsString('exampleValue', $html);
    }

    #[Test]
    public function configuredDay(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('typoScriptDefaults', '1');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('03.11.1988', $html);
    }

    #[Test]
    public function providedDay(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[day][day]', '2020-11-03');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('03.11.2020', $html);
    }

    #[Test]
    public function modifiedVariablesForCurrentWeek(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'week');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('modifiedVariable', $html);
    }

    #[Test]
    public function pluginNameForCurrentWeek(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'week');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('PluginName: Example', $html);
    }

    #[Test]
    public function contextForCurrentWeek(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'week');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('Table: pages', $html);
        self::assertStringContainsString('Title: Page Title', $html);
    }

    #[Test]
    public function customDataForCurrentWeek(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'week');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString(date('W Y'), $html);
        self::assertStringContainsString('exampleValue', $html);
    }

    #[Test]
    public function configuredWeek(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'week');
        $request = $request->withQueryParameter('typoScriptDefaults', '1');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('12 1988', $html);
    }

    #[Test]
    public function providedWeek(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'week');
        $request = $request->withQueryParameter('tx_calendar_example[week][week]', '02');
        $request = $request->withQueryParameter('tx_calendar_example[week][year]', '2020');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('02 2020', $html);
    }

    #[Test]
    public function modifiedVariablesForCurrentMonth(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'month');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('modifiedVariable', $html);
    }

    #[Test]
    public function pluginNameForCurrentMonth(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'month');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('PluginName: Example', $html);
    }

    #[Test]
    public function contextForCurrentMonth(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'month');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('Table: pages', $html);
        self::assertStringContainsString('Title: Page Title', $html);
    }

    #[Test]
    public function customDataForCurrentMonth(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'month');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString((new DateFormatter())->strftime('%B %Y', 'now', 'de-DE'), $html);
        self::assertStringContainsString('exampleValue', $html);
    }

    #[Test]
    public function configuredMonth(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'month');
        $request = $request->withQueryParameter('typoScriptDefaults', '1');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('November 1988', $html);
    }

    #[Test]
    public function providedMonth(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'month');
        $request = $request->withQueryParameter('tx_calendar_example[month][month]', '11');
        $request = $request->withQueryParameter('tx_calendar_example[month][year]', '2020');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('November 2020', $html);
        self::assertStringContainsString('exampleValue', $html);
    }

    #[Test]
    public function modifiedVariablesForCurrentYear(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'year');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('modifiedVariable', $html);
    }

    #[Test]
    public function pluginNameForCurrentYear(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'year');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('PluginName: Example', $html);
    }

    #[Test]
    public function contextForCurrentYear(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'year');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('Table: pages', $html);
        self::assertStringContainsString('Title: Page Title', $html);
    }

    #[Test]
    public function customDataForCurrentYear(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'year');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString(date('Y'), $html);
        self::assertStringContainsString('exampleValue', $html);
    }

    #[Test]
    public function configuredYear(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'year');
        $request = $request->withQueryParameter('typoScriptDefaults', '1');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('1988', $html);
    }

    #[Test]
    public function providedYear(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_calendar_example[action]', 'year');
        $request = $request->withQueryParameter('tx_calendar_example[year][year]', '2020');
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertStringContainsString('2020', $html);
        self::assertStringContainsString('exampleValue', $html);
    }
}
