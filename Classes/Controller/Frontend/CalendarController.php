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

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use WerkraumMedia\Calendar\Domain\Model\Context;
use WerkraumMedia\Calendar\Domain\Model\ContextSpecificFactory;
use WerkraumMedia\Calendar\Domain\Model\Day;
use WerkraumMedia\Calendar\Domain\Model\ForeignDataFactory;
use WerkraumMedia\Calendar\Domain\Model\Month;
use WerkraumMedia\Calendar\Domain\Model\Week;
use WerkraumMedia\Calendar\Domain\Model\Year;
use WerkraumMedia\Calendar\Events\AssignTemplateVariables;

final class CalendarController extends ActionController
{
    /**
     * @var ForeignDataFactory
     */
    private $foreignDataFactory;

    /**
     * @var TypoScriptService
     */
    private $typoScriptService;

    public function __construct(
        ForeignDataFactory $foreignDataFactory,
        TypoScriptService $typoScriptService
    ) {
        $this->foreignDataFactory = $foreignDataFactory;
        $this->typoScriptService = $typoScriptService;
    }

    public function initializeAction(): void
    {
        if ($this->foreignDataFactory instanceof ContextSpecificFactory) {
            $this->foreignDataFactory->setContext(
                Context::createFromContentObjectRenderer($this->getContentObjectRenderer())
            );
        }
    }

    public function initializeYearAction(): void
    {
        if ($this->request->hasArgument('year') === false) {
            $this->request = $this->request->withArguments([
                'year' => [
                    'year' => $this->getDefaultArgumentValue('year'),
                ],
            ]);
        }

        $this->arguments->getArgument('year')
            ->getPropertyMappingConfiguration()
            ->allowAllProperties();
    }

    /**
     * @Extbase\IgnoreValidation("year")
     */
    public function yearAction(Year $year): ResponseInterface
    {
        $this->assignVariables([
            'year' => $year,
        ]);

        return $this->htmlResponse();
    }

    public function initializeMonthAction(): void
    {
        if ($this->request->hasArgument('month') === false) {
            $this->request = $this->request->withArguments([
                'month' => [
                    'month' => $this->getDefaultArgumentValue('month'),
                    'year' => $this->getDefaultArgumentValue('year'),
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
    public function monthAction(Month $month): ResponseInterface
    {
        $this->assignVariables([
            'month' => $month,
        ]);

        return $this->htmlResponse();
    }

    public function initializeWeekAction(): void
    {
        if ($this->request->hasArgument('week') === false) {
            $this->request = $this->request->withArguments([
                'week' => [
                    'week' => $this->getDefaultArgumentValue('week'),
                    'year' => $this->getDefaultArgumentValue('year'),
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
    public function weekAction(Week $week): ResponseInterface
    {
        $this->assignVariables([
            'week' => $week,
        ]);

        return $this->htmlResponse();
    }

    public function initializeDayAction(): void
    {
        if ($this->request->hasArgument('day') === false) {
            $this->request = $this->request->withArguments([
                'day' => [
                    'day' => $this->getDefaultArgumentValue('day'),
                ],
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
    public function dayAction(Day $day): ResponseInterface
    {
        $this->assignVariables([
            'day' => $day,
        ]);

        return $this->htmlResponse();
    }

    private function assignVariables(array $variables): void
    {
        $event = GeneralUtility::makeInstance(
            AssignTemplateVariables::class,
            $variables,
            $this->request->getPluginName()
        );
        $this->eventDispatcher->dispatch($event);
        $this->view->assignMultiple($event->getVariables());
    }

    /**
     * Checks for TypoScript and transforms TypoScript into expected value.
     * Allows to define defaults other than "now" for arguments.
     */
    private function getDefaultArgumentValue(string $argumentName): string
    {
        $arguments = $this->typoScriptService->convertPlainArrayToTypoScriptArray(
            $this->settings['arguments'] ?? []
        );

        $fallbackValues = [
            'year' => date('Y'),
            'month' => date('m'),
            'week' => date('W'),
            'day' => date('Y-m-d'),
        ];

        $value = $this->getContentObjectRenderer()->stdWrapValue(
            $argumentName,
            $arguments,
            $fallbackValues[$argumentName]
        );

        return (string)$value;
    }

    private function getContentObjectRenderer(): ContentObjectRenderer
    {
        $contentObjectRenderer = $this->request->getAttribute('currentContentObject');

        if (! $contentObjectRenderer instanceof ContentObjectRenderer) {
            throw new RuntimeException('Could not fetch currentContentObject from request.', 1726490796);
        }

        return $contentObjectRenderer;
    }
}
