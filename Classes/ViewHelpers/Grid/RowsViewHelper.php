<?php
namespace Fab\VidiFrontend\ViewHelpers\Grid;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Configuration\ContentElementConfiguration;
use Fab\VidiFrontend\View\Grid\Row;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper for rendering multiple rows.
 */
class RowsViewHelper extends AbstractViewHelper
{

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext
     */
    protected $controllerContext;

    /**
     * Returns rows of content as array.
     *
     * @param array $objects
     * @return array
     */
    public function render(array $objects = [])
    {
        $rows = [];

        $columns = ContentElementConfiguration::getInstance()->getColumns();

        /** @var Row $row */
        $row = GeneralUtility::makeInstance(Row::class, $columns);
        $row->setConfigurationManager($this->configurationManager)
            ->setControllerContext($this->controllerContext);

        foreach ($objects as $index => $object) {
            $rows[] = $row->render($object, $index);
        }

        return $rows;
    }

    /**
     * @param mixed $controllerContext
     * @return $this
     */
    public function setControllerContext($controllerContext)
    {
        $this->controllerContext = $controllerContext;
        return $this;
    }
}
