<?php
namespace Fab\VidiFrontend\View\Grid;

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

use Fab\Vidi\Tca\FieldType;
use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\Domain\Model\Content;
use Fab\Vidi\Tca\Tca;
use Fab\Vidi\View\AbstractComponentView;

/**
 * View helper for rendering a row of a content object.
 */
class Row extends AbstractComponentView {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @var \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext
	 */
	protected $controllerContext;

	/**
	 * Registry for storing variable values and speed up the processing.
	 *
	 * @var array
	 */
	protected $variables = array();

	/**
	 * @param array $columns
	 */
	public function __construct($columns = array()){
		$this->columns = $columns;
	}

	/**
	 * Returns rows of content as array.
	 *
	 * @param Content $object
	 * @param int $rowIndex
	 * @return array
	 */
	public function render(Content $object = NULL, $rowIndex = 0) {

		// Initialize returned array
		$output = array();
		$dataType = $object->getDataType();

		foreach(FrontendTca::grid($dataType)->getFields() as $fieldNameAndPath => $configuration) {

			// Fetch value
			if (FrontendTca::grid($dataType)->hasRenderers($fieldNameAndPath)) {

				$value = '';
				$renderers = FrontendTca::grid($dataType)->getRenderers($fieldNameAndPath);

				// if is relation has one
				foreach ($renderers as $rendererClassName => $rendererConfiguration) {
					$rendererConfiguration['uriBuilder'] = $this->controllerContext->getUriBuilder();
					$rendererConfiguration['contentElement'] = $this->configurationManager->getContentObject();

					/** @var $rendererObject \Fab\Vidi\Grid\GridRendererInterface */
					$rendererObject = GeneralUtility::makeInstance($rendererClassName);
					$value .= $rendererObject
						->setObject($object)
						->setFieldName($fieldNameAndPath)
						->setRowIndex($rowIndex)
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

			// Get the first part of the field name.
			$fieldName = $this->getFieldPathResolver()->stripFieldName($fieldNameAndPath, $object->getDataType());
			$output[$fieldName] = $value;
		}

		$output['DT_RowId'] = 'row-' . $object->getUid();
		$output['DT_RowClass'] = sprintf('%s_%s', $object->getDataType(), $object->getUid());

		return $output;
	}

	/**
	 * Compute the value for the Content object according to a field name.
	 *
	 * @param \Fab\Vidi\Domain\Model\Content $object
	 * @param string $fieldNameAndPath
	 * @return string
	 */
	protected function resolveValue(Content $object, $fieldNameAndPath) {

		// Get the first part of the field name.
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
				$foreignTable = Tca::table($object->getDataType())->field($fieldName)->getForeignTable();
				$fieldNameOfForeignTable = Tca::table($foreignTable)->getLabelField();
			}

			$value = $object[$fieldName][$fieldNameOfForeignTable];
		}

		return $value;
	}

	/**
	 * Process the value
	 *
	 * @param string $value
	 * @param \Fab\Vidi\Domain\Model\Content $object
	 * @param string $fieldNameAndPath
	 * @return string
	 */
	protected function processValue($value, Content $object, $fieldNameAndPath) {

		// Set default value if $field name correspond to the label of the table
		$fieldName = $this->getFieldPathResolver()->stripFieldPath($fieldNameAndPath, $object->getDataType());
		if (Tca::table($object->getDataType())->getLabelField() === $fieldName && empty($value)) {
			#$value = sprintf('[%s]', $this->getLabelService()->sL('LLL:EXT:lang/locallang_core.xlf:labels.no_title', 1));
		}

		// Resolve the identifier in case of "select" or "radio button".
		$fieldType = Tca::table($object->getDataType())->field($fieldNameAndPath)->getType();
		if ($fieldType !== FieldType::TEXTAREA) {
			$value = htmlspecialchars($value);
//		} elseif ($fieldType === Tca::TEXTAREA && !$this->isClean($value)) {
//			$value = htmlspecialchars($value); // Avoid bad surprise, converts characters to HTML.
//		} elseif ($fieldType === Tca::TEXTAREA && !$this->hasHtml($value)) {
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

		/** @var \Fab\Vidi\Formatter\FormatterInterface $formatter */
		$formatter = GeneralUtility::makeInstance($className);
		$value = $formatter->format($value);

		return $value;
	}

	/**
	 * @return \Fab\Vidi\Resolver\FieldPathResolver
	 */
	protected function getFieldPathResolver() {
		return GeneralUtility::makeInstance('Fab\Vidi\Resolver\FieldPathResolver');
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return $this
	 */
	public function setConfigurationManager($configurationManager) {
		$this->configurationManager = $configurationManager;
		return $this;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $controllerContext
	 * @return $this
	 */
	public function setControllerContext($controllerContext) {
		$this->controllerContext = $controllerContext;
		return $this;
	}

	/**
	 * @return \Fab\Vidi\Resolver\ContentObjectResolver
	 */
	protected function getContentObjectResolver() {
		return GeneralUtility::makeInstance('Fab\Vidi\Resolver\ContentObjectResolver');
	}

}
