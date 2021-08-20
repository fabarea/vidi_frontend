<?php
namespace Fab\VidiFrontend\ViewHelpers\Grid;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Configuration\ColumnsConfiguration;
use Fab\VidiFrontend\View\Grid\Row;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Fab\Vidi\Domain\Model\Content;

/**
 * View helper for rendering multiple rows.
 */
class RowViewHelper extends AbstractViewHelper
{

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('object', Content::class, '', true);
        $this->registerArgument('index', 'int', '', false, 0);
    }

    /**
     * Returns rows of content as array.
     *
     * @return string
     */
    public function render(): string
    {

        $object = $this->arguments['object'];
        $index = $this->arguments['index'];

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

    public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

}
