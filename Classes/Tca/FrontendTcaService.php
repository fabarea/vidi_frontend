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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Vidi\Domain\Model\Content;
use TYPO3\CMS\Vidi\Exception\NotExistingClassException;
use TYPO3\CMS\Vidi\Tca\TcaServiceInterface;

/**
 * A class to handle TCA ctrl.
 */
class FrontendTcaService implements SingletonInterface, TcaServiceInterface {

	/**
	 * Fields that are considered as system.
	 *
	 * @var array
	 */
	static protected $systemFields = array(
		'uid',
		'pid',
		'tstamp',
		'crdate',
		'deleted',
		'hidden',
		'starttime',
		'endtime',
		'sys_language_uid',
		'l18n_parent',
		'l18n_diffsource',
		't3ver_oid',
		't3ver_id',
		't3ver_wsid',
		't3ver_label',
		't3ver_state',
		't3ver_stage',
		't3ver_count',
		't3ver_tstamp',
		't3_origuid',
	);

	/**
	 * @var array
	 */
	static protected $instances;

	/**
	 * Returns a class instance of a corresponding TCA service.
	 * If the class instance does not exist, create one.
	 *
	 * @throws NotExistingClassException
	 * @param string $tableName
	 * @param string $serviceType of the TCA. Typical values are: grid
	 * @return TcaServiceInterface
	 */
	static public function getService($tableName, $serviceType) {

		if (empty(self::$instances[$tableName][$serviceType])) {
			$className = sprintf('Fab\VidiFrontend\Tca\Frontend%sService', ucfirst($serviceType));

			if (!class_exists($className)) {
				throw new NotExistingClassException('Class does not exit: ' . $className, 1357060937);

			}
			$instance = GeneralUtility::makeInstance($className, $tableName, $serviceType);
			self::$instances[$tableName][$serviceType] = $instance;
		}
		return self::$instances[$tableName][$serviceType];
	}

	/**
	 * Returns a "grid" service instance.
	 *
	 * @param string|Content $tableNameOrContentObject
	 * @return \TYPO3\CMS\Vidi\Tca\GridService
	 */
	static public function grid($tableNameOrContentObject) {
		$tableName = $tableNameOrContentObject instanceof Content ? $tableNameOrContentObject->getDataType() : $tableNameOrContentObject;
		return self::getService($tableName, self::TYPE_GRID);
	}

}
