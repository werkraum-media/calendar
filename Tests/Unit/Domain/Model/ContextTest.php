<?php

declare(strict_types=1);

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

namespace WerkraumMedia\Calendar\Tests\Unit\Domain\Model;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use WerkraumMedia\Calendar\Domain\Model\Context;

/**
 * @covers \WerkraumMedia\Calendar\Domain\Model\Context
 */
class ContextTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function canNotBeCreatedViaNew(): void
    {
        $this->expectError();
        $this->expectErrorMessage('Call to private WerkraumMedia\Calendar\Domain\Model\Context::__construct() from context \'WerkraumMedia\Calendar\Tests\Unit\Domain\Model\ContextTest\'');
        $subject = new Context();
    }

    /**
     * @test
     */
    public function canBeCreatedFromContentObjectRenderer(): void
    {
        $contentObjectRenderer = $this->prophesize(ContentObjectRenderer::class);
        $subject = Context::createFromContentObjectRenderer($contentObjectRenderer->reveal());

        self::assertInstanceOf(Context::class, $subject);
    }

    /**
     * @test
     */
    public function providesTableNameInheritedFromContentObjectRenderer(): void
    {
        $contentObjectRenderer = $this->prophesize(ContentObjectRenderer::class);
        $contentObjectRenderer->getCurrentTable()->willReturn('tx_calendar_example_table');
        $subject = Context::createFromContentObjectRenderer($contentObjectRenderer->reveal());

        self::assertSame('tx_calendar_example_table', $subject->getTableName());
    }

    /**
     * @test
     */
    public function providesDatabaseRowInheritedFromContentObjectRenderer(): void
    {
        $contentObjectRenderer = $this->prophesize(ContentObjectRenderer::class);
        $contentObjectRenderer->data = [
            'uid' => 10,
            'pid' => 1,
        ];
        $subject = Context::createFromContentObjectRenderer($contentObjectRenderer->reveal());

        self::assertSame([
            'uid' => 10,
            'pid' => 1,
        ], $subject->getDatabaseRow());
    }
}
