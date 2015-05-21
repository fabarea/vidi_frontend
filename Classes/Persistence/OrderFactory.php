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

use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Factory class related to Order object.
 */
class OrderFactory implements SingletonInterface {

	/**
	 * Gets a singleton instance of this class.
	 *
	 * @return \Fab\VidiFrontend\Persistence\OrderFactory
	 */
	static public function getInstance() {
		return GeneralUtility::makeInstance('Fab\VidiFrontend\Persistence\OrderFactory');
	}

	/**
	 * Returns an order object.
	 *
	 * @param string $dataType
	 * @return \Fab\Vidi\Persistence\Order
	 */
	public function getOrder($dataType) {

		// Default ordering
		#$order = Tca::table($dataType)->getDefaultOrderings();

		// Retrieve a possible id of the column from the request
		$columnPosition = GeneralUtility::_GP('iSortCol_0');
		$field = FrontendTca::grid($dataType)->getFieldNameByPosition($columnPosition);

		$direction = GeneralUtility::_GP('sSortDir_0');
		$order = array(
			$field => strtoupper($direction)
		);

		return GeneralUtility::makeInstance('Fab\Vidi\Persistence\Order', $order);
	}

}
