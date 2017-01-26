<?php
namespace Fab\VidiFrontend\ViewHelpers\Grid\Column;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Configuration\ColumnsConfiguration;
use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper for rendering configuration that will be consumed by Javascript
 */
class ConfigurationViewHelper extends AbstractViewHelper
{

    /**
     * Render the columns of the grid.
     *
     * @return string
     */
    public function render()
    {
        $output = '';
        $dataType = $this->templateVariableContainer->get('dataType');
        $settings = $this->templateVariableContainer->get('settings');

        foreach (ColumnsConfiguration::getInstance()->get($dataType, $settings['columns']) as $fieldNameAndPath => $configuration) {

            // mData vs columnName
            // -------------------
            // mData: internal name of DataTable plugin and can not contains a path, e.g. metadata.title
            // columnName: whole field name with path
            $output .= sprintf('columns.push({ "data": "%s", "sortable": %s, "visible": true, "width": "%s", "class": "%s", "columnName": "%s" });' . PHP_EOL,
                $this->getFieldPathResolver()->stripFieldPath($fieldNameAndPath, $dataType), // Suitable field name for the DataTable plugin.
                FrontendTca::grid($dataType)->isSortable($fieldNameAndPath) ? 'true' : 'false',
                FrontendTca::grid($dataType)->getWidth($fieldNameAndPath),
                FrontendTca::grid($dataType)->getClass($fieldNameAndPath),
                $fieldNameAndPath
            );
        }

        return $output;
    }

    /**
     * @return \Fab\Vidi\Resolver\FieldPathResolver
     */
    protected function getFieldPathResolver()
    {
        return GeneralUtility::makeInstance('Fab\Vidi\Resolver\FieldPathResolver');
    }
}
