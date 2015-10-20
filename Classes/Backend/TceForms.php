<?php
namespace Fab\VidiFrontend\Backend;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\Tca\Tca;

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
	public function feedItemsForSettingsDataTypes(&$parameters, $parentObject = NULL) {

		foreach ($GLOBALS['TCA'] as $dataType => $tca) {
			if (isset($GLOBALS['TCA'][$dataType]['grid_frontend'])) {
				$label = sprintf(
					'%s (%s)',
					Tca::table($dataType)->getTitle(),
					$dataType
				);
				$values = array($label, $dataType, NULL);

				$parameters['items'][] = $values;
			}
		}
	}

	/**
	 * This method modifies the list of items for FlexForm "template".
	 *
	 * @param array $parameters
	 * @param \TYPO3\CMS\Backend\Form\FormEngine $parentObject
	 */
	public function feedItemsForSettingsTemplateShow(&$parameters, $parentObject = NULL) {
		$configuration = $this->getPluginConfiguration();

		if (empty($configuration) || empty($configuration['settings']['templates'])) {
			$parameters['items'][] = array('No template found. Forgotten to load the static TS template?', '', NULL);
		} else {

			$configuredDataType = '';
			if (!empty($parameters['row']['pi_flexform'])) {
				$flexform = GeneralUtility::xml2array($parameters['row']['pi_flexform']);
				if (!empty($flexform['data']['sDEF']['lDEF']['settings.dataType'])) {
					$configuredDataType = $flexform['data']['sDEF']['lDEF']['settings.dataType']['vDEF'];
				}
			}

			$parameters['items'][] = ''; // Empty value
			foreach ($configuration['settings']['templates'] as $template) {
				$values = array($template['title'], $template['path'], NULL);
				if (empty($template['dataType']) || $template['dataType'] === $configuredDataType) {
					$parameters['items'][] = $values;
				}
			}
		}
	}

	/**
	 * This method modifies the list of items for FlexForm "template".
	 *
	 * @param array $parameters
	 * @param \TYPO3\CMS\Backend\Form\FormEngine $parentObject
	 */
	public function feedItemsForSettingsColumns(&$parameters, $parentObject = NULL) {
		$configuration = $this->getPluginConfiguration();

		if (empty($configuration) || empty($configuration['settings']['templates'])) {
			$parameters['items'][] = array('No template found. Forgotten to load the static TS template?', '', NULL);
		} else {

			$configuredDataType = '';
			if (!empty($parameters['row']['pi_flexform'])) {
				$flexform = GeneralUtility::xml2array($parameters['row']['pi_flexform']);
				if (!empty($flexform['data']['sDEF']['lDEF']['settings.dataType'])) {
					$configuredDataType = $flexform['data']['sDEF']['lDEF']['settings.dataType']['vDEF'];
				}
			}

			if (empty($configuredDataType)) {
				$parameters['items'][] = array('No content type has been saved yet!', '', NULL);
			} else {
				foreach(FrontendTca::grid($configuredDataType)->getFields() as $fieldNameAndPath => $configuration) {
					$values = array($fieldNameAndPath, $fieldNameAndPath, NULL);
					$parameters['items'][] = $values;
				}
			}

		}
	}

	/**
	 * Returns the TypoScript configuration for this extension.
	 *
	 * @return array
	 */
	protected function getPluginConfiguration() {
		$setup = $this->getConfigurationManager()->getTypoScriptSetup();

		$pluginConfiguration = array();
		if (is_array($setup['plugin.']['tx_vidifrontend.'])) {
			/** @var \TYPO3\CMS\Extbase\Service\TypoScriptService $typoScriptService */
			$typoScriptService = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Service\TypoScriptService');
			$pluginConfiguration = $typoScriptService->convertTypoScriptArrayToPlainArray($setup['plugin.']['tx_vidifrontend.']);
		}
		return $pluginConfiguration;
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager
	 */
	protected function getConfigurationManager() {
		$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
		return $objectManager->get('Tx_Extbase_Configuration_BackendConfigurationManager');
	}

}