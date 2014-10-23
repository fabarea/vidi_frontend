<?php
namespace Fab\VidiFrontend\Tca;

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

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Vidi\Exception\InvalidKeyInArrayException;
use TYPO3\CMS\Vidi\Tca\GridService;
use TYPO3\CMS\Vidi\Tca\TcaService;

/**
 * A class to handle TCA grid configuration
 */
class FrontendGridService extends GridService {


	/**
	 * __construct
	 *
	 * @throws InvalidKeyInArrayException
	 * @param string $tableName
	 * @return \Fab\VidiFrontend\Tca\FrontendGridService
	 */
	public function __construct($tableName) {

		$this->tableName = $tableName;

		if (empty($GLOBALS['TCA'][$this->tableName])) {
			throw new InvalidKeyInArrayException('No TCA existence for table name: ' . $this->tableName, 1413965764);
		}

		$this->tca = $GLOBALS['TCA'][$this->tableName]['grid_frontend'];
	}

	/**
	 * Get the translation of a label given a column name.
	 *
	 * @param string $fieldNameAndPath
	 * @return string
	 */
	public function getLabel($fieldNameAndPath) {
		if ($this->hasLabel($fieldNameAndPath)) {
			$field = $this->getField($fieldNameAndPath);
			$label = LocalizationUtility::translate($field['label'], '');
			if (is_null($label)) {
				$label = $field['label'];
			}
		} else {
			// Fetch the label from the Grid service provided by "vidi". He may know more about labels.
			$label = TcaService::grid($this->tableName)->getLabel($fieldNameAndPath);
		}
		return $label;
	}


	/**
	 * Returns the "sortable" value of the column.
	 *
	 * @param string $fieldName
	 * @return int|string
	 */
	public function isSortable($fieldName) {
		// Fetch Frontend configuration first and check if a value is defined there.
		$field = $this->getField($fieldName);

		if (isset($field['sortable'])) {
			$isSortable = $field['sortable'];
		} else {
			$isSortable = TcaService::grid($this->tableName)->isSortable($fieldName);
		}
		return $isSortable;
	}
}
