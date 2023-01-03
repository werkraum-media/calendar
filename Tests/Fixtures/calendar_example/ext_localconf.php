<?php

(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Calendar',
        'Example',
        [
            \WerkraumMedia\Calendar\Controller\Frontend\CalendarController::class => implode(',', [
                'day',
                'week',
                'month',
                'year',
            ]),
        ],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
})();
