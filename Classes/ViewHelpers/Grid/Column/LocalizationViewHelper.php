<?php
namespace Fab\VidiFrontend\ViewHelpers\Grid\Column;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Fab\VidiFrontend\Tca\FrontendTcaService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper for rendering the localization.
 */
class LocalizationViewHelper extends AbstractViewHelper {

	/**
	 * Render the columns of the grid.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';
		$labels = array(
			'processing',
			'search',
			'lengthMenu',
			'info',
			'infoEmpty',
			'infoFiltered',
			'loadingRecords',
			'zeroRecords',
			'emptyTable',
		);

		/**
		 *


		"processing":     "",
		"search":         "",
		"lengthMenu":     "",
		"info":           "",
		"infoEmpty":      "",
		"infoFiltered":   "",
		"infoPostFix":    "",
		"loadingRecords": "",
		"zeroRecords":    "",
		"emptyTable":     "",
		"paginate": {
			"first":      "",
			"previous":   "",
			"next":       "",
			"last":       ""
		},
		"aria": {
			"sortAscending":  "",
			"sortDescending": ""
		}

		 *
		 *
		 */
		foreach($labels as $label) {
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
	 * @return \TYPO3\CMS\Vidi\Resolver\FieldPathResolver
	 */
	protected function getFieldPathResolver () {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Resolver\FieldPathResolver');
	}
}
