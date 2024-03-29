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

namespace WerkraumMedia\Calendar\Domain\Model;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class Context
{
    /**
     * @var string
     */
    protected $tableName = '';

    /**
     * @var array
     */
    protected $databaseRow = [];

    private function __construct()
    {
    }

    public static function createFromContentObjectRenderer(
        ContentObjectRenderer $contentObjectRenderer
    ): self {
        $instance = new self();

        $instance->tableName = $contentObjectRenderer->getCurrentTable();
        $instance->databaseRow = $contentObjectRenderer->data;

        return $instance;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getDatabaseRow(): array
    {
        return $this->databaseRow;
    }
}
