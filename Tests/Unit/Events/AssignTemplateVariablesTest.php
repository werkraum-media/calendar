<?php

namespace WerkraumMedia\Calendar\Tests\Unit\Events;

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
use PHPUnit\Framework\TestCase;
use WerkraumMedia\Calendar\Events\AssignTemplateVariables;

class AssignTemplateVariablesTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $subject = new AssignTemplateVariables(
            [],
            ''
        );

        self::assertInstanceOf(AssignTemplateVariables::class, $subject);
    }

    #[Test]
    public function returnsPluginName(): void
    {
        $subject = new AssignTemplateVariables(
            [],
            'Example'
        );

        $result = $subject->getPluginName();

        self::assertSame('Example', $result);
    }

    #[Test]
    public function returnsVariables(): void
    {
        $subject = new AssignTemplateVariables(
            [
                'variable1' => 'value1',
            ],
            ''
        );

        $result = $subject->getVariables();

        self::assertSame([
            'variable1' => 'value1',
        ], $result);
    }

    #[Test]
    public function newVariablesOverwriteExistingVariables(): void
    {
        $subject = new AssignTemplateVariables(
            [
                'variable1' => 'value1',
            ],
            ''
        );

        $subject->setVariables([
            'variable2' => 'value2',
        ]);

        $result = $subject->getVariables();

        self::assertSame([
            'variable2' => 'value2',
        ], $result);
    }
}
