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

use Fab\VidiFrontend\Configuration\ColumnsConfiguration;
use Fab\VidiFrontend\Plugin\PluginParameter;
use Fab\VidiFrontend\View\Grid\Row;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use Fab\Vidi\Domain\Model\Content;

/**
 * View helper for rendering multiple rows.
 */
class RowViewHelper extends AbstractViewHelper
{

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * Returns rows of content as array.
     *
     * @param Content $object
     * @param int $index
     * @return array
     */
    public function render(Content $object, $index = 0)
    {
        $settings = $this->templateVariableContainer->get('settings');

        // Initialize returned array
        $dataType = $object->getDataType();
        $columnList = $settings['columns'];

        $columns = ColumnsConfiguration::getInstance()->get($dataType, $columnList);

        /** @var Row $row */
        $row = GeneralUtility::makeInstance(Row::class, $columns);
        $row->setConfigurationManager($this->configurationManager)
            ->setControllerContext($this->controllerContext);

        $renderedRow = $row->render($object, $index);
        $formattedRow = $this->format($object, $renderedRow);
        return $formattedRow;
    }

    /**
     * @param Content $object
     * @param array $row
     * @return string
     */
    protected function format(Content $object, array $row)
    {

        $classNames = $row['DT_RowId'] . ' ' . $row['DT_RowClass'];
        $uri = $row['DT_uri'];
        unset($row['DT_RowId'], $row['DT_RowClass'], $row['DT_uri']);

        $formattedRow = sprintf(
            '<tr id="row-%s" class="%s" data-uri="%s"><td>%s</td></tr>%s',
            $object->getUid(),
            $classNames,
            $uri,
            implode('</td><td>', $row),
            chr(10)
        );

        return $formattedRow;
    }

    /**
     * @return bool
     */
    protected function hasClickOnRow()
    {
        $settings = $this->templateVariableContainer->get('settings');
        return (bool)$settings['hasClickOnRow'] && !empty($settings['templateDetail']);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
     */
    protected function getUriBuilder()
    {
        return $this->controllerContext->getUriBuilder();
    }

}
