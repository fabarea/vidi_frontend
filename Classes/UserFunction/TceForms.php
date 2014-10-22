<?php
namespace Fab\VidiFrontend\UserFunction;

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
use TYPO3\CMS\Vidi\Tca\TcaService;

/**
 * A class to interact with TCEForms
 */
class TceForms {

	/**
	 * This method modifies the list of items for FlexForm "dataType".
	 *
	 * @param array $parameters
	 * @param \TYPO3\CMS\Backend\Form\FormEngine $parentObject
	 */
	public function fetchDataTypes(&$parameters, $parentObject = NULL) {

		foreach ($GLOBALS['TCA'] as $dataType => $tca) {
			if (isset($GLOBALS['TCA'][$dataType]['grid_frontend'])) {
				$label = sprintf(
					'%s (%s)',
					TcaService::table($dataType)->getTitle(),
					$dataType
				);
				$values = array($label, $dataType, NULL);

				$parameters['items'][] = $values;
			}
		}
	}
}