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
     * @param array $cells
     * @return string
     */
    protected function format(Content $object, array $cells)
    {

        $classNames = $cells['DT_RowId'] . ' ' . $cells['DT_RowClass'];
        unset($cells['DT_RowId'], $cells['DT_RowClass']);

        $uri = $this->getUri($object);

        $formattedRow = sprintf(
            '<tr id="row-%s" class="%s" %s><td>%s</td></tr>%s',
            $object->getUid(),
            $classNames,
            empty($uri) ? '' : 'data-uri="' . $uri . '"',
            implode('</td><td>', $cells),
            chr(10)
        );

        return $formattedRow;
    }

    /**
     * @param Content $object
     * @return string
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception\InvalidVariableException
     */
    protected function getUri(Content $object)
    {
        $uri = '';
        if ($this->hasClickOnRow()) {
            $contentElementIdentifier = $this->templateVariableContainer->get('contentElementIdentifier');

            $arguments = array(
                PluginParameter::PREFIX => array(
                    'content' => $object->getUid(),
                    'action' => 'show',
                    'contentElement' => $contentElementIdentifier,
                    'controller' => 'Content',
                ),
            );

            $this->getUriBuilder()->setArguments($arguments);
            $uri = $this->getUriBuilder()->build();
        }

        return $uri;
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
