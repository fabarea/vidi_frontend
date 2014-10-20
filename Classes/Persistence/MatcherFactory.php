<?php
namespace TYPO3\CMS\VidiFrontend\Persistence;

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
use TYPO3\CMS\Vidi\Persistence\Matcher;

/**
 * Factory class related to Matcher object.
 */
class MatcherFactory implements SingletonInterface {

	/**
	 * Gets a singleton instance of this class.
	 *
	 * @return \TYPO3\CMS\VidiFrontend\Persistence\MatcherFactory
	 */
	static public function getInstance() {
		return GeneralUtility::makeInstance('TYPO3\CMS\VidiFrontend\Persistence\MatcherFactory');
	}

	/**
	 * Returns a matcher object.
	 *
	 * @param array $matches
	 * @param string $dataType
	 * @return Matcher
	 */
	public function getMatcher($matches = array(), $dataType) {

		/** @var $matcher Matcher */
		$matcher = GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Persistence\Matcher', $matches, $dataType);

		// Trigger signal for post processing Matcher Object.
		$this->emitPostProcessMatcherObjectSignal($matcher);

		return $matcher;
	}

	/**
	 * Signal that is called for post-processing a matcher object.
	 *
	 * @param Matcher $matcher
	 * @signal
	 */
	protected function emitPostProcessMatcherObjectSignal(Matcher $matcher) {
		$this->getSignalSlotDispatcher()->dispatch('TYPO3\CMS\VidiFrontend\Persistence\MatcherFactory', 'postProcessMatcherObject', array($matcher, $matcher->getDataType()));
	}

	/**
	 * Get the SignalSlot dispatcher
	 *
	 * @return \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 */
	protected function getSignalSlotDispatcher() {
		return $this->getObjectManager()->get('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected function getObjectManager() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
	}

}
