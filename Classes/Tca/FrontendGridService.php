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

use TYPO3\CMS\Vidi\Exception\InvalidKeyInArrayException;
use TYPO3\CMS\Vidi\Tca\GridService;

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

}
