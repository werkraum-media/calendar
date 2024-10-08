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

namespace WerkraumMedia\CalendarExample\Domain;

use WerkraumMedia\Calendar\Domain\Model\Context;
use WerkraumMedia\Calendar\Domain\Model\ContextSpecificFactory;
use WerkraumMedia\Calendar\Domain\Model\Day;
use WerkraumMedia\Calendar\Domain\Model\ForeignDataFactory;

class ExampleDataFactory implements ForeignDataFactory, ContextSpecificFactory
{
    /**
     * @var Context
     */
    private $context;

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    public function getData(Day $day): mixed
    {
        return [
            'exampleKey' => 'exampleValue',
            'context' => [
                'tableName' => $this->context->getTableName(),
                'databaseRow' => $this->context->getDatabaseRow(),
            ],
        ];
    }
}
