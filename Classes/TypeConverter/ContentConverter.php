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
use TYPO3\CMS\Vidi\Domain\Repository\ContentRepositoryFactory;

/**
 * Convert a content identifier into a Content object.
 */
class ContentConverter extends AbstractTypeConverter {

	/**
	 * @var array<string>
	 */
	protected $sourceTypes = array('int');

	/**
	 * @var string
	 */
	protected $targetType = 'TYPO3\CMS\Vidi\Domain\Model\Content';

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

		$contentElementIdentifier = $configuration->getConfigurationValue('Fab\VidiFrontend\TypeConverter\ContentConverter', 'contentElement');
		$clause = sprintf('uid = %s ', $contentElementIdentifier);
		$clause .= $this->getPageRepository()->enableFields('tt_content');
		$clause .= $this->getPageRepository()->deleteClause('tt_content');
		$contentElement = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', 'tt_content', $clause);

		if (empty($contentElement)) {
			throw new \Exception('I could find Content Element with identifier: ' . $contentElementIdentifier, 1413992029);
		}

		$xml = GeneralUtility::xml2array($contentElement['pi_flexform']);

		if (!empty($xml['data']['sDEF']['lDEF']['settings.dataType']['vDEF'])) {
			$dataType = $xml['data']['sDEF']['lDEF']['settings.dataType']['vDEF'];
		} else {
			throw new \Exception('I could find data type in Content Element: ' . $contentElementIdentifier, 1413992029);
		}

		$contentRepository = ContentRepositoryFactory::getInstance($dataType);
		$content = $contentRepository->findByIdentifier((int)$source);
		return $content;
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

}