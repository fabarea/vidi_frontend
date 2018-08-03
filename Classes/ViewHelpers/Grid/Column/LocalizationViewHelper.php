<?php

namespace Fab\VidiFrontend\ViewHelpers\Grid\Column;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper for rendering the localization.
 */
class LocalizationViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Render the columns of the grid.
     *
     * @return string
     */
    public function render()
    {
        $output = '';

        // The list of labels.
        $labels = array(
            'processing',
            'search',
            'searchPlaceholder',
            'lengthMenu',
            'info',
            'infoEmpty',
            'infoFiltered',
            'loadingRecords',
            'zeroRecords',
            'rows.all',
            'rows.selected',
        );

        foreach ($labels as $label) {
            $output .= sprintf('labels["%s"] = "%s";' . PHP_EOL,
                $label,
                LocalizationUtility::translate('grid.' . $label, 'vidi_frontend')
            );
        }

        // Add pagination labels.
        $output .= sprintf('labels["paginate"] = {%s, %s, %s, %s};' . PHP_EOL,
            sprintf('"first": "%s"', LocalizationUtility::translate('grid.paginate.first', 'vidi_frontend')),
            sprintf('"previous": "%s"', LocalizationUtility::translate('grid.paginate.previous', 'vidi_frontend')),
            sprintf('"next": "%s"', LocalizationUtility::translate('grid.paginate.next', 'vidi_frontend')),
            sprintf('"last": "%s"', LocalizationUtility::translate('grid.paginate.last', 'vidi_frontend'))
        );

        // Add aria labels.
        $output .= sprintf('labels["aria"] = {%s, %s};' . PHP_EOL,
            sprintf('"sortAscending": "%s"', LocalizationUtility::translate('grid.aria.sortAscending', 'vidi_frontend')),
            sprintf('"sortAscending": "%s"', LocalizationUtility::translate('grid.aria.sortDescending', 'vidi_frontend'))
        );
        return $output;
    }

    /**
     * @return object|\Fab\Vidi\Resolver\FieldPathResolver
     */
    protected function getFieldPathResolver()
    {
        return GeneralUtility::makeInstance(\Fab\Vidi\Resolver\FieldPathResolver::class);
    }
}
