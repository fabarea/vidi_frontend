<?php
namespace Fab\VidiFrontend\Persistence;

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

use Fab\Vidi\Tca\Tca;
use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Factory class related to Order object.
 */
class OrderFactory implements SingletonInterface {

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Constructor
	 *
	 * @param array $settings
	 */
	public function __construct(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Gets a singleton instance of this class.
	 *
	 * @param $settings
	 * @return OrderFactory
	 */
	static public function getInstance($settings = array()) {
		return GeneralUtility::makeInstance('Fab\VidiFrontend\Persistence\OrderFactory', $settings);
	}

	/**
	 * Returns an order object.
	 *
	 * @param string $dataType
	 * @return \Fab\Vidi\Persistence\Order
	 */
	public function getOrder($dataType) {

		// Default ordering
		$order = Tca::table($dataType)->getDefaultOrderings();

		// Retrieve a possible id of the column from the request.
		$columnPosition = GeneralUtility::_GP('iSortCol_0');

		if ($columnPosition) {
			$columns = GeneralUtility::trimExplode(',', $this->settings['columns'], TRUE);

			if (isset($columns[$columnPosition])) {
				$fieldName = $columns[$columnPosition];
				if (FrontendTca::grid($dataType)->isSortable($fieldName)) {
					$direction = GeneralUtility::_GP('sSortDir_0');
					$order = array(
						$fieldName => strtoupper($direction)
					);
				}
			}
		} elseif (!empty($this->settings['sorting'])) {
			$direction = empty($this->settings['direction']) ? 'ASC' : $this->settings['direction'];
			$order = array(
				$this->settings['sorting'] => $direction
			);
		}

		return GeneralUtility::makeInstance('Fab\Vidi\Persistence\Order', $order);
	}

}
