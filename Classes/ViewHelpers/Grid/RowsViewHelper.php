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

use Fab\VidiFrontend\Configuration\ContentElementConfiguration;
use Fab\VidiFrontend\View\Grid\Row;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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
     * @return string
     */
    public function render(array $objects = array())
    {
        $rows = array();

        $columns = ContentElementConfiguration::getInstance()->getColumns();

        /** @var Row $row */
        $row = GeneralUtility::makeInstance('Fab\VidiFrontend\View\Grid\Row', $columns);
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
