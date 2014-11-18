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

use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper for rendering configuration that will be consumed by Javascript
 */
class ConfigurationViewHelper extends AbstractViewHelper {

	/**
	 * Render the columns of the grid.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';
		$dataType = $this->templateVariableContainer->get('dataType');

		foreach(FrontendTca::grid($dataType)->getFields() as $fieldNameAndPath => $configuration) {

			// mData vs columnName
			// -------------------
			// mData: internal name of DataTable plugin and can not contains a path, e.g. metadata.title
			// columnName: whole field name with path
			$output .= sprintf('columns.push({ "data": "%s", "sortable": %s, "visible": %s, "width": "%s", "class": "%s", "columnName": "%s" });' . PHP_EOL,
				$this->getFieldPathResolver()->stripFieldPath($fieldNameAndPath, $dataType), // Suitable field name for the DataTable plugin.
				FrontendTca::grid($dataType)->isSortable($fieldNameAndPath) ? 'true' : 'false',
				FrontendTca::grid($dataType)->isVisible($fieldNameAndPath) ? 'true' : 'false',
				FrontendTca::grid($dataType)->getWidth($fieldNameAndPath),
				FrontendTca::grid($dataType)->getClass($fieldNameAndPath),
				$fieldNameAndPath
			);
		}

		return $output;
	}

	/**
	 * @return \TYPO3\CMS\Vidi\Resolver\FieldPathResolver
	 */
	protected function getFieldPathResolver () {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Resolver\FieldPathResolver');
	}
}
