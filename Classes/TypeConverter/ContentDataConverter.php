<?php
namespace Fab\VidiFrontend\TypeConverter;

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

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;
use Fab\Vidi\Domain\Repository\ContentRepositoryFactory;

/**
 * Convert a content identifier to an data array.
 */
class ContentDataConverter extends AbstractTypeConverter {

	/**
	 * @var array<string>
	 */
	protected $sourceTypes = array('int');

	/**
	 * @var string
	 */
	protected $targetType = 'array';

	/**
	 * @var integer
	 */
	protected $priority = 1;

	/**
	 * Actually convert from $source to $targetType
	 *
	 * @param string $source
	 * @param string $targetType
	 * @param array $convertedChildProperties
	 * @param PropertyMappingConfigurationInterface $configuration
	 * @throws \Exception
	 * @throws \TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException
	 * @return File
	 * @api
	 */
	public function convertFrom($source, $targetType, array $convertedChildProperties = array(), PropertyMappingConfigurationInterface $configuration = NULL) {

		$tableName = 'tt_content';
		$clause = 'uid = ' . $source;
		$clause .= ' AND pid = ' . $this->getFrontendObject()->id; // Make sure we are on the same pid for security reason
		$clause .= $this->getPageRepository()->enableFields($tableName);
		$clause .= $this->getPageRepository()->deleteClause($tableName);
		$record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $tableName, $clause);

		if (empty($record)) {
			throw new \RuntimeException('Vidi Frontend: I could not access this resource', 1445352723);
		}

		return $record;
	}

	/**
	 * Returns a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Returns an instance of the page repository.
	 *
	 * @return \TYPO3\CMS\Frontend\Page\PageRepository
	 */
	protected function getPageRepository() {
		return $GLOBALS['TSFE']->sys_page;
	}

	/**
	 * Returns an instance of the Frontend object.
	 *
	 * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 */
	protected function getFrontendObject() {
		return $GLOBALS['TSFE'];
	}
}