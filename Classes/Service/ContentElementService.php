<?php
namespace Fab\VidiFrontend\Service;

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


/**
 * Service related to the Content Element (tt_content.
 */
class ContentElementService {

	/**
	 * @var string
	 */
	protected $dataType;

	/**
	 * Constructor
	 *
	 * @param string $dataType
	 * @return \Fab\VidiFrontend\Service\ContentElementService
	 */
	public function __construct($dataType) {
		$this->dataType = $dataType;
	}

	/**
	 * Fetch the Content Element data.
	 *
	 * @param int $contentIdentifier
	 * @return array
	 */
	public function fetchContentData($contentIdentifier) {
		$tableName = 'tt_content';
		$clause = 'uid = ' . $contentIdentifier;
		$clause .= $this->getPageRepository()->enableFields($tableName);
		$clause .= $this->getPageRepository()->deleteClause($tableName);
		return $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $tableName, $clause);
	}

	/**
	 * Return a Content Element object.
	 *
	 * @param int $contentIdentifier
	 * @return \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	public function getContentObjectRender($contentIdentifier) {

		$contentElementData = $this->fetchContentData($contentIdentifier);

		/** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObjectRenderer */
		$contentObjectRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
		$contentObjectRenderer->start($contentElementData, $this->dataType);
		return $contentObjectRenderer;
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
