<?php

namespace Fab\VidiFrontend\ViewHelpers\Column;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Resolver\FieldPathResolver;
use Fab\Vidi\Tca\FieldType;
use Fab\Vidi\Tca\Tca;
use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use Fab\Vidi\Domain\Model\Content;

/**
 * Class ContentViewHelper
 */
class ContentViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('column', 'string', 'Column name', false);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        /** @var Content $object */
        $object = $this->templateVariableContainer->get('object');

        $fieldNameAndPath = $this->templateVariableContainer->exists('column')
            ? $this->templateVariableContainer->get('column')
            : $this->arguments['column'];

        // Fetch value
        if (FrontendTca::grid($object)->hasRenderers($fieldNameAndPath)) {

            $value = '';
            $renderers = FrontendTca::grid($object)->getRenderers($fieldNameAndPath);

            // if is relation has one
            foreach ($renderers as $rendererClassName => $rendererConfiguration) {

                /** @var $rendererObject \Fab\Vidi\Grid\ColumnRendererInterface */
                $rendererObject = GeneralUtility::makeInstance($rendererClassName);
                $value .= $rendererObject
                    ->setObject($object)
                    ->setFieldName($fieldNameAndPath)
                    #->setRowIndex(0)
                    #->setFieldConfiguration([)
                    ->setGridRendererConfiguration($rendererConfiguration)
                    ->render();
            }
        } else {
            $value = $this->resolveValue($object, $fieldNameAndPath);

            if (is_array($value)) {

                $values = [];
                foreach ($value as $childObject) {
                    $labelField = Tca::table($childObject)->getLabelField();
                    $values[] = $childObject[$labelField];
                }

                $value = implode(', ', $values);
            } else {
                $value = $this->processValue($value, $object, $fieldNameAndPath); // post resolve processing.
                // Possible formatting given by configuration. @see TCA['grid']
                #$value = $this->formatValue($value, $configuration);
            }
        }


        return $value;
    }

    /**
     * Compute the value for the Content object according to a field name.
     *
     * @param \Fab\Vidi\Domain\Model\Content $object
     * @param string $fieldNameAndPath
     * @return string
     */
    protected function resolveValue(Content $object, $fieldNameAndPath)
    {

        // Get the first part of the field name.
        $fieldName = $this->getFieldPathResolver()->stripFieldName($fieldNameAndPath, $object->getDataType());

        $value = $object[$fieldName];

        // Relation but contains no data.
        if (is_array($value) && empty($value)) {
            $value = '';
        } elseif ($value instanceof Content) {

            $fieldNameOfForeignTable = $this->getFieldPathResolver()->stripFieldPath($fieldNameAndPath, $object->getDataType());

            // true means the field name does not contains a path. "title" vs "metadata.title"
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
    protected function processValue($value, Content $object, $fieldNameAndPath)
    {

        // Set default value if $field name correspond to the label of the table
        $fieldName = $this->getFieldPathResolver()->stripFieldPath($fieldNameAndPath, $object->getDataType());

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
     * @return FieldPathResolver|Object
     */
    protected function getFieldPathResolver()
    {
        return GeneralUtility::makeInstance(FieldPathResolver::class);
    }

}
