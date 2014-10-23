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
use TYPO3\CMS\Core\Utility\GeneralUtility;
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

	/**
	 * This method modifies the list of items for FlexForm "template".
	 *
	 * @param array $parameters
	 * @param \TYPO3\CMS\Backend\Form\FormEngine $parentObject
	 */
	public function fetchTemplatesForActionShow(&$parameters, $parentObject = NULL) {
		$configuration = $this->getPluginConfiguration();
		if (empty($configuration) || empty($configuration['settings']['templates'])) {
			$parameters['items'][] = array('No template found. Forgotten to load the static TS template?', '', NULL);
		} else {

			foreach ($configuration['settings']['templates'] as $template) {
				$values = array($template['title'], $template['path'], NULL);
				$parameters['items'][] = $values;
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
		$extensionName = 'vidifrontend';

		$pluginConfiguration = array();
		if (is_array($setup['plugin.']['tx_' . strtolower($extensionName) . '.'])) {
			/** @var \TYPO3\CMS\Extbase\Service\TypoScriptService $typoScriptService */
			$typoScriptService = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Service\TypoScriptService');
			$pluginConfiguration = $typoScriptService->convertTypoScriptArrayToPlainArray($setup['plugin.']['tx_' . strtolower($extensionName) . '.']);
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