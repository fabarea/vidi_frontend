<?php
namespace Fab\VidiFrontend\Configuration;

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
use Fab\Vidi\Tca\TcaServiceInterface;

/**
 * Tweak TCA configuration for the Frontend for File References.
 */
class TcaTweak {

	/**
	 * Tweak the TCA for sys_file_reference on the Frontend.
	 * Make that sys_file_reference behaves as a MM relations between "sys_file" and "tx_domain_model_foo"
	 *
	 * @param string $dataType
	 * @param string $serviceType
	 * @return void
	 */
	public function tweakFileReferences($dataType, $serviceType) {

		if ($this->isFrontendMode() && $serviceType === TcaServiceInterface::TYPE_TABLE) {
			foreach ($this->getFields($dataType) as $fieldName) {
				if ($this->getForeignTable($dataType, $fieldName) === 'sys_file_reference') {

					// Adjust TCA so that sys_file_reference behaves as MM tables of type "group" on the Frontend
					// Consequence: we'll get directly a file and not a File Reference.
					unset($GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['foreign_field']);
					unset($GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['foreign_label']);
					$GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['foreign_table'] = 'sys_file';
					$GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['MM'] = 'sys_file_reference';
					$GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['MM_opposite_field'] = 'items';
					$GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['MM_match_fields'] = array(
						'tablenames' => $dataType,
						'fieldname' => $fieldName,
					);

					// Just a faked TCA to handle the opposite relation of sys_file_reference.
					// It is required by Vidi to have relations configured both side.
					if (empty($GLOBALS['TCA']['sys_file']['columns']['items'])) {
						$GLOBALS['TCA']['sys_file']['columns']['items']['config'] = array(
							'allowed' => '*',
							'internal_type' => 'db',
							'MM' => 'sys_file_reference',
							'type' => 'group',
						);
					}
				}
			}
		}
	}

	/**
	 * Returns whether the current mode is Frontend
	 *
	 * @param string $dataType
	 * @return array
	 */
	protected function getFields($dataType) {
		return array_keys($GLOBALS['TCA'][$dataType]['columns']);
	}

	/**
	 * Returns whether the current mode is Frontend
	 *
	 * @param string $dataType
	 * @param string $fieldName
	 * @return string
	 */
	protected function getForeignTable($dataType, $fieldName) {
		$foreignTableName = '';
		if (isset($GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['foreign_table'])) {
			$foreignTableName = $GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['foreign_table'];
		}
		return $foreignTableName;
	}

	/**
	 * Returns whether the current mode is Frontend
	 *
	 * @return bool
	 */
	protected function isFrontendMode() {
		return TYPO3_MODE == 'FE';
	}

}
