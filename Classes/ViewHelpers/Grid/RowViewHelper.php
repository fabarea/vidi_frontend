<?php
namespace Fab\VidiFrontend\ViewHelpers\Grid;

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

use Fab\VidiFrontend\Tca\FrontendTcaService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Vidi\Domain\Model\Content;
use TYPO3\CMS\Vidi\Tca\TcaService;

/**
 * View helper for rendering multiple rows.
 */
class RowViewHelper extends AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * Registry for storing variable values and speed up the processing.
	 *
	 * @var array
	 */
	protected $variables = array();

	/**
	 * Returns rows of content as array.
	 *
	 * @param Content $object
	 * @return string
	 */
	public function render(Content $object) {
		$fieldNameAndPath = $this->templateVariableContainer->get('columnName');
		$configuration = $this->templateVariableContainer->get('configuration');
		$dataType = $object->getDataType();

		// Fetch value
		if (FrontendTcaService::grid($dataType)->hasRenderers($fieldNameAndPath)) {

			$value = '';
			$renderers = FrontendTcaService::grid($dataType)->getRenderers($fieldNameAndPath);

			// if is relation has one
			foreach ($renderers as $rendererClassName => $rendererConfiguration) {

				$rendererConfiguration['uriBuilder'] = $this->controllerContext->getUriBuilder();
				$rendererConfiguration['contentElement'] = $this->configurationManager->getContentObject();

				/** @var $rendererObject \TYPO3\CMS\Vidi\Grid\GridRendererInterface */
				$rendererObject = GeneralUtility::makeInstance($rendererClassName);
				$value .= $rendererObject
					->setObject($object)
					->setFieldName($fieldNameAndPath)
					#->setRowIndex($rowIndex)
					->setFieldConfiguration($configuration)
					->setGridRendererConfiguration($rendererConfiguration)
					->render();
			}
		} else {
			$value = $this->resolveValue($object, $fieldNameAndPath);
			$value = $this->processValue($value, $object, $fieldNameAndPath); // post resolve processing.
		}

		// Possible formatting given by configuration. @see TCA['grid']
		$value = $this->formatValue($value, $configuration);

		// Final wrap given by configuration. @see TCA['grid']
		#$value = $this->wrapValue($value, $configuration);

		return $value;
	}

	/**
	 * Compute the value for the Content object according to a field name.
	 *
	 * @param \TYPO3\CMS\Vidi\Domain\Model\Content $object
	 * @param string $fieldNameAndPath
	 * @return string
	 */
	protected function resolveValue(Content $object, $fieldNameAndPath) {

		// Get the first part of the field name and
		$fieldName = $this->getFieldPathResolver()->stripFieldName($fieldNameAndPath, $object->getDataType());

		$value = $object[$fieldName];

		// Relation but contains no data.
		if (is_array($value) && empty($value)) {
			$value = '';
		} elseif ($value instanceof Content) {

			$fieldNameOfForeignTable = $this->getFieldPathResolver()->stripFieldPath($fieldNameAndPath, $object->getDataType());

			// TRUE means the field name does not contains a path. "title" vs "metadata.title"
			// Fetch the default label
			if ($fieldNameOfForeignTable === $fieldName) {
				$foreignTable = TcaService::table($object->getDataType())->field($fieldName)->getForeignTable();
				$fieldNameOfForeignTable = TcaService::table($foreignTable)->getLabelField();
			}

			$value = $object[$fieldName][$fieldNameOfForeignTable];
		}

		return $value;
	}

	/**
	 * Process the value
	 *
	 * @param string $value
	 * @param \TYPO3\CMS\Vidi\Domain\Model\Content $object
	 * @param string $fieldNameAndPath
	 * @return string
	 */
	protected function processValue($value, Content $object, $fieldNameAndPath) {

		// Set default value if $field name correspond to the label of the table
		$fieldName = $this->getFieldPathResolver()->stripFieldPath($fieldNameAndPath, $object->getDataType());
		if (TcaService::table($object->getDataType())->getLabelField() === $fieldName && empty($value)) {
			#$value = sprintf('[%s]', $this->getLabelService()->sL('LLL:EXT:lang/locallang_core.xlf:labels.no_title', 1));
		}

		// Resolve the identifier in case of "select" or "radio button".
		$fieldType = TcaService::table($object->getDataType())->field($fieldNameAndPath)->getType();
		if ($fieldType !== TcaService::TEXTAREA) {
			$value = htmlspecialchars($value);
//		} elseif ($fieldType === TcaService::TEXTAREA && !$this->isClean($value)) {
//			$value = htmlspecialchars($value); // Avoid bad surprise, converts characters to HTML.
//		} elseif ($fieldType === TcaService::TEXTAREA && !$this->hasHtml($value)) {
//			$value = nl2br($value);
		}

		return $value;
	}

	/**
	 * Possible value formatting.
	 *
	 * @param string $value
	 * @param array $configuration
	 * @return string
	 */
	protected function formatValue($value, array $configuration) {
		if (empty($configuration['format'])) {
			return $value;
		}
		$className = $configuration['format'];

		/** @var \TYPO3\CMS\Vidi\Formatter\FormatterInterface $formatter */
		$formatter = GeneralUtility::makeInstance($className);
		$value = $formatter->format($value);

		return $value;
	}

	/**
	 * @return \TYPO3\CMS\Vidi\Resolver\FieldPathResolver
	 */
	protected function getFieldPathResolver() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Resolver\FieldPathResolver');
	}

	/**
	 * @return \TYPO3\CMS\Vidi\Resolver\ContentObjectResolver
	 */
	protected function getContentObjectResolver() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Resolver\ContentObjectResolver');
	}

}
