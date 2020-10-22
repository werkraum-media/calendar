<?php

namespace WerkraumMedia\Calendar\Controller\Frontend;

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

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;
use WerkraumMedia\Calendar\Domain\Model\Day;
use WerkraumMedia\Calendar\Domain\Model\Month;
use WerkraumMedia\Calendar\Domain\Model\Week;

class CalendarController extends ActionController
{
    public function initializeMonthAction()
    {
        if ($this->request->hasArgument('month') === false) {
            $this->request->setArguments([
                'month' => [
                    'month' => date('m'),
                    'year' => date('Y'),
                ],
            ]);
        }

        $this->arguments->getArgument('month')
            ->getPropertyMappingConfiguration()
            ->allowAllProperties();
    }

    /**
     * @Extbase\IgnoreValidation("month")
     */
    public function monthAction(Month $month)
    {
        $this->view->assignMultiple([
            'month' => $month,
        ]);
    }

    public function initializeWeekAction()
    {
        if ($this->request->hasArgument('week') === false) {
            $this->request->setArguments([
                'week' => [
                    'week' => date('W'),
                    'year' => date('Y'),
                ],
            ]);
        }

        $this->arguments->getArgument('week')
            ->getPropertyMappingConfiguration()
            ->allowAllProperties();
    }

    /**
     * @Extbase\IgnoreValidation("week")
     */
    public function weekAction(Week $week)
    {
        $this->view->assignMultiple([
            'week' => $week,
        ]);
    }

    public function initializeDayAction()
    {
        if ($this->request->hasArgument('day') === false) {
            $this->request->setArguments([
                'day' => new \DateTimeImmutable(),
            ]);
        }

        $propertyMappingConfiguration = $this->arguments->getArgument('day')
            ->getPropertyMappingConfiguration();

        $propertyMappingConfiguration->allowAllProperties();
        $propertyMappingConfiguration
            ->forProperty('day')
            ->setTypeConverterOption(
                DateTimeConverter::class,
                DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'Y-m-d'
            );
    }

    /**
     * @Extbase\IgnoreValidation("day")
     */
    public function dayAction(Day $day)
    {
        $this->view->assignMultiple([
            'day' => $day,
        ]);
    }
}
